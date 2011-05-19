{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Invoice payment and shipping methods
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 * @ListChild (list="invoice.bottom", weight="20")
 *}
<td class="payment">
  <strong>{t(#Payment method#)}:</strong>
  {order.paymentMethod.name:h}
</td>

<td class="shipping">
  <strong>{t(#Shipping method#)}:</strong>
  {if:order.getShippingMethod()}{order.shippingMethod.name:h}{else:}n/a{end:}
</td>
