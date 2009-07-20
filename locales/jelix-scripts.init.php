<?php
/**
* @package   locales
* @subpackage 
* @author    yourname
* @copyright 2008 yourname
* @link      http://www.yourwebsite.undefined
* @license    All right reserved
*/

require (dirname(__FILE__).'/../lib/jelix/init.php');

define ('JELIX_APP_PATH', dirname (__FILE__).DIRECTORY_SEPARATOR); // don't change

define ('JELIX_APP_VAR_PATH',     realpath(JELIX_APP_PATH.'./var/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_LOG_PATH',     realpath(JELIX_APP_PATH.'./var/log/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_CONFIG_PATH',  realpath(JELIX_APP_PATH.'./var/config/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_WWW_PATH',     realpath(JELIX_APP_PATH.'./www/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_CMD_PATH',     realpath(JELIX_APP_PATH.'./scripts/').DIRECTORY_SEPARATOR);

// the temp path for jelix-scripts
define ('JELIX_APP_TEMP_PATH',    realpath(JELIX_APP_PATH.'../temp/locales-jelix-scripts/').DIRECTORY_SEPARATOR);

// the temp path for cli scripts of the application
define ('JELIX_APP_TEMP_CLI_PATH',    realpath(JELIX_APP_PATH.'../temp/locales-cli/').DIRECTORY_SEPARATOR);

// the temp path for the web scripts of the application (the same value as JELIX_APP_TEMP_PATH in application.init.php)
define ('JELIX_APP_REAL_TEMP_PATH',    realpath(JELIX_APP_PATH.'../temp/locales/').DIRECTORY_SEPARATOR);
