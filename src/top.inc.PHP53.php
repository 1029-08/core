<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * LiteCommerce
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@litecommerce.com so we can send you a copy immediately.
 * 
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage ____sub_package____
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

// It's the feature of PHP 5. We need to explicitly define current time zone.
// See also http://bugs.php.net/bug.php?id=48914
@date_default_timezone_set(@date_default_timezone_get());

// Timestamp of the application start
define('LC_START_TIME', time());

// Namespaces
define('LC_NAMESPACE',          'XLite');
define('LC_NAMESPACE_INCLUDES', 'Includes');
define('LC_MODEL_NS',           LC_NAMESPACE . '\Model');
define('LC_MODEL_PROXY_NS',     LC_MODEL_NS . '\Proxy');

// Paths
define('LC_DIR',               realpath(__DIR__));
define('LC_ROOT_DIR',          rtrim(LC_DIR, LC_DS) . LC_DS);
define('LC_CLASSES_DIR',       LC_ROOT_DIR . 'classes' . LC_DS);
define('LC_VAR_DIR',           LC_ROOT_DIR . 'var' . LC_DS);
define('LC_LIB_DIR',           LC_ROOT_DIR . 'lib' . LC_DS);
define('LC_SKINS_DIR',         LC_ROOT_DIR . 'skins' . LC_DS);
define('LC_CONFIG_DIR',        LC_ROOT_DIR . 'etc' . LC_DS);
define('LC_INCLUDES_DIR',      LC_ROOT_DIR . LC_NAMESPACE_INCLUDES . LC_DS);
define('LC_MODULES_DIR',       LC_CLASSES_DIR . LC_NAMESPACE . LC_DS . 'Module' . LC_DS);
define('LC_COMPILE_DIR',       LC_VAR_DIR . 'run' . LC_DS);
define('LC_CLASSES_CACHE_DIR', LC_COMPILE_DIR . 'classes' . LC_DS);
define('LC_MODEL_CACHE_DIR',   LC_CLASSES_CACHE_DIR . LC_NAMESPACE . LC_DS . 'Model' . LC_DS);
define('LC_PROXY_CACHE_DIR',   LC_MODEL_CACHE_DIR . 'Proxy' . LC_DS);
define('LC_LOCAL_REPOSITORY',  LC_VAR_DIR . 'addons' . LC_DS);
define('LC_BACKUP_DIR',        LC_VAR_DIR . 'backup' . LC_DS);

// Disabled xdebug coverage for Selenium-based tests [DEVELOPMENT PURPOSE]
if (isset($_COOKIE) && !empty($_COOKIE['no_xdebug_coverage']) && function_exists('xdebug_stop_code_coverage')) {
    @xdebug_stop_code_coverage();
}

// Autoloading routines
require_once (LC_INCLUDES_DIR . 'Autoloader.php');
\Includes\Autoloader::registerAll();

// Fire the error if LC is not installed
if (!defined('XLITE_INSTALL_MODE')) {
    \Includes\ErrorHandler::checkIsLCInstalled();
}

// So called "developer" mode. Set it to "false" in production mode!
define('LC_DEVELOPER_MODE', (bool) \Includes\Utils\ConfigParser::getOptions(array('performance', 'developer_mode')));

// Fatal error and exception handlers
register_shutdown_function(array('\Includes\ErrorHandler', 'shutdown'));
set_exception_handler(array('\Includes\ErrorHandler', 'handleException'));

// FIXME - to remove
require_once (LC_INCLUDES_DIR . 'prepend.php');

// TODO and FIXME: Check this feature with the installation!!
// require_once (LC_INCLUDES_DIR . 'NoDBStore.php');

// Check and (if needed) rebuild classes cache
if (!defined('LC_DO_NOT_REBUILD_CACHE')) {
    \Includes\Decorator\Utils\CacheManager::rebuildCache();
}
