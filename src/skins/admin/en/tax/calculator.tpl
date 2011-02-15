{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * ____file_title____
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset={charset}">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <LINK href="skins/admin/en/style.css"  rel=stylesheet type=text/css>
</head>
<body class="popup">

<table cellpadding="3" cellspacing="3">
<form action="admin.php" method="POST">
<input type="hidden" name="target" value="taxes" />
<input type="hidden" name="action" value="calculator" />
<tr>
	<td>Country:&nbsp;</td><td><widget class="\XLite\View\CountrySelect" field="billing_country" country="{country}" /></td>
</tr>
<tr>
	<td>State:&nbsp;</td><td><widget class="\XLite\View\StateSelect" field="billing_state" state="{state}" isLinked=1 /></td>
</tr>
<tr>
	<td>City:&nbsp;</td><td><input type="text" name="city" value="{city}" /></td>
</tr>
<tr>
    <td>Zip:&nbsp;</td><td><input type="text" name="zip" value="{zip}" /></td>
</tr>
<tr>
	<td>Membership:&nbsp;</td><td><widget class="\XLite\View\MembershipSelect" template="common/select_membership.tpl" field="membership"></td>
</tr>
<tr>
	<td>Payment method:&nbsp;</td>
	<td>
		<select name="payment_method">
			<option FOREACH="xlite.factory.\XLite\Model\PaymentMethod.findAll(),payment_method" selected="{isSelected(payment_method,#name#,payment_method)}">{payment_method.name}</option>
		</select>
	</td>
</tr>
<tr>
	<td>Product class:&nbsp;</td><td><input type="text" name="product_class" value="{product_class}" /></td>
</tr>
<tr>
	<td colspan=2 align=center>
    <br /><widget class="\XLite\View\Button\Submit" label=" Calculate taxes " />
	</td>
</tr>
</form>
</table>

{if:display_taxes}
<b>Item taxes:</b><br />
<table><tr FOREACH="item_taxes,name,value"><td>{name}</td><td>{value}%</td></tr></table>
<b>Taxes on shipping:</b><br />
<table><tr FOREACH="shipping_taxes,name,value"><td>{name}</td><td>{value}%</td></tr></table>
<p>
<a href="javascript: window.close()"><img src="images/go.gif" alt="" width="13" height="13" align="absmiddle" /> Close tax calculator</a><br />
{end:}

</body>
