<?php

/**
 * This class mananages the reading and writing in locales files
 * @author Patrick Dos-Santos
 */
class localesFileManager {
    
    /**
     * List all locales directories from a given module
     * @param $module string : The module's name
     * @return array() : The files in the module
     */
    public static function listLocalesDirectories($module) {
		$lang_dir = JELIX_APP_PATH.'modules/'.$module.'/locales/';
		return localesFileManager::readFiles($lang_dir);
	}
	
	/**
	 * Get all locales from the configuration
	 * @return array() : The locales specified in the configuration file
	 */
    public static function getLocalesFromConf() {
		$ini_file = JELIX_APP_PATH.'var/config/defaultconfig.ini.php';
		$ini_content = jIniFile::read($ini_file);
		$locales = $ini_content['locales']['langs'];
		return $locales;
	}
	
	/**
	 * Lists all modules in the application
	 * @return array() : The modules in the application
	 */
    public static function listModules() {
		$module_dir = localesFileManager::getModuleDirectory();
		$modules = localesFileManager::readFiles($module_dir);
		sort($modules);
		return $modules;
	}
	
	/**
	 * Lists all the locales files in a given module for a specified locale
	 * @param $module string : The module's name
	 * @param $locale string : The locale's name
	 * @return array() : The locale files' name
	 */
	public static function listLocalesFiles($module,$locale) {
	    $locale_file_directory = localesFileManager::getLocaleFileDirectory($module,$locale);
	    $locale_files = localesFileManager::readFiles($locale_file_directory);
	    sort($locale_files);
	    return $locale_files;	
	}
	
	/**
	 * Read a file's content
	 * @param $module string : The module's name
	 * @param $locale string : The locale's name
	 * @param $locale_file string : The locale file's name
	 * @return string : The file's content
	 */
    public static function getFileContent($module, $locale, $locale_file) {
	    $locale_file_dir = localesFileManager::getLocaleFileDirectory($module,$locale);
		$content = jFile::read($locale_file_dir.$locale_file);
		if ($content === false) {
		    $content = '';
		}
		return $content;
	}
	
	/**
	 * List the files in a directory
	 * @param $directory string : The directory's path
	 * @return array() : The files in the directory
	 */
    private static function readFiles($directory) {
		$files = array();
		if (is_dir($directory)) {
			if( $dir = opendir($directory) ) {
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
	
	/**
	 * Gives the path to a given module and directory
	 * @param $module string : The module's name
	 * @param $locale string : The locale's name
	 * @return string : The path to the directory
	 */
	public static function getLocaleFileDirectory($module, $locale) {
	    return JELIX_APP_PATH.'modules/'.$module.'/locales/'.$locale.'/';
	}
	
	/**
	 * Gives the path to the module's directory of the application
	 * @return string : The path to the module's directory
	 */
	public static function getModuleDirectory() {
	    return JELIX_APP_PATH.'modules/';
	}
}