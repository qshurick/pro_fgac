<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 27.02.14
 * Time: 14:25
 */

class Fgac_Application_Db_FgacAcl extends Pro_Db_Table {
    protected $_name = "fgac_acl";
    public function getFull() {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($this->_name)
            ->join(array("fr" => "fgac_rules"), "fr.id = fgac_id")
            ->join(array("acl" => "acl_roles"), "acl.id = role_id");
        return $this->fetchAll($select);
    }
}