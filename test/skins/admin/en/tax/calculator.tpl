<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset={charset}">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <LINK href="skins/admin/en/style.css"  rel=stylesheet type=text/css>
</head>
<body class="PopUp" LEFTMARGIN=3 TOPMARGIN=3 RIGHTMARGIN=3 BOTTOMMARGIN=3 MARGINWIDTH=0 MARGINHEIGHT=0>

<widget template="js/select_states_begin_js.tpl">

<table border=0 cellpadding=3 cellspacing=3>
<form action="admin.php" method="POST">
<input type="hidden" name="target" value="taxes">
<input type="hidden" name="action" value="calculator">
<tr>
	<td>Country:&nbsp;</td><td><widget class="XLite_View_CountrySelect" field="billing_country" value="{country}" onChange="javascript: populateStates(this,'billing_state');" fieldId="billing_country_select"></td>
</tr>
<tr>
	<td>State:&nbsp;</td><td><widget class="XLite_View_StateSelect" field="billing_state" value="{state}" fieldId="billing_state_select"></td>
</tr>
<tr>
	<td>City:&nbsp;</td><td><input type="text" name="city" value="{city}"></td>
</tr>
<tr>
    <td>Zip:&nbsp;</td><td><input type="text" name="zip" value="{zip}"></td>
</tr>
<tr>
	<td>Membership:&nbsp;</td><td><widget class="XLite_View_MembershipSelect" template="common/select_membership.tpl" field="membership"></td>
</tr>
<tr>
	<td>Payment method:&nbsp;</td>
	<td>
		<select name="payment_method">
			<option FOREACH="xlite.factory.PaymentMethod.findAll(),payment_method" selected="{isSelected(payment_method,#name#,payment_method)}">{payment_method.name}</option>
		</select>
	</td>
</tr>
<tr>
	<td>Product class:&nbsp;</td><td><input type="text" name="product_class" value="{product_class}"></td>
</tr>
<tr>
	<td colspan=2 align=center>
    <br><input type="submit" value=" Calculate taxes ">
	</td>
</tr>
</form>
</table>

{if:display_taxes}
<b>Item taxes:</b><br>
<table><tr FOREACH="item_taxes,name,value"><td>{name}</td><td>{value}%</td></tr></table>
<b>Taxes on shipping:</b><br>
<table><tr FOREACH="shipping_taxes,name,value"><td>{name}</td><td>{value}%</td></tr></table>
<p>
<a href="javascript: window.close()"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"> Close tax calculator</a><br>
{end:}
<widget template="js/select_states_end_js.tpl">

</body>
