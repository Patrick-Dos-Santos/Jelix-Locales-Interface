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
	
    private $params_name;
    private $params_value;
    private $param_id = 0;
    private $locales_content = array();
    
	function index() {
		$rep = $this->getResponse('html');
		
		$this->handleAllModules();
		$tpl = $this->assignTemplate();
		$rep->body->assign('MAIN', $tpl->fetch('locales'));

		return $rep;
	}

	function assignTemplate() {
	    $tpl = new jTpl(); 
		$tpl->assign('locales',$this->getLocalesFromConf());
		$tpl->assign('params_name',$this->params_name);
		$tpl->assign('params_value',$this->params_value);
		return $tpl;
	}
	
	private function getLocalesFromConf() {
		$ini_file = JELIX_APP_PATH.'var/config/defaultconfig.ini.php';
		$ini_content = jIniFile::read($ini_file);
		$locales = $ini_content['locales']['langs'];
		return $locales;
	}
	
	private function handleAllModules() {
		$modules = $this->listModules();
	    if (!empty($modules)) {
			foreach($modules as $module) {
	            $this->handleAllLocalesPerModule($module);
			}
		}
	}
	
    private function handleAllLocalesPerModule($module) {
	    $locales= $this->listLocalesDirectories($module);
		    if (!empty($locales)) {
				foreach($locales as $locale) {
			        $this->handleAllFilePerLocale($module,$locale);
				}
			}
	}
			
	private function handleAllFilePerLocale($module,$locale) {
	    $locale_files = $this->listLocalesFiles($module,$locale);
		if (!empty($locale_files)) {
			foreach($locale_files as $locale_file)
			{
			    $this->handleAllContentPerFile($module,$locale,$locale_file);
			}
        }
	}
	
	private function handleAllContentPerFile($module,$locale,$locale_file) {
		
		$content = $this->getFileContent($module,$locale,$locale_file);
		
		$content_lines = $this->divideContentInLines($content);
		if (!empty($content_lines)) {
		    $file_prefix = $this->getFilePrefix($locale_file);
		    $file_charset = $this->getCharset($locale_file,$file_prefix);
		    
		    $this->setTemplateParameters($content_lines,$file_prefix,$file_charset,$locale,$module);
		}
	}
	
	private function getFileContent($module, $locale, $locale_file) {
	    $locale_file_dir = $this->getLocaleFileDirectory($module,$locale);
		return  htmlspecialchars(jFile::read($locale_file_dir.$locale_file));
	}
	
	private function divideContentInLines($content) {
	    // Divides the content in lines matching the pattern : key=value
		preg_match_all('/(.*[^=])=(.*)/',$content,$lines);
		$content_lines = array();
		$nb_lines = count($lines[0]);
		for ($i = 0; $i < $nb_lines; $i++) {
			$content_lines[$lines[1][$i]] =  $lines[2][$i];
		}
		return $content_lines;
	}
	
	private function getFilePrefix($locale_file) {
	    preg_match('/(.*)\.(.*\.properties)/', $locale_file, $file_prefix);
	    return $file_prefix[1];
	}
	
	private function getCharset($locale_file,$file_prefix) {
		preg_match('/('.$file_prefix.')\.(.*)\.(properties)/', $locale_file, $charset);
		return $charset[2];
	}
	
    private function setTemplateParameters($content_lines,$file_prefix,$file_charset,$locale,$module) {
	    foreach($content_lines as $param_name=>$value) {
	        
	        // If the param isn't stored yet, we store it in the template parameter and in the session
	        if (!$this->isSetParamName($file_prefix,$param_name)) {
	            $this->setParamName($file_prefix, $param_name);
	            $this->storeParamInSession($module, $file_prefix, $file_charset,$param_name);
	            $this->setParamValue($this->param_id, $locale, $value);
	            $this->param_id++;
	        } else { //else we just store the parameter's value
	            $id = $this->params_name[$file_prefix.'::'.$param_name];
	            $this->setParamValue($id, $locale, $value);
	        }        
	    }
	}

	private function setParamName($file_prefix, $param) {
	    $this->params_name[$file_prefix.'::'.$param] = $this->param_id;    
	}
	
	private function isSetParamName($file_prefix, $param) {
	    return isset($this->params_name[$file_prefix.'::'.$param]);
	}
	
    private function setParamValue($id, $locale, $value) {
	    $this->params_value[$id.$locale] = $value;    
	}
	
	private function storeParamInSession($module, $file_prefix, $file_charset, $param_name) {
	    $_SESSION['id'.$this->param_id] = array($module, $file_prefix, $file_charset,$param_name);
	}
	
	private function listModules() {
		$module_dir = $this->getModuleDirectory();
		return $this->readFiles($module_dir);
	}
	
	private function listLocalesDirectories($module) {
		$lang_dir = JELIX_APP_PATH.'modules/'.$module.'/locales/';
		return $this->readFiles($lang_dir);
	}
	
	private function listLocalesFiles($module,$locale) {
		return $this->readFiles($this->getLocaleFileDirectory($module,$locale));	
	}
	
    private function readFiles($rep) {
		$files = array();
		if( $dir = opendir($rep) ) {
			while( FALSE !== ($fich = readdir($dir)) ) {
				if ($fich != "." && $fich != "..") {
					array_push($files,$fich);
				}
			}
		}
		closedir($dir);
		return $files;
	}
	
	private function getLocaleFileDirectory($module, $locale) {
	    return JELIX_APP_PATH.'modules/'.$module.'/locales/'.$locale.'/';
	}
	
    function savecreate() {
		$rep = $this->getResponse('redirect');

		$rep->action = $this->getAction('index');
		$params = $this->params();
		$this->createLocaleFiles($params);
		
		return $rep;
	}
	
	private function createLocaleFiles($params) {
		$module_dir = $this->getModuleDirectory();

		foreach($params as $param_name=>$param_value) {
		    $this->buildContentPerParam($param_name, $param_value, $module_dir);
		}
        $this->saveCreateFiles();
	}
	
	private function buildContentPerParam($param_name, $param_value, $module_dir) {
        // Gets the parameters matching the pattern "name::locale"
	    $id_locale = $this->getUsefulParameters($param_name);
		if (!empty($id_locale)) {
		    $this->createLocaleContent($id_locale, $param_value);
		}  
	}
	
	/*
	 * Returns the paramater if it matches the pattern "id::locale"
	 * Else, returns an empty array
	 */
	private function getUsefulParameters($param_name) {
	    preg_match('/(.*)::(.*)/', $param_name, $id_locale);
	    return $id_locale;
	}
	
	private function createLocaleContent($id_locale, $param_value) 
	{	        
	        $param_key = $this->getParamKey($id_locale);
			$file_path = $this->getFilePath($id_locale);
			$is_file_found = false;
			
			foreach ($this->locales_content as $path=>$content) {
				if ($path === $file_path) {
				    $this->addLineToContent($file_path, $param_key, $param_value);
				    $is_file_found = true;
				    break;
				}
			}
			if (!$is_file_found) {
			     $this->createContent($file_path, $param_key, $param_value);
			}
			
	}
	
	private function getParamId($id_locale) {
	    return $id_locale[1];
	}
	
    private function getParamKey($id_locale) {
        $param_id = $this->getParamId($id_locale);
	    return $_SESSION['id'.$param_id][3];
	}
	
	private function getFilePath($id_locale) {
	    $param_id = $this->getParamId($id_locale);
	    $module_dir = $this->getModuleDirectory();
	    $module = $_SESSION['id'.$param_id][0];
	    $param_locale = $id_locale[2];
		$file_name =  $_SESSION['id'.$param_id][1];
		$file_charset =  $_SESSION['id'.$param_id][2];
		
	    return $module_dir.$module.'/locales/'.$param_locale.'/'.$file_name.'.'.$file_charset.'.properties';
	}
	
	private function addLineToContent($path, $param_key, $param_value) {
	    $this->locales_content[$path] .= $param_key.'='.$param_value."\n";
	}
	
    private function createContent($path, $param_key, $param_value) {
	    $this->locales_content[$path] = $param_key.'='.$param_value."\n";
	}
	
    private function saveCreateFiles() {

		foreach($this->locales_content as $file_name=>$content) {
			jFile::write($file_name,$content);
			@chmod($file,  0666);
		}

	}
	
	private function getModuleDirectory() {
	    return JELIX_APP_PATH.'modules/';
	}
	
	protected function getAction($method){
		if (strpos($method, "~")) return $method;
		global $gJCoord;
		return $gJCoord->action->module.'~'.$gJCoord->action->controller.':'.$method;
	}
}
