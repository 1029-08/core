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
 * @subpackage Includes_Utils
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace Includes\Utils;

/**
 * URLManager 
 * 
 * @package    XLite
 * @see        ____class_see____
 * @since      3.0.0
 */
class URLManager extends AUtils
{
    /**
     * Remove trailing slashes from URL 
     * 
     * @param string $url URL to prepare
     *  
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function trimTrailingSlashes($url)
    {
        return \Includes\Utils\Converter::trimTrailingChars($url, '/');
    }

    /**
     * Return full URL for the resource
     * 
     * @param string $url      url part to add
     * @param bool   $isSecure use HTTP or HTTPS
     * @param array  $params   optional URL params
     *  
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getShopURL($url = '', $isSecure = false, array $params = array())
    {
        $hostDetails = \Includes\Utils\ConfigParser::getOptions('host_details');

        if ($host = $hostDetails['http' . ($isSecure ? 's' : '') . '_host']) {

            $proto = ($isSecure ? 'https' : 'http') . '://';

            if ('/' != substr($url, 0, 1)) {
                $url = $hostDetails['web_dir_wo_slash'] . '/' . $url;
            }

            if ($isSecure) {
                $session = \XLite\Core\Session::getInstance();
                $url .= (false !== strpos($url, '?') ? '&' : '?') . $session->getName() . '=' . $session->getID();
            }

            $url = $proto . $host . $url;
        }

        return $url;
    }

    /**
     * Return current URL
     * 
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getSelfURL()
    {
        return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
    }
}
