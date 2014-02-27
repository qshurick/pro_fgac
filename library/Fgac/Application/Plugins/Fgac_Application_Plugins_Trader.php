<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 27.02.14
 * Time: 3:53
 */

class Fgac_Application_Plugins_Trader implements Fgac_Application_Plugins_PluginInterface  {
    public function addRule(Zend_Db_Table_Select &$select) {
        $select->join(array("tr" => "trader"), "tr.id = trader_id");
    }

}