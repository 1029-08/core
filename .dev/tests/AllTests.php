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
 * @package    Tests
 * @subpackage Core
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */
if (0 > version_compare(phpversion(), '5.3.0')) {
    echo ('PHP version must be 5.3.0 or later' . PHP_EOL);
    die(1);
}

if (false === defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'XLite_Tests_AllTests::main');
}

ini_set('memory_limit', '900M');

// PHPUnit classes
define('PATH_TESTS', realpath(__DIR__));
define('PATH_ROOT', realpath(__DIR__ . '/../..'));

// Include local code
if (file_exists(PATH_TESTS . '/local.php')) {
    require_once PATH_TESTS . '/local.php';
}

if (defined('DRUPAL_SITE_PATH') && !defined('LOCAL_TESTS')) {
    define('PATH_SRC', realpath(DRUPAL_SITE_PATH . '/modules/lc_connector/litecommerce'));

} else { 
    define('PATH_SRC', realpath(PATH_ROOT . '/src'));
}

set_include_path(
    get_include_path()
    . PATH_SEPARATOR . PATH_SRC . '/classes'
    . PATH_SEPARATOR . PATH_SRC . '/var/run/classes'
);

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once PATH_TESTS . '/PHPUnit/TestSuite.php';
require_once PATH_TESTS . '/PHPUnit/TestCase.php';
require_once PATH_TESTS . '/PHPUnit/MetricWriter.php';
require_once PATH_TESTS . '/PHPUnit/SeleniumTestCase.php';

if (!defined('MENU_LOCAL_TASK')) {
    define('MENU_LOCAL_TASK', 0x0080 | 0x0004);
}

// Start X-Lite core

define('LC_DO_NOT_REBUILD_CACHE', true);

if (
    defined('INCLUDE_ONLY_TESTS')
    && preg_match('/DEPLOY_/', constant('INCLUDE_ONLY_TESTS'))
    && !defined('XLITE_INSTALL_MODE')
) {
    define('XLITE_INSTALL_MODE', true);
}

require_once PATH_SRC . '/top.inc.php';

if (!defined('SELENIUM_SOURCE_URL')) {
    $arr = explode('/', realpath(__DIR__ . '/../..'));
    array_shift($arr);
    array_shift($arr);
    array_shift($arr);
    array_shift($arr);

    define('SELENIUM_SOURCE_URL', 'http://xcart2-530.crtdev.local/~' . posix_getlogin() . '/' . implode('/', $arr));

    unset($arr);
}

if (!defined('SELENIUM_SERVER')) {
    define('SELENIUM_SERVER', 'cormorant.crtdev.local');
}

if (isset($_SERVER['argv']) && preg_match('/--log-xml\s+(\S+)\s/s', implode(' ', $_SERVER['argv']), $match)) {
    XLite_Tests_MetricWriter::init($match[1] . '.speed');
    unset($match);
}

if (!defined('INCLUDE_ONLY_TESTS') || !preg_match('/DEPLOY_/', constant('INCLUDE_ONLY_TESTS'))) {
    PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(PATH_ROOT . '/.dev');
    PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(PATH_SRC . '/etc');
    PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist(PATH_SRC . '/var/run/classes');
    PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(PATH_SRC . '/var/run/classes/XLite/Model/Proxy');
}

foreach (glob(LC_ROOT_DIR . 'var/log/selenium.*.html') as $f) {
    @unlink($f);
}

/**
 * Class to run all the tests
 * 
 * @package    X-Lite_Tests
 * @subpackage Main
 * @see        ____class_see____
 * @since      1.0.0
 */
