<?php
jClasses::inc("locales~localesFileManager");

/**
 * Reads the data from the interface and creates the locales files
 * @author Patrick Dos-Santos
 */
class localesInterfaceReader {
    
    /** @var Contains the file_paths=>file_contents */
    private $files_path = array();

    /**
     * Handles the paramaters and create the locales files
     * @param $params The parameters set by the user
     */
    public function createLocaleFiles($params) {
		foreach($params as $param_name=>$param_value) {
		    $files_path = $this->buildContentPerParam($param_name, $param_value);
		}
        $this->createFiles();
	}
	
	/**
	 * Build the content to write in the locale files
	 * @param $param_name string : The paramater_name (id::locale)
	 * @param $param_value string : The parameter_value
	 */
	private function buildContentPerParam($param_name, $param_value) {
	    $id_locale = $this->getUsefulParameters($param_name);
		if (!empty($id_locale)) {
		    $this->createLocaleContent($id_locale, $param_value);
		}  
	}
	
	/**
	 * Retrieves the paramaters matching the pattern "id::locale"
	 * @param The parameters name
	 * @return unknown_type
	 */
	private function getUsefulParameters($param_name) {
	    preg_match('/(.*)::(.*)/', $param_name, $id_locale);
	    return $id_locale;
	}
	
	/**
	 * Create the content to insert in the locale files
	 * @param $id_locale array : id=>locale
	 * @param $param_value string : The parameter's value
	 */
	private function createLocaleContent($id_locale, $param_value) 
	{	        
        $file_path = $this->getFilePath($id_locale);
        $param_id = $this->getParamId($id_locale);
        $param_key = $this->getKeyFromSession($param_id);

		if ($this->issetFilePath($file_path))
		{
		    $this->addLineToContent($file_path, $param_key, $param_value); 
		} else {
		    $this->createContentLine($file_path, $param_key, $param_value);
		}
	}
	
	/**
	 * Gets file's path from its id and locale
	 * @param $id_locale array : id=>locale
	 * @return string : The file's path
	 */
    private function getFilePath($id_locale) {
	    $param_id = $this->getParamId($id_locale);
	    
	    $module_directory = localesFileManager::getModuleDirectory();
	    $module = $this->getModuleFromSession($param_id);
        $locale = $this->getParamLocale($id_locale);
        $file_name = $this->getFileNameFromSession($param_id);
	    
	    return $module_directory.$module.'/locales/'.$locale.'/'.$file_name;
	}
	
	/**
	 * Checks if a file already exists in the file path array
	 * @param $file_path string : The file to check
	 * @return boolean : True if the file is already set, false if not
	 */
	private function issetFilePath($file_path) {
	    return isset($this->files_path[$file_path]);
	}
	
	/**
	 * Add a line "\n key = value" to the content associated to a file path
	 * @param $file_path string : The file's path
	 * @param $param_key string : The parameter's key
	 * @param $param_value string : The parameter's value
	 */
	private function addLineToContent($file_path, $param_key, $param_value) {
	    $this->files_path[$file_path] .= "\n".$param_key.'='.$param_value;
	}
	
	/**
	 * Creates the content associated to a file path and add a line to it
	 * @param $file_path string : The file's path
	 * @param $param_key string : The parameter's key
	 * @param $param_value string : The parameter's value
	 */
    private function createContentLine($file_path, $param_key, $param_value) {
	    $this->files_path[$file_path] = $param_key.'='.$param_value; 
	}
	
	/**
	 * Creates the locales files
	 * @param $files_path string
	 * @return unknown_type
	 */
	private function createFiles() {
		foreach($this->files_path as $file_path=>$content) {
            $this->createFile($file_path, $content);
		}
	}
	
	/**
	 * Creates a file from a given file path and content
	 * @param $file_path string : The file's path
	 * @param $content : The file's content
	 */
    private function createFile($file_path, $content) {
        jFile::write($file_path,$content);
    }
	
	private function getParamId($id_locale) {
	    return $id_locale[1];
	}
	
	private function getParamLocale($id_locale) {
	    return $id_locale[2];    
	}
	
    private function getKeyFromSession($param_id) {
	    return $_SESSION['id'.$param_id][0];
	}
	
	private function getModuleFromSession($param_id) {
	    return $_SESSION['id'.$param_id][1];
	}
	
	private function getFileNameFromSession($param_id) {
	    return $_SESSION['id'.$param_id][2];
	}
	
	
}