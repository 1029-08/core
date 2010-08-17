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
 * @subpackage Model
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Model;

/**
 * Cart 
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 * @Entity
 */
class Cart extends \XLite\Model\Order
{
    /**
     * Array of instances for all derived classes
     *
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0
     */
    protected static $instances = array();

    /**
     * Method to access a singleton
     *
     * @return \XLite\Model\Cart
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getInstance()
    {
        $className = get_called_class();

        // Create new instance of the object (if it is not already created)
        if (!isset(static::$instances[$className])) {
            $orderId = \XLite\Model\Session::getInstance()->get('order_id');

            if ($orderId) {
                $cart = \XLite\Core\Database::getRepo('XLite\Model\Cart')->find($orderId);
                if ($cart && self::TEMPORARY_STATUS != $cart->getStatus()) {
                    \XLite\Model\Session::getInstance()->set('order_id', 0);
                    $cart = null;
                }
            }

            if (!isset($cart)) {
                $cart = new $className();
                $cart->setStatus(self::TEMPORARY_STATUS);
                $cart->setProfileId(0);
            }

            static::$instances[$className] = $cart;

            $auth = \XLite\Model\Auth::getInstance();

            if ($auth->isLogged()) {
                if ($auth->getProfile()->get('profile_id') != $cart->getProfileId()) {
                    $cart->setProfile($auth->getProfile());
                    $cart->calculateTotals();
                }


            } elseif ($cart->getProfileId()) {

                $cart->setProfile(null);
                $cart->calculateTotals();
            }

            \XLite\Core\Database::getEM()->persist($cart);
            \XLite\Core\Database::getEM()->flush();

            \XLite\Model\Session::getInstance()->set('order_id', $cart->getOrderId());

        }

        return static::$instances[$className];

    }

    /**
     * Prepare order before save data operation
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     * @PrePersist
     * @PreUpdate
     */
    protected function prepareBeforeSave()
    {
        parent::prepareBeforeSave();

        $this->setDate(time());

    }

    /**
     * Clear cart
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function clear()
    {
        foreach ($this->getItems() as $item) {
            \XLite\Core\Database::getEM()->remvoe($item);
        }
        $this->getItems()->clear();

        \XLite\Core\Database::getEM()->persist($this);
        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Prepare order before remove operation
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     * @PreRemove
     */
    protected function prepareBeforeRemove()
    {
        parent::prepareBeforeRemove();

        \XLite\Model\Session::getInstance()->set('order_id', null);
    }

    /**
     * Order 'complete' event
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function processCheckOut()
    {
        if ('T' == $this->getStatus()) {
            $this->setDate(time());

            $profile = \XLite\Model\Auth::getInstance()->getProfile();
            if ($profile->getOrderId()) {
                // anonymous checkout:
                // use the current profile as order profile
                $this->setProfileId($this->getProfile()->get('profile_id'));

            } else {
                $this->setProfileCopy($profile);
            }
            $this->setStatus(self::INPROGRESS_STATUS);

            \XLite\Core\Database::getEM()->persist($this);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Calculate shipping rates 
     * 
     * @return array of \XLite\Moel\ShippingRate
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function calculateShippingRates()
    {
        $rates = parent::calculateShippingRates();
        $id = $this->getShippingId();

        if (
            ($id && !isset($rates[$id]))
            || ($rates && !$id)
        ) {
            $shipping = null;
            if (0 < count($rates)) {
                list($k, $rate) = each($rates);
                reset($rates);
                $shipping = $rate->getShipping();
            }
            $this->setShippingMethod($shipping);
            $this->calculate();

            \XLite\Core\Database::getEM()->persist($this);
            \XLite\Core\Database::getEM()->flush();
        }

        return $rates;
    }

    /**
     * Calculates order totals and store them in the order properties
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     * @PostPersist
     * @PostUpdate
     */
    public function calculate()
    {
        parent::calculate();
    }

}

