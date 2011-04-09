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
 * @subpackage Includes
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      1.0.0
 */

namespace Includes\Decorator\Utils;

/**
 * Plugins manager 
 *
 * Available hooks:
 * - run()
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   1.0.0
 */
abstract class PluginManager extends \Includes\Decorator\Utils\AUtils
{
    /**
     * Config file name 
     */
    const FILE_INI = 'plugins.ini';


    /**
     * List of registered plugins 
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  1.0.0
     */
    protected static $plugins;


    /**
     * Check and execute hook handlers
     *
     * @param string $hook Hook name
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public static function invokeHook($hook)
    {
        // Get plugins "subscribed" for the hook
        foreach (static::getPlugins($hook) as $plugin => $instance) {

            if (!isset($instance)) {
                $class = '\Includes\Decorator\Plugin\\' . str_replace('_', '\\', $plugin) . '\Main';
                static::$plugins[$plugin] = $instance = new $class();
            }

            $instance->{'executeHookHandler' . ucfirst(\Includes\Utils\Converter::convertToCamelCase($hook))}();
        }
    }

    /**
     * Return list of registered plugins
     *
     * @param string $hook hook name (optional)
     *
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected static function getPlugins($hook = null)
    {
        if (!isset(static::$plugins)) {

            // Check config file
            if (\Includes\Utils\FileManager::isFileReadable(static::getConfigFile())) {

                // Iterate over all sections
                foreach (parse_ini_file(static::getConfigFile(), true) as $section => $plugins) {

                    // Set plugins order
                    $plugins = array_filter($plugins);
                    asort($plugins, SORT_NUMERIC);

                    // Save plugins list
                    static::$plugins[$section] = array_fill_keys(array_keys($plugins), null);
                }

            } else {

                \Includes\ErrorHandler::fireError('Unable to read config file for the Decorator plugins');
            }
        }

        return \Includes\Utils\ArrayManager::getIndex(static::$plugins, $hook);
    }

    /**
     * Return configuration file
     * 
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected static function getConfigFile()
    {
        return LC_INCLUDES_DIR . 'Decorator' . LC_DS . self::FILE_INI;
    }
}
