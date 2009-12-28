<script type="text/javascript" language="JavaScript">
<!--
function showSettings(solution) 
{
	if (solution == "form") {
		document.getElementById('email_message').style.display = '';
		document.getElementById('xor_password').style.display = '';
		document.getElementById('direct_logo').style.display = 'none';
		document.getElementById('form_logo').style.display = '';
	} else {
		document.getElementById('email_message').style.display = 'none';
		document.getElementById('xor_password').style.display = 'none';
		document.getElementById('direct_logo').style.display = '';
		document.getElementById('form_logo').style.display = 'none';
	}
}
-->
</script>

<form action="admin.php" method="POST">
<input type="hidden" name="target" value="payment_method">
<input type="hidden" name="action" value="update">
<input type="hidden" name="payment_method" value="{dialog.pm.get(#payment_method#)}">

<table width="100%" border="0" cellpadding="4" cellspacing="0">
    <tr>
        <td colspan="3" class="DialogTitle">Select a Protx Solution:</td>
    </tr>
    <tr>
        <td align="center" rowspan="4"><img  src="images/modules/Protx/protx_logo.gif" border="0" alt="Acceptance Mark"></td>
        <td align="center"><input type="radio" name="params[solution]" value="direct" onClick="javascript: this.blur(); showSettings('direct')"  {if:isSelected(#direct#,pm.params.solution)}checked{end:}></td>
        <td><b>Protx VSP Direct</b></td>
    </tr>
    <tr>
        <td></td>
        <td>The customers enter credit card details right on the store pages during checkout. No redirecting involved.&nbsp;<a href="http://www.protx.com/products/vsp_direct.asp"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle">Solution overview</a></td>
    </tr>
    <tr>
        <td align="center"><input type="radio" name="params[solution]" value="form" onClick="javascript: this.blur(); showSettings('form')" {if:isSelected(#form#,pm.params.solution)}checked{end:}></td>
        <td><b>Protx VSP Form</b></td>
    </tr>
    <tr>
        <td></td>
        <td>The customers are redirected to Protx to enter their card details, so no sensitive information needs to be taken or stored on your site.&nbsp;<a href="http://www.protx.com/products/vsp_form.asp"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle">Solution overview</a></td>
    </tr>
    <tr>
        <td colspan="3"><hr></td>
    </tr>
</table>

<table border=0 cellspacing=2 cellpadding=1 width="100%">
	<tr>
		<td align="center"><img id="direct_logo" src="images/modules/Protx/VSPDirectlogo.gif" border="0" alt="Protx VSP Direct"><img id="form_logo" src="images/modules/Protx/VSPFormlogo.gif" border="0" alt="Protx VSP Form"></td>
		<td width="20">&nbsp;</td>
		<td>
<table border=0 cellspacing=2 cellpadding=3>
<tr>
	<td align="right">Vendor name:</td>
	<td>&nbsp;</td>
	<td><input type=text name=params[vendor_name] size=32 value="{dialog.pm.params.vendor_name}"></td>
</tr>

<tr id="xor_password">
	<td align="right">Encryption Password:</td>
	<td>&nbsp;</td>
	<td><input type=text name="params[xor_password]" size=24 value="{pm.params.xor_password:r}"></td>
</tr>

<tr>
	<td align="right">Order prefix:</td>
	<td>&nbsp;</td>
	<td><input type=text name=params[order_prefix] size=32 value="{dialog.pm.params.order_prefix}"><br><i>it mustn't be empty in test mode</i></td>
</tr>

<tr>
	<td align="right">'AUTHENTICATED'/'REGISTERED' order status:</td>
	<td>&nbsp;</td>
	<td><widget class="CStatusSelect" field="params[status_auth]" value="{dialog.pm.orderAuthStatus}"></td>
</tr>

<tr>
	<td align="right">'NOTAUTHED'/'REJECTED' order status:</td>
	<td>&nbsp;</td>
	<td><widget class="CStatusSelect" field="params[status_reject]" value="{dialog.pm.orderRejectStatus}"></td>
</tr>

<tr>
	<td align="right">'SUCCESS' order status (no 3D-secure check):</td>
	<td>&nbsp;</td>
	<td><widget class="CStatusSelect" field="params[status_success_no3d]" value="{dialog.pm.orderSuccessNo3dStatus}"></td>
</tr>

<tr>
	<td align="right">'SUCCESS' order status (3D-secure passed):</td>
	<td>&nbsp;</td>
	<td><widget class="CStatusSelect" field="params[status_success_3dok]" value="{dialog.pm.orderSuccess3dOkStatus}"></td>
</tr>

<tr>
	<td align="right">'SUCCESS' order status (3D-secure failed):</td>
	<td>&nbsp;</td>
	<td><widget class="CStatusSelect" field="params[status_success_3dfail]" value="{dialog.pm.orderSuccess3dFailStatus}"></td>
</tr>

<tr>
	<td align="right">Currency:</td>
	<td>&nbsp;</td>
	<td>
		<select name=params[currency]>
			<option value="USD" selected="{IsSelected(dialog.pm.params.currency,#USD#)}">US Dollar</option>
			<option value="GBP" selected="{IsSelected(dialog.pm.params.currency,#GBP#)}">Britain Pound</option>
			<option value="EUR" selected="{IsSelected(dialog.pm.params.currency,#EUR#)}">Euro</option>
			<option value="CAD" selected="{IsSelected(dialog.pm.params.currency,#CAD#)}">Canadian Dollar</option>
			<option value="AUD" selected="{IsSelected(dialog.pm.params.currency,#AUD#)}">Australian Dollar</option>
		</select>
	</td>
</tr>

<tr>
	<td align="right">Test/Live mode:</td>
	<td>&nbsp;</td>
	<td>
		<select name=params[testmode]>
			<option value="Y" selected="{IsSelected(dialog.pm.params.testmode,#Y#)}">test
			<option value="N" selected="{IsSelected(dialog.pm.params.testmode,#N#)}">live
		</select>
	</td>
</tr>

<tr id="email_message">
	<td align="right">E-mail Message:</td>
	<td>&nbsp;</td>
	<td><textarea rows="5" cols="50" name="params[eMailMessage]" >{pm.params.eMailMessage}</textarea></td>
</tr>

<tr>
	<td align="right">Apply AVS/CV2 checks:</td>
	<td>&nbsp;</td>
	<td>
		<select name=params[ApplyAVSCV2]>
			<option value="0" selected="{IsSelected(dialog.pm.params.ApplyAVSCV2,0)}">Allow AVS/CV2 checks, use rules</option>
			<option value="1" selected="{IsSelected(dialog.pm.params.ApplyAVSCV2,1)}">Force AVS/CV2 checks, use rules</option>
			<option value="2" selected="{IsSelected(dialog.pm.params.ApplyAVSCV2,2)}">Do not allow AVS/CV2 checks</option>
			<option value="3" selected="{IsSelected(dialog.pm.params.ApplyAVSCV2,3)}">Force AVS/CV2 checks, do not use rules</option>
		</select>
	</td>
</tr>

<tr>
	<td align="right">Apply 3DSecure  checks:</td>
	<td>&nbsp;</td>
	<td>
		<select name=params[Apply3DSecure]>
			<option value="0" selected="{IsSelected(dialog.pm.params.Apply3DSecure,0)}">Allow 3D-Secure checks, use rules</option>
			<option value="1" selected="{IsSelected(dialog.pm.params.Apply3DSecure,1)}">Force 3D-Secure checks, use rules</option>
			<option value="2" selected="{IsSelected(dialog.pm.params.Apply3DSecure,2)}">Do not allow 3D-Secure checks</option>
			<option value="3" selected="{IsSelected(dialog.pm.params.Apply3DSecure,3)}">Force 3D-Secure checks, do not use rules</option>
		</select>
	</td>
</tr>

<tr>
	<td align="right">Transaction type:</td>
	<td>&nbsp;</td>
	<td>
		<select name="params[trans_type]">
			<option value="AUTHENTICATE" selected="{dialog.pm.params.trans_type=#AUTHENTICATE#}">Authenticate</option>
			<option value="DEFERRED" selected="{dialog.pm.params.trans_type=#DEFERRED#}">Deferred</option>
			<option value="PAYMENT" selected="{dialog.pm.params.trans_type=#PAYMENT#}">Payment</option>
		</select>
	</td>
</tr>

<tr>
	<td colspan="3" height="20">&nbsp;</td>
</tr>

</table>
		</td>
	</tr>
</table>

<center>
<input type=submit value=" Update ">
</center>

</form>

<script type="text/javascript" language="JavaScript">
	showSettings('{pm.params.solution:h}');
</script>
