{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Invoice payment methods
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 * @ListChild (list="invoice.bottom.methods", weight="20")
 *}
<td class="payment" IF="order.getActivePaymentTransactions()">
  <strong>{t(#Payment method#)}:</strong>
  {foreach:order.getActivePaymentTransactions(),t}
    {t.paymentMethod.getName():h}<br />
  {end:}
</td>
