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
 * @subpackage Controller
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Module\CDev\UPSOnlineTools\Controller\Customer;

/**
 * Checkout controller
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Checkout extends \XLite\Controller\Customer\Checkout
implements \XLite\Base\IDecorator
{
    /**
     * Change shipping method
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionShipping()
    {
        $carrier = $this->getCart()->getCarrier();

        if (
            0 < count($this->getCart()->getCarriers())  
            && $carrier
            && isset(\XLite\Core\Request::getInstance()->carrier)
            && \XLite\Core\Request::getInstance()->carrier != $carrier
        ) {

            // Update carrier
            $rates = $this->getCart()->getCarrierRates(\XLite\Core\Request::getInstance()->carrier);
            if (!$rates) {
                \XLite\Core\Request::getInstance()->shipping = 0;

            } elseif (!isset($rates[$this->getCart()->get('shipping_id')])) {
                reset($rates);
                $rate = array_shift($rates);
                $shipping = $rate->get('shipping');
                \XLite\Core\Request::getInstance()->shipping = $shipping ? $shipping->get('shipping_id') : 0;

            }
        }

        parent::doActionShipping();
    }

    /**
     * Check if we are ready to select shipping method
     * (CHECKOUT_MODE_PAYMENT_METHOD step check) 
     * TODO: isShippingAvailable() is no more exists - rework method
     *
     * @return boolean 
     * @access protected
     * @since  3.0.0
     */
    protected function isShippingNeeded()
    {
        $result = parent::isShippingNeeded();

        if (!$result) {
            $cart = $this->getCart();
            if ($cart->isShippingAvailable() && $cart->getShippingMethod()) {
                $shippingId = $cart->getShippingMethod()->get('shipping_id');
                $rates = $cart->getCarrierRates();
                $result = !isset($rates[$shippingId]);
            }
        }

        return $result;
    }
}
