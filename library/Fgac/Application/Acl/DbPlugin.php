<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 27.02.14
 * Time: 2:15
 */

class Fgac_Application_Acl_DbPlugin extends Pro_Db_Plugin_PluginAbstract {

    const REGISTRY_ALIAS = "fgac-acl";
    const SESSION_ALIAS = "fgac-acl";

    static protected $_instance;
    static public function getInstance() {
        if (null === self::$_instance) {
            $session = new Zend_Session_Namespace(self::SESSION_ALIAS);
            if (null !== $session->fgac) {
                self::$_instance = unserialize($session->fgac);
            } else {
                self::$_instance = new self();
                $session->fgac = serialize(self::$_instance);
            }
        }
        return self::$_instance;
    }

    protected $_rules = array();
    protected $_tables = array();
    protected $_aclAlias;

    public function setAclAlias($alias) {
        $this->_aclAlias = $alias;
    }

    public function addRule($rule, $options) {
        $table = $options['table_name'];
        $roles = $options['code'];
        if (null === $this->_rules[$rule]) {
            $this->_rules[$rule] = new Fgac_Application_Acl_Rule($rule, $table, $roles);
        } else {
            $this->_rules[$rule]->addRoles($roles);
        }
        if (null === $this->_tables[$table]) {
            $this->_tables[$table] = array($rule);
        } elseif (!in_array($rule, $this->_tables[$table])) {
            $this->_tables[$table][] = $rule;
        }
        $this->fixate();
    }

    public function hasRule($table, $roles = array()) {
        if (in_array($table, $this->_tables)) {
            if (empty($roles))
                return true;
            foreach ($this->_tables[$table] as $rule) {
                if ($this->_rules[$rule]->assert($table, $roles)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function invoke($table, $roles, Pro_Db_Select &$select) {
        if (in_array($table, $this->_tables)) {
            foreach ($this->_tables[$table] as $rule) {
                if ($this->_rules[$rule]->assert($table, $roles)) {
                    $this->_rules[$rule]->invoke($select);
                }
            }
        }
    }

    protected function fixate() {
        $session = new Zend_Session_Namespace(self::SESSION_ALIAS);
        $session->fgac = serialize(self::$_instance);
    }

    public function beforeFetch($tableName, Pro_Db_Select &$select) {
        $acl = Zend_Registry::get($this->_aclAlias);
        $this->invoke($tableName, $acl->getCurrentHierarchy(), $select);
    }

}