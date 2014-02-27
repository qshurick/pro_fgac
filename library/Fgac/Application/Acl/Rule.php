<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 27.02.14
 * Time: 14:56
 */

class Fgac_Application_Acl_Rule {
    const ANY_ROLE = "*";

    protected $_rule;
    protected $_tables = array();
    protected $_roles = array();

    public function __construct($rule, $tables, $roles = array()) {
        $this->_rule = $rule;
        $this->addTables($tables);
        $this->addRoles($roles);
    }

    public function addTables($tables) {
        if (is_array($tables)) {
            foreach($tables as $table) {
                $this->addTable($table);
            }
        } else {
            $this->addTable($tables);
        }
    }

    public function addTable($table) {
        if (null !== $table && !in_array($table, $this->_tables))
            $this->_tables[] = $table;
    }

    public function addRoles($roles) {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                $this->addRole($role);
            }
        } else {
            $this->addRole($roles);
        }
    }

    public function addRole($role) {
        if (null !== $role && !in_array($role, $this->_roles)) {
            $this->_roles[] = $role;
        }
    }

    /**
     * @param string $table
     * @param array|null $roles
     * @return bool
     */
    public function assert($table, $roles = null) {
        if (in_array($table, $this->_tables)) {
            if (null === $roles || self::ANY_ROLE == $roles)
                return true;
            foreach ($roles as $role) {
                if (in_array($role, $this->_roles))
                    return true;
            }
        }
        return false;
    }

    public function invoke(Pro_Db_Select &$select) {
        if ($this->_rule instanceof Fgac_Application_Plugins_PluginInterface) {
            $this->_rule->addRule($select);
        } else {
            $ruleClass = $this->_rule;
            /** @var $rule Fgac_Application_Plugins_PluginInterface */
            $rule = new $ruleClass();
            $rule->addRule($select);

        }
    }

}