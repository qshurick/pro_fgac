<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 27.02.14
 * Time: 2:15
 */

class Fgac_Application_Acl_DbPlugin extends Pro_Db_Plugin_PluginAbstract {

    public function beforeFetch($tableName, Pro_Db_Select &$select) {
        $fgac = Fgac_Application_Acl_Engine::getInstance();
        $acl = Zend_Registry::get($fgac->getAclAlias());
        $fgac->invoke($tableName, $acl->getCurrentHierarchy(), $select);
    }

}