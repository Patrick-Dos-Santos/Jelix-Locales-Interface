<?php
jClasses::inc("locales~localesFileManager");

/**
 * Handles the loading of locales attributes for them to be displayed in the web interface
 * @author Patrick Dos-Santos
 */
class localesInterfaceLoader {
    
   /** @var array[module:$locale_file_name:$key] = id */
   private $idLocaleAttribut = array();
  
   /** @var array[id_locale] = value */
   private $valuesPerId = array();
   
    /** @var array[id] = key */
   private $localeAttributesName = array();
   
   /** @var int */
   private $id = 0;
   
   
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
     * Returns all the keys in the locale files associated to an id
     * @return array() : The keys from all locale files
     */
	public function getLocaleAttributesName() {
	    return $this->localeAttributesName;
	}
	
	/**
	 * Returns all the values in the locale files associated to an id 
	 * @return array() : The values from all locale files
	 */
	public function getLocaleAttributesValues() {
	    return $this->valuesPerId;
	}
	
    /**
     * Handles locales directories for a given module
     * @param $module string : The module's name
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
	 * @param $module string : The module's name
	 * @param $locale string : The module's locale
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
			$this->storeContent($module, $locale, $locale_file_name, $content);
		}
	}
	
    /**
     * Store the content in the parameters for the locale interface
     * @param $module string : The module's name
     * @param $locale string : The locale's name  
     * @param $locale_file_name string : The locales's file name
     * @param $content string : The file's content
     */
	private function storeContent($module, $locale, $locale_file_name, $content) {
        $content_lines = $this->divideContentInLines($content);
	        
        foreach($content_lines as $key=>$value) {
            $this->storeParams($module, $locale_file_name, $key, $value, $locale);
        }
	}
    
	/**
	 * Divides the content in lines
	 * @param $content string : The file's content
	 * @return array() : The content lines
	 */
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
    
	/**
	 * Stores a key and value in the paramaters for the locales interface and in the session
	 * @param $module string : The module's name
	 * @param $locale_file_name string : The locale's file name
	 * @param $key string : The attribute key
	 * @param $value string : The attribute value
	 * @param $locale string : The file's locale
	 */
	private function storeParams($module, $locale_file_name, $key, $value, $locale) {

	    if($this->issetKeyInIdLocaleAttribute($module, $locale_file_name, $key)){
	        $value_id = $this->idLocaleAttribut[$module.':'.$locale_file_name.':'.$key];
        } else {
            $this->storeLocaleAttribute($module, $locale_file_name, $key);
	        $this->storeLocaleAttributesName($key);
	        /* 
        	* When getting parameters from html, jelix replaces some characters
        	* with underscores.
        	* To overcome this issue, all values are stored in session 
        	*/
	        $this->storeValuesInSession($key,$module, $locale_file_name);
	        
	        $value_id = $this->id;
	        $this->id++;
        }
        $this->valuesPerId[$value_id.$locale] = htmlspecialchars($value);
	}
	
	/**
	 * Stores the locales attribute's module, file name and key
	 * @param string : $module The module's name
	 * @param string : $locale_file_name The locales file name
	 * @param string : $key The key name
	 */
	private function storeLocaleAttribute($module, $locale_file_name, $key) {
	    $this->idLocaleAttribut[$module.':'.$locale_file_name.':'.$key] = $this->id;
	}
	
	/**
	 * Stores the locales attribute's name
	 * @param $key string : The key to store
	 */
    private function storeLocaleAttributesName($key) {
	    $this->localeAttributesName[$this->id] = $key;
	}
	
    /**
     * Associate an id to a key, module and file name and stores it in the user's session
     * @param $key string : The key's name
     * @param $module string : The module's name
     * @param $locale_file_name string : The locales file's name
     */
	private function storeValuesInSession($key,$module, $locale_file_name) {
	    $_SESSION['id'.$this->id] = array($key,$module, $locale_file_name);
	}
	
	/**
	 * Indicates wether or not a key is already stored in the idLocaleAttribute array
	 * @param $module string : The module's name
	 * @param $locale_file_name string : The locale file's name
	 * @param $key string : The key's name
	 * @return boolean : True if the key is set, false if not
	 */
	private function issetKeyInIdLocaleAttribute($module, $locale_file_name, $key) {
	    return isset ($this->idLocaleAttribut[$module.':'.$locale_file_name.':'.$key]);
	}
	
}