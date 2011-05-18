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
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */
namespace XLite\Model\Base;

/**
 * Order item related object interface
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
interface IOrderItem
{
    /**
     * Get unique id
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getId();

    /**
     * Get price
     *
     * @return float
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getPrice();

    /**
     * Get weight
     *
     * @return float
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getWeight();

    /**
     * Get purchase limit (minimum)
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getMinPurchaseLimit();

    /**
     * Get purchase limit (maximum)
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getMaxPurchaseLimit();

    /**
     * Get name
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getName();

    /**
     * Get SKU
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getSku();

    /**
     * Get image
     *
     * @return \XLite\Model\Base\Image|void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getImage();

    /**
     * Get free shipping
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getFreeShipping();

    /**
     * Get URL
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getURL();
}
