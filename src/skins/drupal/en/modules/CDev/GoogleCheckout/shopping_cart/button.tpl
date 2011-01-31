{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Shopping cart button
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<div class="alt-checkout-button google-checkout-button">
  <p>- or use -</p>
  <a href="{buildUrl(#gcheckout#,#checkout#)}" IF="isGoogleAllowPay()"><img src="{googleCheckoutButtonUrl}" alt="Google Checkout" /></a>
  <img src="{getGoogleCheckoutButtonUrl()}" width="160" height="43" alt="Google Checkout" class="disabled" IF="isGoogleAllowPay()" />
</div>
