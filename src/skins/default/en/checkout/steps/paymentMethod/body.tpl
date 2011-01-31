{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Payment / shipping methods step
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}

<div class="shipping-estimator checkout-shipping-estimator">
  <widget class="\XLite\View\Form\Checkout\ShippingMethod" name="shipping_method" />
    <widget template="shopping_cart/delivery.tpl">
  <widget name="shipping_method" end />
</div>

<widget class="\XLite\View\Form\Checkout\PaymentMethod" name="payment_method_form" className="payment-methods" />

  <widget template="{getDir()}/formContent.tpl" />
  <widget template="checkout/totals.tpl" />

  <div class="checkout-button-row">
    <widget class="\XLite\View\Button\Submit" label="Continue..." />
  </div>

<widget name="payment_method_form" end />
