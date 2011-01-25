{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Delivery methods block
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<div IF="xlite.session.ups_failed_items&cart.shippingMethod.class=#ups#" class="shipping-warning">
United Parcel Service carrier is unavailable. One or more items added to cart exceed the size or the weight limit of the container. Please contact the <a href="mailto:{config.Company.site_administrator:h}">store administrator</a>.
</div>

<div IF="xlite.session.ups_rates_error&cart.shippingMethod.class=#ups#" class="shipping-warning"> 
United Parcel Service return error: ({xlite.session.ups_rates_error})<br />
Please contact the <a href="mailto:{config.Company.site_administrator:h}">store administrator</a>.
</div>

<div IF="cart.shippingAvailable&cart.shipped&cart.getCarriers()" class="carriers">
  <select name="carrier" onchange="javascript: this.form.submit();">
    <option FOREACH="cart.getCarriers(),key,carrier" value="{key}" selected="{cart.isSelected(#carrier#,key)}">{carrier:h}</option>
  </select>
</div>

<ul IF="cart.shippingAvailable&cart.shipped&cart.getCarrierRates()" class="deliveries">
  {foreach:cart.getCarrierRates(),rate}
  <li {if:cart.shipping_id=rate.shipping.shipping_id} class="selected"{end:}>
    <input type="radio" id="shipping_{rate.shipping.shipping_id}" name="shipping" value="{rate.shipping.shipping_id}" checked="{cart.isSelected(#shipping_id#,rate.shipping.shipping_id)}" />
    <label for="shipping_{rate.shipping.shipping_id}">{rate.shipping.name:h}</label>
    <span>{price_format(rate,#rate#):h}</span>
  </li>
  {end:}
</ul>

<widget template="modules/CDev/UPSOnlineTools/notice.tpl" IF="cart.shippingMethod.class=#ups#" />
