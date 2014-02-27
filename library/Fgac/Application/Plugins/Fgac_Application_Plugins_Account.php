<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 27.02.14
 * Time: 3:53
 */

class Fgac_Application_Plugins_Account implements Fgac_Application_Plugins_PluginInterface {
    public function addRule(Zend_Db_Table_Select &$select) {
        $select->join(array("acc" => "account"), "acc.id = account_id");
    }

}