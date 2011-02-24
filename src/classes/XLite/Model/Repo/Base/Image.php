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
 * PHP version 5.3.0
 *
 * @category  LiteCommerce
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     3.0.0
 */

namespace XLite\Model\Repo\Base;

/**
 * Image abstract repository
 * 
 * @see   ____class_see____
 * @since 3.0.0
 */
abstract class Image extends \XLite\Model\Repo\ARepo
{
    /**
     * Get storage name 
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    abstract public function getStorageName();

    /**
     * Get file system images storage root path
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getFileSystemRoot()
    {
        return LC_IMAGES_DIR . $this->getStorageName() . LC_DS;
    }

    /**
     * Get web images storage root path
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getWebRoot()
    {
        return \XLite::getInstance()->getShopURL(LC_IMAGES_URL . '/' . $this->getStorageName()) . '/';
    }

    /**
     * Get file system images cache storage root path
     * 
     * @param string $sizeName Image size cell name
     *  
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getFileSystemCacheRoot($sizeName)
    {
        return LC_IMAGES_CACHE_DIR . $this->getStorageName() . LC_DS . $sizeName . LC_DS;
    }

    /**
     * Get web images cache storage root path
     * 
     * @param string $sizeName Image size cell name
     *  
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getWebCacheRoot($sizeName)
    {
        return \XLite::getInstance()->getShopURL(LC_IMAGES_CACHE_URL . '/' . $this->getStorageName() . '/' . $sizeName);
    }

    /**
     * Check - store remote image into local file system or not
     * 
     * @return boolean
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function isStoreRemoteImage()
    {
        return false;
    }

    /**
     * Check - check image hash in Custoemr front-end or not
     * 
     * @return boolean
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function isCheckImage()
    {
        return false;
    }
}
