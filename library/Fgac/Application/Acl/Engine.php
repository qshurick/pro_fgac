<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 27.02.14
 * Time: 2:15
 */

class Fgac_Application_Acl_Engine {

    const REGISTRY_ALIAS = "fgac-acl";
    const SESSION_ALIAS = "fgac-acl";

    static protected $_instance;
    static protected $_options;
    static public function setup($options) {
        self::$_options = $options;
    }
    static public function getInstance() {
        if (null === self::$_instance) {
            $session = new Zend_Session_Namespace(self::SESSION_ALIAS);
            if (null !== $session->fgac) {
                self::$_instance = unserialize($session->fgac);
            } else {
                self::$_instance = new self(self::$_options);
                $session->fgac = serialize(self::$_instance);
            }
        }
        return self::$_instance;
    }

    protected function __construct($options) {
        $this->setOptions($options);
    }

    protected $_rules = array();
    protected $_tables = array();
    protected $_aclAlias;

    public function setAclAlias($alias) {
        $this->_aclAlias = $alias;
    }

    public function getAclAlias() {
        return $this->_aclAlias;
    }

    /**
     * @return Zend_Log
     */
    protected function getLogger() {
        return Zend_Registry::get('logger')->ensureStream('fgac');
    }

    protected  function setOptions($options) {
        if ("yes" !== $options['enabled']) return;

        $this->getLogger()->log("FGAC Plugin initialization", Zend_Log::DEBUG);

        $this->setAclAlias($options['acl-alias']);

        // loading data from application.ini
        if (null !== $options['rule'] && !empty($options['rule'])) {
            foreach($options['rule'] as $rule => $ruleOptions) {
                $this->getLogger()->log("Rule (config):: \"$rule\"", Zend_Log::DEBUG);
                $this->addRule($ruleOptions['plugin'], array(
                    'tables' => explode(",", $ruleOptions['tables']),
                    'roles' => explode(",", $ruleOptions['roles']),
                ));
            }
        }
        // loading data from DB storage
        $table = new Fgac_Application_Db_FgacAcl();
        $rules = $table->getFull();
        if ($rules->count() > 0) {
            foreach($rules as $rule) {
                $this->getLogger()->log("Rule (db):: \"" . $rule->name . "\"", Zend_Log::DEBUG);
                $this->addRule($rule->plugin, array(
                    'tables' => $rule->table_name,
                    'roles' => $rule->code,
                ));
            }
        }
    }

    public function addRule($rule, $options) {
        $table = $options['tables'];
        $roles = $options['roles'];
        if (null === $this->_rules[$rule]) {
            $this->_rules[$rule] = new Fgac_Application_Acl_Rule($rule, $table, $roles);
        } else {
            /**
             * @var $ruleObject Fgac_Application_Acl_Rule
             */
            $ruleObject = $this->_rules[$rule];
            $ruleObject->addRoles($roles);
            $ruleObject->addTables($table);
        }
        if (null === $this->_tables[$table]) {
            $this->_tables[$table] = array($rule);
        } elseif (!in_array($rule, $this->_tables[$table])) {
            $this->_tables[$table][] = $rule;
        }
        $this->fixate();
    }

    public function hasRule($table, $roles = array()) {
        if (array_key_exists($table, $this->_tables)) {
            if (empty($roles))
                return true;
            foreach ($this->_tables[$table] as $rule) {
                /**
                 * @var $ruleObject Fgac_Application_Acl_Rule
                 */
                $ruleObject = $this->_rules[$rule];
                if ($ruleObject->assert($table, $roles)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function invoke($table, $roles, Pro_Db_Select &$select) {
        if (array_key_exists($table, $this->_tables)) {
            foreach ($this->_tables[$table] as $rule) {
                /**
                 * @var $ruleObject Fgac_Application_Acl_Rule
                 */
                $ruleObject = $this->_rules[$rule];
                if ($ruleObject->assert($table, $roles)) {
                    $ruleObject->invoke($select);
                }
            }
        }
    }

    protected function fixate() {
        $session = new Zend_Session_Namespace(self::SESSION_ALIAS);
        $session->fgac = serialize(self::$_instance);
    }

}