class XLite_Tests_AllTests
{
    /**
     * Test suite main method
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Creates the phpunit test suite 
     * 
     * @return XLite_Tests_TestSuite
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public static function suite()
    {
        $suite = new XLite_Tests_TestSuite('LiteCommerce - AllTests');

        $deploy = null;
        $includes = false;
        $includeTests = array();
        $excludes = array();
        $ds = preg_quote(LC_DS, '/');

        if (defined('INCLUDE_ONLY_TESTS')) {
            $includes = array_map('trim', explode(',', INCLUDE_ONLY_TESTS));

            if (in_array('LOCAL_TESTS', $includes)) {
                $k = array_search('LOCAL_TESTS', $includes);
                unset($includes[$k]);
            }

            if (in_array('NOWEB', $includes)) {
                if (!defined('SELENIUM_DISABLED')) {
                    define('SELENIUM_DISABLED', true);
                }
                $k = array_search('NOWEB', $includes);
                unset($includes[$k]);
            }

            if (in_array('ONLYWEB', $includes)) {
                if (!defined('UNITS_DISABLED')) {
                    define('UNITS_DISABLED', true);
                }
                $k = array_search('ONLYWEB', $includes);
                unset($includes[$k]);
            }

            if (in_array('DEPLOY_DRUPAL', $includes)) {
                $deploy = 'Drupal';

            } elseif (in_array('DEPLOY_STANDALONE', $includes)) {
                $deploy = 'Standalone';
            }

            if (!is_null($deploy)) {
                if (!defined('UNITS_DISABLED')) {
                    define('UNITS_DISABLED', true);
                }
                $k = array_search('DEPLOY_' . strtoupper($deploy), $includes);
                if (!defined('DIR_TESTS')) {
                    define('DIR_TESTS', 'Deploy' . DIRECTORY_SEPARATOR . $deploy);
                }
                unset($includes[$k]);
            }

            if (in_array('W3C', $includes)) {
                if (!defined('W3C_VALIDATION')) {
                    define('W3C_VALIDATION', true);
                }
                $k = array_search('W3C', $includes);
                unset($includes[$k]);
            }

            foreach ($includes as $k => $v) {
                if ('-' == substr($v, 0, 1)) {
                    $excludes[] = substr($v, 1);
                    unset($includes[$k]);
                }
            }

            foreach ($includes as $k => $v) {
                $tmp = explode(':', $v, 2);
                $includes[$k] = $tmp[0];
                if (isset($tmp[1])) {
                    $includeTests[$tmp[0]] = $tmp[1];
                }
            }
        }

        if (isset($deploy) && !defined('DEPLOYMENT_TEST')) {
            define('DEPLOYMENT_TEST', true);
        }

        // Include abstract classes
        $classesDir  = dirname( __FILE__ );
        $pattern     = '/^' . preg_quote($classesDir, '/') . '.*\/(?:\w*Abstract|A[A-Z][a-z]\w*)\.php$/Ss';

        $dirIterator = new RecursiveDirectoryIterator($classesDir . DIRECTORY_SEPARATOR);
        $iterator    = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $filePath => $fileObject) {
            if (preg_match($pattern, $filePath, $matches)) {
                require_once $filePath;
            }
        }

        // Include fake classes
        if (!defined('DEPLOYMENT_TEST')) {
            $classesDir  = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'FakeClass' . DIRECTORY_SEPARATOR;
            $pattern     = '/^' . preg_quote($classesDir, '/') . '.+\.php$/Ss';

            $dirIterator = new RecursiveDirectoryIterator($classesDir . DIRECTORY_SEPARATOR);
            $iterator    = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($iterator as $filePath => $fileObject) {
                if (preg_match($pattern, $filePath, $matches)) {
                    require_once $filePath;
                }
            }
        }

        // DB backup
        echo ('DB backup ... ');
        $path = dirname(__FILE__) . '/dump.sql';
        if (file_exists($path)) {
            unlink($path);
        }

        if (!isset($deploy) || !$deploy) {
            $config = XLite::getInstance()->getOptions('database_details');
            $cmd = 'mysqldump --opt -h' . $config['hostspec'];
            if ($config['port']) {
                $cmd .= ':' . $config['port'];
            }

            $cmd .= ' -u' . $config['username'] . ' -p' . $config['password'];
            if ($config['socket']) {
                $cmd .= ' -S' . $config['socket'];
            }

            exec($cmd .= ' ' . $config['database'] . ' > ' . $path);

            echo ('done' . PHP_EOL);
        } else {
            echo ('ignored' . PHP_EOL);
        }

        // Classes tests
        if (!defined('UNITS_DISABLED')) {

            $classesDir  = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR;
            $pattern     = '/^' . str_replace('/', '\/', preg_quote($classesDir)) . '(.*)\.php$/';

            $dirIterator = new RecursiveDirectoryIterator($classesDir);
            $iterator    = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($iterator as $filePath => $fileObject) {
                if (
                    preg_match($pattern, $filePath, $matches)
                    && !empty($matches[1])
                    && !preg_match('/\/(\w+Abstract|A[A-Z]\w+)\.php$/Ss', $filePath)
                    && (!$includes || in_array($matches[1], $includes))
                    && (!$excludes || !in_array($matches[1], $excludes))
                    && !preg_match('/' . $ds . '(?:scripts|skins)' . $ds . '/Ss', $filePath)
                ) {
                    $class = XLite_Tests_TestCase::CLASS_PREFIX
                        . str_replace(DIRECTORY_SEPARATOR, '_', $matches[1]);

                    require_once $filePath;
                    $suite->addTest(new XLite_Tests_TestSuite(new ReflectionClass($class)));

                    if (isset($includeTests[$matches[1]])) {
                        eval($class . '::$testsRange = array($includeTests[$matches[1]]);');
                    }
                }
            }
        }

        // Web tests
        if (!defined('SELENIUM_DISABLED')) {

            if (!defined('DIR_TESTS')) {
                define('DIR_TESTS', 'Web');
            }

            $classesDir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . constant('DIR_TESTS') . DIRECTORY_SEPARATOR;
            $pattern    = '/^' . str_replace('/', '\/', preg_quote($classesDir)) . '(.*)\.php$/';

            $dirIterator = new RecursiveDirectoryIterator($classesDir);
            $iterator    = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($iterator as $filePath => $fileObject) {
                if (
                    preg_match($pattern, $filePath, $matches)
                    && !empty($matches[1])
                    && !preg_match('/\/(\w+Abstract|A[A-Z]\d+)\.php/Ss', $filePath)
                    && (!$includes || in_array($matches[1], $includes))
                    && (!$excludes || !in_array($matches[1], $excludes))
                    && !preg_match('/' . $ds . '(?:scripts|skins)' . $ds . '/Ss', $filePath)
                ) {

                    $classPrefix = !isset($deploy)
                        ? XLite_Tests_SeleniumTestCase::CLASS_PREFIX
                        : 'XLite_Deploy_' . $deploy . '_';
                    $class = $classPrefix . str_replace(DIRECTORY_SEPARATOR, '_', $matches[1]);

                    require_once $filePath;
                    //$suite->addTest(new XLite_Tests_TestSuite(new ReflectionClass($class)));
                    $suite->addTestSuite($class);

                    if (isset($includeTests[$matches[1]])) {
                        eval($class . '::$testsRange = array($includeTests[$matches[1]]);');
                    }
                }
            } 
        }

        error_reporting(E_ALL);

        return $suite;
    }
}

// Execute
if (PHPUnit_MAIN_METHOD === 'XLite_Tests_AllTests::main') {
    XLite_Tests_AllTests::main();
}
