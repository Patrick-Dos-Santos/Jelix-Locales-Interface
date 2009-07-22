<?php

class localesFileManager {
    
    public static function listLocalesDirectories($module) {
		$lang_dir = JELIX_APP_PATH.'modules/'.$module.'/locales/';
		return localesFileManager::readFiles($lang_dir);
	}
	
    public static function getLocalesFromConf() {
		$ini_file = JELIX_APP_PATH.'var/config/defaultconfig.ini.php';
		$ini_content = jIniFile::read($ini_file);
		$locales = $ini_content['locales']['langs'];
		return $locales;
	}
	
    public static function listModules() {
		$module_dir = localesFileManager::getModuleDirectory();
		$modules = localesFileManager::readFiles($module_dir);
		sort($modules);
		return $modules;
	}
	
	public static function listLocalesFiles($module,$locale) {
	    $locale_file_directory = localesFileManager::getLocaleFileDirectory($module,$locale);
	    $locale_files = localesFileManager::readFiles($locale_file_directory);
	    sort($locale_files);
	    return $locale_files;	
	}
	
    public static function getFileContent($module, $locale, $locale_file) {
	    $locale_file_dir = localesFileManager::getLocaleFileDirectory($module,$locale);
		return  htmlspecialchars(jFile::read($locale_file_dir.$locale_file));
	}
	
    private static function readFiles($rep) {
		$files = array();
		if (is_dir($rep)) {
			if( $dir = opendir($rep) ) {
				while( FALSE !== ($fich = readdir($dir)) ) {
					if ($fich != "." && $fich != "..") {
						array_push($files,$fich);
					}
				}
			}
			closedir($dir);
		}
		return $files;
	}
	
	public static function getLocaleFileDirectory($module, $locale) {
	    return JELIX_APP_PATH.'modules/'.$module.'/locales/'.$locale.'/';
	}
	
	public static function getModuleDirectory() {
	    return JELIX_APP_PATH.'modules/';
	}
}