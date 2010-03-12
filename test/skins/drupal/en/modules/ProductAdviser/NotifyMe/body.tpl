{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Notify me box
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<widget class="XLite_Module_ProductAdviser_View_Form_Product_NotifyMe" name="notify_me" product="{getProduct()}" className="notify-me" />

  <h1 IF="isOutOfStock()">Notify me when the product is in stock</h1>
  <h1 IF="isSmallQuantity()">Notify me when the stock quantity of a product increases</h1>
  <h1 IF="isBigPrice()">Notify me when the price drops</h1>

  <table cellspacing="0">

    <tr>
      <td>Your e-mail:</td>
      <td>
        <input type="text" size="30" name="email" value="{email}" />
        <widget class="XLite_Validator_EmailValidator" field="email" />
      </td>
    </tr>

    <tr>
      <td>Your name:</td>
      <td>
        <input type="text" size="50" name="person_info" value="{xlite.auth.profile.billing_title} {xlite.auth.profile.billing_firstname} {xlite.auth.profile.billing_lastname}" />
        <span class="optional-label">optional</span>
      </td>
    </tr>

  </table>

  <div class="button-row">
    <widget class="XLite_View_Button_Submit" label="Notify me" />
  </div>

<widget name="notify_me" end />
