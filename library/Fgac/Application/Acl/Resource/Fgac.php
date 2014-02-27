<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 27.02.14
 * Time: 2:41
 */

class Fgac_Application_Resource_Acl_Fgac extends Zend_Application_Resource_ResourceAbstract {

    public  function init() {
        $this->getBootstrap()->bootstrap('logger');
        $this->getBootstrap()->bootstrap('acl');
        /** @var $logger Zend_Log */
        $logger = Zend_Registry::get('logger')->ensureStream('fgac');
        $options = $this->getOptions();
        $fgac = FGAC_Application_Acl_DbPlugin::getInstance();
        if ("yes" === $options['enabled']) {
            $aclAlias = $options['acl-alias'];
            $fgac->setAclAlias($aclAlias);

            $logger->log("FGAC Plugin load", Zend_Log::DEBUG);
            // loading data from application.ini
            foreach($options['rule'] as $rule => $ruleOptions) {
                $logger->log("Rule (config):: \"$rule\"", Zend_Log::DEBUG);
                $fgac->addRule($ruleOptions['plugin'], array(
                    'tables' => explode(",", $ruleOptions['tables']),
                    'roles' => explode(",", $ruleOptions['roles']),
                ));
            }
            // loading data from DB storage
            $table = new Fgac_Application_Db_FgacAcl();
            $rules = $table->getFull();
            foreach($rules as $rule) {
                $logger->log("Rule (db):: \"" . $rule->name . "\"", Zend_Log::DEBUG);
                $fgac->addRule($rule->plugin, array(
                    'tables' => $rule->table_name,
                    'roles' => $rule->code,
                ));
            }
        } else {
            $logger->log("FGAC Plugin is not enabled", Zend_Log::INFO);
        }
    }
}