<?php
/**
 * @package   locales
 * @subpackage locales
 * @author    yourname
 * @copyright 2008 yourname
 * @link      http://www.yourwebsite.undefined
 * @license    All right reserved
 */
class localesCtrl extends jController {
    
	function index() {
		$rep = $this->getResponse('html');
		
		$locales_manager = jClasses::createInstance('locales~localesManager');
		
		$locales_manager->initLocaleManager();
		$locales_manager->createAndStoreParams();

		$params_name = $locales_manager->getLocaleAttributesName();
		$params_value = $locales_manager->getLocaleAttributesValues();
		
		$tpl = $this->assignTemplate($params_name, $params_value);
		$rep->body->assign('MAIN', $tpl->fetch('locales'));

		return $rep;
	}

	function assignTemplate($params_name, $params_value) {
	    $tpl = new jTpl(); 
		$tpl->assign('locales',localesFileManager::getLocalesFromConf());
		$tpl->assign('params_name',$params_name);
		$tpl->assign('params_value',$params_value);
		return $tpl;
	}

    function savecreate() {
		$rep = $this->getResponse('redirect');
		$rep->action = $this->getAction('index');
		$params = $this->params();
		
		$locales_manager = jClasses::createInstance('locales~localesManager');
		$locales_manager->createLocaleFiles($params);
		return $rep;
	}
	
    protected function getAction($method){
		if (strpos($method, "~")) return $method;
		global $gJCoord;
		return $gJCoord->action->module.'~'.$gJCoord->action->controller.':'.$method;
	}
}
