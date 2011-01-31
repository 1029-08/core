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
 * @subpackage Core
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Core;

/**
 * Miscelaneous convertion routines
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Converter extends \XLite\Base\Singleton
{
    /**
     * Method name translation records
     *
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected static $to = array(
        'Q', 'W', 'E', 'R', 'T',
        'Y', 'U', 'I', 'O', 'P',
        'A', 'S', 'D', 'F', 'G',
        'H', 'J', 'K', 'L', 'Z',
        'X', 'C', 'V', 'B', 'N',
        'M',
    );

    /**
     * Method name translation patterns
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected static $from = array(
        '_q', '_w', '_e', '_r', '_t',
        '_y', '_u', '_i', '_o', '_p',
        '_a', '_s', '_d', '_f', '_g',
        '_h', '_j', '_k', '_l', '_z',
        '_x', '_c', '_v', '_b', '_n',
        '_m',
    );

    /**
     * Convert a string like "test_foo_bar" into the camel case (like "TestFooBar")
     * 
     * @param string $string String to convert
     *  
     * @return string
     * @access public
     * @since  3.0
     */
    public static function convertToCamelCase($string)
    {
        return ucfirst(str_replace(self::$from, self::$to, strval($string)));
    }

    /**
     * Convert a string like "testFooBar" into the underline style (like "test_foo_bar")
     * 
     * @param string $string String to convert
     *  
     * @return string
     * @access public
     * @since  3.0
     */
    public static function convertFromCamelCase($string)
    {
        return str_replace(self::$to, self::$from, lcfirst(strval($string)));
    }

    /**
     * Prepare method name 
     * 
     * @param string $string Underline-style string
     *  
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function prepareMethodName($string)
    {
        return str_replace(self::$from, self::$to, strval($string));
    }

    /**
     * Compose controller class name using target
     * 
     * @param string $target Current target
     *  
     * @return string
     * @access public
     * @since  1.0.0
     */
    public static function getControllerClass($target)
    {
        if (\XLite\Core\Request::getInstance()->isCLI()) {
            $zone = 'Console';

        } elseif (\XLite::isAdminZone()) {
            $zone = 'Admin';

        } else {
            $zone = 'Customer';
        }

        return '\XLite\Controller\\' 
               . $zone
               . (empty($target) ? '' : '\\' . self::convertToCamelCase($target));
    }

    /**
     * Compose URL from target, action and additional params
     *
     * @param string $target    Page identifier OPTIONAL
     * @param string $action    Action to perform OPTIONAL
     * @param array  $params    Additional params
     * @param string $interface Interface script OPTIONAL
     *
     * @return string
     * @access public
     * @since  3.0.0
     */
    public static function buildURL($target = '', $action = '', array $params = array(), $interface = null)
    {
        $url = isset($interface) ? $interface : \XLite::getInstance()->getScript();

        $parts = array();

        if ($target) {
            $parts[] = 'target=' . $target;
        }

        if ($action) {
            $parts[] = 'action=' . $action;
        }

        if ($params) {
            $parts[] = http_build_query($params);
        }

        if ($parts) {
            $url .= '?' . implode('&', $parts);
        }

        return $url;
    }

    /**
     * Compose full URL from target, action and additional params
     *
     * @param string $target Page identifier OPTIONAL
     * @param string $action Action to perform OPTIONAL
     * @param array  $params Additional params
     *
     * @return string
     * @access public
     * @since  3.0
     */
    public static function buildFullURL($target = '', $action = '', array $params = array())
    {
        return \XLite::getInstance()->getShopUrl(static::buildURL($target, $action, $params));
    }

    /**
     * Return array schema 
     * 
     * @param array $keys   Keys list
     * @param array $values Values list
     *  
     * @return array
     * @access public
     * @since  3.0.0
     */
    public static function getArraySchema(array $keys = array(), array $values = array())
    {
        return array_combine($keys, $values);
    }

    /**
     * Convert to one-dimensional array 
     * 
     * @param array  $data    Array to flat
     * @param string $currKey Parameter for recursive calls OPTIONAL
     *  
     * @return array
     * @access public
     * @since  3.0.0
     */
    public static function convertTreeToFlatArray(array $data, $currKey = '')
    {
        $result = array();

        foreach ($data as $key => $value) {
            $key = $currKey . (empty($currKey) ? $key : '[' . $key . ']');
            $result += is_array($value) ? self::convertTreeToFlatArray($value, $key) : array($key => $value);
        }

        return $result;
    }

    /**
     * Generate random token (32 chars)
     * 
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function generateRandomToken()
    {
        return md5(microtime(true) + rand(0, 1000000));
    }

    /**
     * Resize image by width / height limits
     * 
     * @param \XLite\Model\Base\Image $image  Image
     * @param integer                 $width  Width limit OPTIONAL
     * @param integer                 $height Height limit OPTIONAL
     *  
     * @return array (new width + new height + image body)
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function resizeImageSoft(\XLite\Model\Base\Image $image, $width = null, $height = null)
    {
        list($newWidth, $newHeight) = self::getCroppedDimensions(
            $image->getWidth(),
            $image->getHeight(),
            $width,
            $height
        );

        $result = array($newWidth, $newHeight, null);

        if (
            self::isGDEnabled()
            && ($newWidth != $image->getWidth() || $newHeight != $image->getHeight())
        ) {
            $image = self::resizeImage($image, $newWidth, $newHeight);

            if ($image) {
                $result[2] = $image;
            }
        }

        return $result;
    }

    /**
     * Resize image 
     * 
     * @param \XLite\Model\Base\Image $image  Image
     * @param integer                 $width  New width
     * @param integer                 $height New height
     *  
     * @return string|boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function resizeImage(\XLite\Model\Base\Image $image, $width, $height)
    {
        static $types = array(
            'image/jpeg' => 'jpeg',
            'image/jpg'  => 'jpeg',
            'image/gif'  => 'gif',
            'image/xpm'  => 'xpm',
            'image/gd'   => 'gd',
            'image/gd2'  => 'gd2',
            'image/wbmp' => 'wbmp',
            'image/bmp'  => 'wbmp',
        );

        $type = $image->getMime();

        $result = false;

        if (isset($types[$type]) && $image->getBody()) {

            $type = $types[$type];

            // start: Current resizing method: using GD functions

            $func = 'imagecreatefrom' . $type;
            if (function_exists($func)) {

                $data = $image->getBody();

                $fn = tempnam(LC_TMP_DIR, 'image');

                file_put_contents($fn, $data);
                unset($data);

                // $func is assembled from 'imagecreatefrom' + image type
                $imageResource = $func($fn);
                unlink($fn);

                if ($imageResource) {

                    $newImage = imagecreatetruecolor($width, $height);
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);

                    $res = imagecopyresampled(
                        $newImage,
                        $imageResource,
                        0,
                        0,  
                        0,
                        0,
                        $width,
                        $height,
                        $image->getWidth(),
                        $image->getHeight()
                    );
                    imagedestroy($imageResource);

                    if (\XLite::getInstance()->getOptions(array('images', 'unsharp_mask_filter_on_resize'))) {
                        
                        require_once LC_LIB_DIR . 'phpunsharpmask.php';

                        $unsharpImage = UnsharpMask($newImage);
                        if ($unsharpImage) {
                            $newImage = $unsharpImage;
                        }
                    }

                    $func = 'image' . $type;

                    ob_start();
                    // $func is assembled from 'image' + image type
                    $result = $func($newImage);
                    $image = ob_get_contents();
                    ob_end_clean();
                    imagedestroy($newImage);

                    $result = $result ? $image : false;
                }
            }

            // end: Current resizing method: using GD functions

            /*
            // start: Resizing method using PHPThumb() library
            // http://phpthumb.sourceforge.net/
            // TODO: implement complete integration or remove the code

            include_once LC_LIB_DIR . 'PHPThumb' . LC_DS . 'phpthumb.class.php';
            include_once LC_LIB_DIR . 'PHPThumb' . LC_DS . 'phpThumb.config.php';

            $pt = new \phpThumb();

            foreach ($PHPTHUMB_CONFIG as $key => $value) {
                $keyname = 'config_' . $key;
                $pt->setParameter($keyname, $value);
                if (!eregi('password|mysql', $key)) {
                    $pt->DebugMessage(
                        'setParameter(' . $keyname . ', ' . $pt->phpThumbDebugVarDump($value) . ')',
                        __FILE__,
                        __LINE__
                    );
                }
            }

            $pt->rawImageData = $image->body;
            $pt->setParameter('w', $width);
            $pt->setParameter('h', $heigth);
            $pt->fltr[] = 'usm|80|0.5|3';
            $pt->GenerateThumbnail();
            $pt->RenderOutput();

            $result = $pt->outputImageData;
            
            // end: Resizing method using PHPThumb() library
            */

        }

        return $result;
    }

    /**
     * Check - is GDlib enabled or not
     * 
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function isGDEnabled()
    {
        return function_exists('imagecreatefromjpeg')
            && function_exists('imagecreatetruecolor')
            && function_exists('imagealphablending')
            && function_exists('imagesavealpha')
            && function_exists('imagecopyresampled');
    }

    /**
     * Get cropped dimensions 
     * 
     * @param integer $w    Original width
     * @param integer $h    Original height
     * @param integer $maxw Maximum width
     * @param integer $maxh Maximum height
     *  
     * @return array (new width & height)
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getCroppedDimensions($w, $h, $maxw, $maxh)
    {
        $maxw = max(0, intval($maxw));
        $maxh = max(0, intval($maxh));

        $properties = array(
            'width'  => 0 < $w ? $w : $maxw,
            'height' => 0 < $h ? $h : $maxh,
        );

        if (0 < $w && 0 < $h && (0 < $maxw || 0 < $maxh)) {

            if (0 < $maxw && 0 < $maxh) {
                $kw = $w > $maxw ? $maxw / $w : 1;
                $kh = $h > $maxh ? $maxh / $h : 1;
                $k = $kw < $kh ? $kw : $kh;

            } elseif (0 < $maxw) {
                $k = $w > $maxw ? $maxw / $w : 1;

            } elseif (0 < $maxh) {
                $k = $h > $maxh ? $maxh / $h : 1;

            }

            $properties['width'] = max(1, round($k * $w, 0));
            $properties['height'] = max(1, round($k * $h, 0));
        }

        if (0 == $properties['width']) {
            $properties['width'] = null;
        }

        if (0 == $properties['height']) {
            $properties['height'] = null;
        }

        return array($properties['width'], $properties['height']);
    }

    /**
     * Check if specified string is URL or not
     * 
     * @param string $url URL
     *  
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function isURL($url)
    {
        static $pattern = '(?:([a-z][a-z0-9\*\-\.]*):\/\/(?:(?:(?:[\w\.\-\+!$&\'\(\)*\+,;=]|%[0-9a-f]{2})+:)*(?:[\w\.\-\+%!$&\'\(\)*\+,;=]|%[0-9a-f]{2})+@)?(?:(?:[a-z0-9\-\.]|%[0-9a-f]{2})+|(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\]))(?::[0-9]+)?(?:[\/|\?](?:[\w#!:\.\?\+=&@!$\'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})*)?)';

        return is_string($url) && 0 < preg_match('/^' . $pattern . '$/Ss', $url);
    }

    /**
     * Return class name without backslashes
     * 
     * @param \XLite_Base $obj Object to get class name from
     *  
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getPlainClassName(\XLite\Base $obj)
    {
        return str_replace('\\', '', get_class($obj));
    }

    /**
     * Format currency value
     * 
     * @param mixed $price Currency unformatted value
     *  
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function formatCurrency($price)
    {
        if (isset($price)) {
            $config = \XLite\Core\Config::getInstance();
            $price = number_format(
                doubleval($price),
                2,
                $config->General->decimal_delim,
                $config->General->thousand_delim
            );
        }

        return $price;
    }

    /**
     * Format price value
     * 
     * @param mixed $price Price
     *  
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function formatPrice($price)
    {
        if (isset($price)) {
            $config = \XLite\Core\Config::getInstance();
            $price = sprintf(
                $config->General->price_format,
                number_format(doubleval($price), 2, $config->General->decimal_delim, $config->General->thousand_delim)
            );
        }

        return $price;
    }

    /**
     * Convert value from one to other weight units 
     * 
     * @param float  $value   Weight value
     * @param string $srcUnit Source weight unit
     * @param string $dstUnit Destination weight unit
     *  
     * @return float
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function convertWeightUnits($value, $srcUnit, $dstUnit)
    {
        $unitsInGrams = array(
            'lbs' => 453.59,
            'oz'  => 28.35,
            'kg'  => 1000,
            'g'   => 1,
        );

        $multiplier = $unitsInGrams[$srcUnit] / $unitsInGrams[$dstUnit];

        return $value * $multiplier;
    }

    /**
     * Format time 
     * 
     * @param integer $base   UNIX time stamp
     * @param string  $format Format string
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function formatTime($base, $format = null)
    {
        if (!$format) {
            $config = \XLite\Core\Config::getInstance();
            $format = $config->General->date_format . ', ' . $config->General->time_format;
        }

        return strftime($format, $base);
    }

    /**
     * Format date 
     * 
     * @param integer $base   UNIX time stamp
     * @param string  $format Format string
     *  
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function formatDate($base, $format = null)
    {
        if (!$format) {
            $format = \XLite\Core\Config::getInstance()->General->date_format;
        }

        return strftime($format, $base);
    }

}
