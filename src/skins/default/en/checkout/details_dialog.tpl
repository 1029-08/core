{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * ____file_title____
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<table border=0>
<tr><td colspan="2"><b>Customer information:</b><hr></td></tr>

<tr><td>E-mail:</td><td>{cart.profile.login}</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>
<table border=0 width=80%>
<tr><td valign=top>
<table border=0>
<tr><td colspan="2"><b>Billing information:</b><hr></td></tr>

<tr><td nowrap>First Name:</td><td>{cart.profile.billing_firstname}</td></tr>
<tr><td nowrap>Last Name:</td><td>{cart.profile.billing_lastname}</td></tr>
<tr><td nowrap>Phone:</td><td>{cart.profile.billing_phone}</td></tr>
<tr><td nowrap>Fax:</td><td>{cart.profile.billing_fax}</td></tr>
<tr><td nowrap>Address:</td><td>{cart.profile.billing_address}</td></tr>
<tr><td nowrap>City:</td><td>{cart.profile.billing_city}</td></tr>
<tr><td nowrap>State:</td><td>{cart.profile.billingState.state}</td></tr>
<tr><td nowrap>Country:</td><td>{cart.profile.billingCountry.country}</td></tr>
<tr><td nowrap>Zip code:</td><td>{cart.profile.billing_zipcode}</td></tr>
</table></td>
<td width=10></td><td valign=top>
<table border=0>
<tr> <td colspan="2"><b>Shipping Information:</b><hr></td></tr>

<tr><td nowrap>First Name:</td><td>{cart.profile.shipping_firstname}</td></tr>
<tr><td nowrap>Last Name:</td><td>{cart.profile.shipping_lastname}</td></tr>
<tr><td nowrap>Phone:</td><td>{cart.profile.shipping_phone}</td></tr>
<tr><td nowrap>Fax:</td><td>{cart.profile.shipping_fax}</td></tr>
<tr><td nowrap>Address:</td><td>{cart.profile.shipping_address}</td></tr>
<tr><td nowrap>City:</td><td>{cart.profile.shipping_city}</td></tr>
<tr><td nowrap>State:</td><td>{cart.profile.shippingState.state}</td></tr>
<tr><td nowrap>Country:</td><td>{cart.profile.shippingCountry.country}</td></tr>
<tr><td nowrap>Zip code:</td><td>{cart.profile.shipping_zipcode}</td></tr>
</table></td>
</tr>
</table>

<p /><widget class="\XLite\View\Button\Link" label="Modify address information" location="{buildURL(#checkout#,##,_ARRAY_(#mode#^#register#))}" />
<p /><widget class="\XLite\View\Button\Link" label="Change payment method" location="{buildURL(#checkout#,##,_ARRAY_(#mode#^#paymentMethod#))}" />

<p>
<widget template="checkout/credit_card.tpl" IF="{cart.paymentMethod.formTemplate=#checkout/credit_card.tpl#}">
<widget template="checkout/echeck.tpl" IF="{cart.paymentMethod.formTemplate=#checkout/echeck.tpl#}">
<widget template="checkout/offline.tpl" IF="{cart.paymentMethod.formTemplate=#checkout/offline.tpl#}">
<widget module="CDev\ePDQ" template="modules/CDev/ePDQ/checkout.tpl" IF="{cart.paymentMethod.formTemplate=#modules/ePDQ/checkout.tpl#}">
<widget module="CDev\WorldPay" template="modules/CDev/WorldPay/checkout.tpl" IF="{cart.paymentMethod.formTemplate=#modules/WorldPay/checkout.tpl#}">
<widget module="CDev\GiftCertificates" template="modules/CDev/GiftCertificates/checkout.tpl" IF="{cart.paymentMethod.formTemplate=#modules/GiftCertificates/checkout.tpl#}">
<widget module="CDev\Promotion" template="modules/CDev/Promotion/checkout.tpl" IF="{cart.paymentMethod.formTemplate=#modules/Promotion/checkout.tpl#}">
<widget module="CDev\TwoCheckoutCom" template="modules/CDev/TwoCheckoutCom/checkout.tpl" IF="{cart.paymentMethod.formTemplate=#modules/TwoCheckoutCom/checkout.tpl#}">
<widget module="CDev\Nochex" template="modules/CDev/Nochex/checkout.tpl" IF="{cart.paymentMethod.formTemplate=#modules/Nochex/checkout.tpl#}">
<widget module="CDev\PayPalPro" template="modules/CDev/PayPalPro/standard_checkout.tpl" IF="{cart.paymentMethod.params.solution=#standard#}">
<widget module="CDev\PayPalPro" template="modules/CDev/PayPalPro/express_checkout.tpl" IF="{cart.paymentMethod.payment_method=#paypalpro_express#}">
<widget module="CDev\SecureTrading" template="modules/CDev/SecureTrading/checkout.tpl" IF="{cart.paymentMethod.formTemplate=#modules/SecureTrading/checkout.tpl#}">
<widget module="CDev\ChronoPay" template="modules/CDev/ChronoPay/checkout.tpl" IF="{cart.paymentMethod.formTemplate=#modules/ChronoPay/checkout.tpl#}">
<widget module="CDev\PayFlowLink" template="modules/CDev/PayFlowLink/checkout.tpl" IF="{cart.paymentMethod.formTemplate=#modules/PayFlowLink/checkout.tpl#}">
<widget module="CDev\GoogleCheckout" template="modules/CDev/GoogleCheckout/google_checkout.tpl" IF="{cart.paymentMethod.payment_method=#google_checkout#}">
<!-- PAYMENT METHOD FORM -->
