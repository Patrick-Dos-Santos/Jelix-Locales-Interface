<?php
jClasses::inc("locales~localesFileManager");
jClasses::inc("locales~localesFile");

/**
 * Handles the loading and saving of locales attributes
 */
class localesManager {
    
   private $localeFiles = array();
   private $idLocaleAttribut = array();
   private $valuesPerId = array();
   
   /**
    * Entry of the localesManagerClass
    * Handles all modules in the application
    */
   public function initLocaleManager() {
        $modules = localesFileManager::listModules();
	    if (!empty($modules)) {
			foreach($modules as $module) {
            $this->manageAllLocalesPerModule($module);
			}
		}
    }
    
    /**
     * Handles locales directories for a given module
     * @param $module string
     */
    private function manageAllLocalesPerModule($module) {
	    $locales= localesFileManager::listLocalesDirectories($module);
		    if (!empty($locales)) {
				foreach($locales as $locale) {
			        $this->manageAllFilePerLocale($module,$locale);
				}
			}
	}

	/**
	 * Handles all locales files for a given locale in a given module
	 * @param $module string
	 * @param $locale string
	 */
	private function manageAllFilePerLocale($module,$locale) {
	    $locale_file_names = localesFileManager::listLocalesFiles($module,$locale);
		if (!empty($locale_file_names)) {
			foreach($locale_file_names as $locale_file_name)
			{
			    $this->manageAllContentPerFile($module,$locale,$locale_file_name);
			}
        }
	}
	
	/**
	 * Handles the specified locale file's content
	 * @param $module string
	 * @param $locale string
	 * @param $locale_file_name string
	 */
	private function manageAllContentPerFile($module,$locale,$locale_file_name) {
		$content = localesFileManager::getFileContent($module,$locale,$locale_file_name);
		
		if (!empty($content)) {
			$locale_file = $this->createLocale($module,$locale,$locale_file_name,$content);
			array_push($this->localeFiles, $locale_file);
		}
	}
	
	/**
	 * 
	 * @param $module
	 * @param $locale
	 * @param $locale_file_name
	 * @param $content
	 * @return unknown_type
	 */
	private function createLocale($module, $locale, $locale_file_name, $content) {
	    
	    $locale_file = jClasses::createInstance('locales~localesFile');
		$locale_file->name = $locale_file_name;
		$locale_file->module = $module;
		$locale_file->locale = $locale;
		$locale_file->content = $content;
		
		return $locale_file;
	}
	
	public function createAndStoreParams() {
	    $id = 0;
	    foreach($this->localeFiles as $locale_file) {
	        
	        $content_lines = $this->divideContentInLines($locale_file->content);
	        
            foreach($content_lines as $key=>$value) {
                /* 
                 * When getting parameters from html, 
                 * jelix replaces the spaces and dots (and maybe other characters),
                 * with underscores.
                 * To overcome this issue, every key of a locale file is linked to an id,
                 * witch permit to identify the key's name, the file where the key is stored,
                 * and the module where the file is written, via the user's session.
                 */
               
                if(!isset ($this->idLocaleAttribut[$locale_file->module.':'.$locale_file->name.':'.$key])){
                    $this->idLocaleAttribut[$locale_file->module.':'.$locale_file->name.':'.$key] = $id;
                    $_SESSION['id'.$id] = array($key,$locale_file->module, $locale_file->name);
                    $value_id = $id;
                    $id++; 
                } else {
                    $value_id = $this->idLocaleAttribut[$locale_file->module.':'.$locale_file->name.':'.$key];
                   
                }
                $this->valuesPerId[$value_id.$locale_file->locale] = $value;
            }
	    }
	}
    
    private function divideContentInLines($content) {
	    // Divides the content in lines matching the pattern : key=value
		preg_match_all('/(.[^=]*)=(.*)/',$content,$lines);
		$content_lines = array();
		$nb_lines = count($lines[0]);
		for ($i = 0; $i < $nb_lines; $i++) {
			$content_lines[$lines[1][$i]] =  $lines[2][$i];
		}
		return $content_lines;
	}
	
	public function getLocaleAttributesName() {
	    $localeAttributesName = array();
	    foreach ($this->idLocaleAttribut as $key=>$id) {
	        preg_match('/(.*):(.*):(.*)/',$key,$locale_attributes);
	        $localeAttributesName[$id] = $locale_attributes[3];
	    }
	    return $localeAttributesName;
	}
	
	public function getLocaleAttributesValues() {
	    return $this->valuesPerId;
	}
	
	public function getLocaleFiles() {
	    return $this->localeFiles;
	}
	
	public function createLocaleFiles($params) {
		$module_dir = localesFileManager::getModuleDirectory();

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
	        $param_id = $id_locale[1];
	        $param_locale = $id_locale[2];
	        $module = $_SESSION['id'.$param_id][1];
	        $name = $_SESSION['id'.$param_id][2];
	        
	        $locale_file = $this->createLocale($module, $param_locale, $name, '');

			$is_file_found = false;
			
			foreach ($this->localeFiles as $current_locale_file) {
				if ($this->isFilesEqual($current_locale_file, $locale_file)) {
				    $this->addLineToContent($current_locale_file, $param_key, $param_value);
				    $is_file_found = true;
				    break;
				}
			}
			if (!$is_file_found) {
			     $this->addLineToContent($locale_file, $param_key, $param_value);
			     array_push($this->localeFiles, $locale_file);
			}
			
	}
	
	private function isFilesEqual($locale_file1, $locale_file2) {
	    return ($locale_file1->name == $locale_file2->name
	            && $locale_file1->module == $locale_file2->module
	            && $locale_file1->locale == $locale_file2->locale);
	}
	
	
	
    private function getParamKey($id_locale) {
        $param_id = $this->getParamId($id_locale);
	    return $_SESSION['id'.$param_id][0];
	}
	
    private function getParamId($id_locale) {
	    return $id_locale[1];
	}
	
	private function addLineToContent($locale_file, $param_key, $param_value) {
	    $locale_file->content .= $param_key.'='.$param_value."\n";
	}
	
    private function saveCreateFiles() {

		foreach($this->localeFiles as $locale_file) {
		    $path = $this->getFilePath($locale_file);
			jFile::write($path,$locale_file->content);
			@chmod($file,  0666);
		}
	}
	
	private function getFilePath($locale_file) {
	    $module_directory = localesFileManager::getModuleDirectory();
	    $module = $locale_file->module;
	    $locale = $locale_file->locale;
	    $file_name = $locale_file->name;
	    return localesFileManager::getModuleDirectory().'/'.$module.'/locales/'.$locale.'/'.$file_name;
	}
}