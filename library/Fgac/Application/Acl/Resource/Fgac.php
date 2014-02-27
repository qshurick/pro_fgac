<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 27.02.14
 * Time: 2:41
 */

class Fgac_Application_Acl_Resource_Fgac extends Zend_Application_Resource_ResourceAbstract {

    public  function init() {
        $this->getBootstrap()->bootstrap('logger');
        $this->getBootstrap()->bootstrap('acl');

        Fgac_Application_Acl_Engine::setup($this->getOptions());
    }
}