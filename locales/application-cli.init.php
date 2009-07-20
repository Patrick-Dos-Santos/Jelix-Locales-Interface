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

define ('JELIX_APP_TEMP_PATH',    realpath(JELIX_APP_PATH.'../temp/locales-cli/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_VAR_PATH',     realpath(JELIX_APP_PATH.'./var/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_LOG_PATH',     realpath(JELIX_APP_PATH.'./var/log/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_CONFIG_PATH',  realpath(JELIX_APP_PATH.'./var/config/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_WWW_PATH',     realpath(JELIX_APP_PATH.'./www/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_CMD_PATH',     realpath(JELIX_APP_PATH.'./scripts/').DIRECTORY_SEPARATOR);

