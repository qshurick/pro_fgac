<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 27.02.14
 * Time: 13:59
 */

interface Fgac_Application_Plugins_PluginInterface {
    public function addRule(Zend_Db_Table_Select &$select);
}