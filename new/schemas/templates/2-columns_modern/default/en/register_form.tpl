<widget template="js/select_states_begin_js.tpl">

<script type="text/javascript" language="JavaScript 1.2" src="skins/default/en/js/billing_shipping.js"></script>

<div IF="!target=#checkout#">
<p align=justify>
To create a customer account complete the form below and click on the 'Submit' button.
<br>
If you are a registered customer, you can <a href="cart.php?target=profile&amp;mode=login&amp;returnUrl={dialog.backUrl:u}"><u>login here</u></a>.
</p>
</div>
<div IF="target=#checkout#">
<p align=justify>
In order to perform the checkout we need to know your e-mail address and billing and shipping information. Complete the form below and click on the 'Submit' button to proceed. <span  IF="allowAnonymous">If you would like to create a customer account at our store, do not omit the password fields.</span>
<br>
If you are a registered customer, please <a href="cart.php?target=profile&amp;mode=login&amp;returnUrl={dialog.backUrl:u}"><u>log into your account</u></a> before you proceed with the checkout process.
</p>
</div>

<p align=justify>Fields marked with <font class="Star">*</font> are mandatory.</p>

<br>
<span IF="userExists" class="ErrorMessage">&gt;&gt;&nbsp;The user is already registered! Please select another e-mail.&nbsp;&lt;&lt;</span>
<form action="cart.php" method="post" name="registration_form">
<table width="100%" border="0" cellspacing="0" cellpadding="2">

<tr valign="middle">
    <td colspan="4"><b>E-mail &amp; Password</b><br><hr size="1" noshade></td>
</tr>
<tr valign="middle">
    <td align="right" width="150">E-mail</td>
    <td width="10"><font class="Star">*</font></td>
    <td width="150">
        <input type="text" name="login" value="{login:r}" size="32" maxlength="128">
    </td>
    <td>
        <widget class="CEmailValidator" field="login">
    </td>
</tr>
<tr valign="middle" IF="allowAnonymous">
    <td colspan="2">&nbsp;</td>
    <td colspan="2">
		Please leave the password fields empty<br>if you do not want to create a personal account with our shop.
    </td>
</tr>
<tr valign="middle">
    <td align="right">Password</td>
    <td><font class="Star">*</font></td>
    <td>
        <input type="password" name="password" value="{password:r}" size="32" maxlength="128">
    </td>
    <td>
        &nbsp;
        <widget class="CRequiredValidator" field="password" visible="{!allowAnonymous}">
    </td>
</tr>
<tr valign="middle">
    <td align="right">Confirm password</td>
    <td><font class="Star">*</font></td>
    <td>
        <input type="password" name="confirm_password" value="{confirm_password:r}" size="32" maxlength="128">
    </td>
    <td>
        &nbsp;
        <widget class="CRequiredValidator" field="confirm_password" visible="{!allowAnonymous}">
        <widget class="CPasswordValidator" field="confirm_password" passwordField="password" visible="{!allowAnonymous}">
    </td>
</tr>
<tr>
    <td colspan="4">&nbsp;</td>
</tr>

<!-- ********************************* BILLING ADDRESS ********************************* -->

<tr valign="middle">
    <td colspan="4"><b>Billing Address</b><br><hr size="1" noshade></td>
</tr>
<tr valign="middle">
    <td align="right">Title</td>
    <td>&nbsp;</td>
    <td>
        <select name="billing_title">
            <option value="Mr." selected="{billing_title=#Mr.#}">Mr.</option>
            <option value="Mrs." selected="{billing_title=#Mrs.#}">Mrs.</option>
            <option value="Ms." selected="{billing_title=#Ms.#}">Ms.</option>
        </select>
    </td>
    <td>
    </td>
</tr>
<tr valign="middle">
    <td align="right">First Name</td>
    <td><font class="Star">*</font></td>
    <td>
        <input type="text" name="billing_firstname" value="{billing_firstname:r}" size="32" maxlength="128">
    </td>
    <td>
        <widget class="CRequiredValidator" field="billing_firstname">
    </td>
</tr>
<tr valign="middle">
    <td align="right">Last Name</td>
    <td><font class="Star">*</font></td>
    <td>
        <input type="text" name="billing_lastname" value="{billing_lastname:r}" size="32" maxlength="128">
    </td>
    <td align="left">
        <widget class="CRequiredValidator" field="billing_lastname">
    </td>
</tr>
<tr valign="middle">
    <td align="right">Company</td>
    <td>&nbsp;</td>
    <td>
        <input type="text" name="billing_company" value="{billing_company:r}" size="32" maxlength="255">
    </td>
    <td>
    </td>
</tr>
<tr valign="middle">
    <td align="right">Phone</td>
    <td><font class="Star">*</font></td>
    <td>
        <input type="text" name="billing_phone" value="{billing_phone:r}" size="32" maxlength="32">
    </td>
    <td>
        <widget class="CRequiredValidator" field="billing_phone">
    </td>
</tr>
<tr valign="middle">
    <td align="right">Fax</td>
    <td>&nbsp;</td>
    <td>
        <input type="text" name="billing_fax" value="{billing_fax:r}" size="32" maxlength="32">
    </td>
    <td>
    </td>
</tr>
<tr valign="middle">
    <td align="right">Address</td>
    <td><font class="Star">*</font></td>
    <td>
        <input type="text" name="billing_address" value="{billing_address:r}" size="32" maxlength="64">
    </td>
    <td>
        <widget class="CRequiredValidator" field="billing_address">
    </td>
</tr>
<tr valign="middle">
    <td align="right">City</td>
    <td><font class="Star">*</font></td>
    <td>
        <input type="text" name="billing_city" value="{billing_city:r}" size="32" maxlength="64">
    </td>
    <td>
        <widget class="CRequiredValidator" field="billing_city">
    </td>
</tr>
<tr valign="middle">
    <td align="right">State</td>
    <td><font class="Star">*</font></td>
    <td>
		<widget class="CStateSelect" field="billing_state" onChange="javascript: changeState(this, 'billing');" fieldId="billing_state_select">
    </td>
    <td>
        <widget class="CRequiredValidator" field="billing_state">
        <widget class="CStateValidator" field="billing_state" countryField="billing_country">
    </td>
</tr>
<tr valign="middle" id="billing_custom_state_body">
	<td align="right">Other state (specify)</td>
	<td>&nbsp;</td>
	<td><input type="text" name="billing_custom_state" value="{billing_custom_state:r}" size="32" maxlength="64">
	</td>
	<td>
		&nbsp;
	</td>
</tr>
<tr valign="middle">
    <td align="right">Country</td>
    <td><font class="Star">*</font></td>
    <td>
		<widget class="CCountrySelect" field="billing_country" onChange="javascript: populateStates(this,'billing');" fieldId="billing_country_select">
    </td>
    <td>
        <widget class="CRequiredValidator" field="billing_country">
    </td>
</tr>
<tr valign="middle">
    <td align="right">Zip code</td>
    <td><font class="Star">*</font></td>
    <td nowrap>
        <input type="text" name="billing_zipcode" value="{billing_zipcode:r}" size="32" maxlength="32">
    </td>
    <td>
        <widget class="CRequiredValidator" field="billing_zipcode">
    </td>
</tr>

<tr>
    <td colspan="4">&nbsp;</td>
</tr>

<!-- ********************************* SHIPPING ADDRESS ********************************* -->

<tr valign="middle">
    <td colspan="4"><b>Shipping Address (leave empty if same as billing address)</b><br><hr size="1" noshade><widget class="CButton" label="Copy Billing Info" href="javascript: copyBillingInfo(document.registration_form);"></td>
</tr>
<tr valign="middle">
    <td align="right">Title</td>
    <td>&nbsp;</td>
    <td>
        <select name="shipping_title">
            <option value="Mr." selected="{shipping_title=#Mr.#}">Mr.</option>
            <option value="Mrs." selected="{shipping_title=#Mrs.#}">Mrs.</option>
            <option value="Ms." selected="{shipping_title=#Ms.#}">Ms.</option>
        </select>
    </td>
    <td>
    </td>
</tr>
<tr valign="middle">
    <td align="right">First Name</td>
    <td><font class="Star">*</font></td>
    <td>
        <input type="text" name="shipping_firstname" value="{shipping_firstname:r}" size="32" maxlength="128">
    </td>
    <td>
    </td>
</tr>
<tr valign="middle">
    <td align="right">Last Name</td>
    <td><font class="Star">*</font></td>
    <td>
        <input type="text" name="shipping_lastname" value="{shipping_lastname:r}" size="32" maxlength="128">
    </td>
    <td align="left">
    </td>
</tr>
<tr valign="middle">
    <td align="right">Company</td>
    <td>&nbsp;</td>
    <td>
        <input type="text" name="shipping_company" value="{shipping_company:r}" size="32" maxlength="255">
    </td>        
    <td>
    </td>
</tr>
<tr valign="middle">
    <td align="right">Phone</td>
    <td><font class="Star">*</font></td>
    <td>
        <input type="text" name="shipping_phone" value="{shipping_phone:r}" size="32" maxlength="32">
    </td>
    <td>
    </td>
</tr>
<tr valign="middle">
    <td align="right">Fax</td>
    <td>&nbsp;</td>
    <td>
        <input type="text" name="shipping_fax" value="{shipping_fax:r}" size="32" maxlength="32">
    </td>
    <td>
    </td>
</tr>
<tr valign="middle">
    <td align="right">Address</td>
    <td><font class="Star">*</font></td>
    <td>
        <input type="text" name="shipping_address" value="{shipping_address:r}" size="32" maxlength="64">
    </td>
    <td>
    </td>
</tr>
<tr valign="middle">
    <td align="right">City</td>
    <td><font class="Star">*</font></td>
    <td>
        <input type="text" name="shipping_city" value="{shipping_city:r}" size="32" maxlength="64">
    </td>
    <td>
    </td>
</tr>
<tr valign="middle">
    <td align="right">State</td>
    <td><font class="Star">*</font></td>
    <td>
		<widget class="CStateSelect" field="shipping_state" value="{shipping_state}" onChange="javascript: changeState(this, 'shipping');" fieldId="shipping_state_select">
    </td>
    <td>
        <widget class="CStateValidator" field="shipping_state" countryField="shipping_country">
    </td>
</tr>
<tr valign="middle" id="shipping_custom_state_body">
	<td align="right">Other state (specify)</td>
	<td>&nbsp;</td>
	<td><input type="text" name="shipping_custom_state" value="{shipping_custom_state:r}" size="32" maxlength="64">
	</td>
	<td>
		&nbsp;
	</td>
</tr>
<tr valign="middle">
    <td align="right">Country</td>
    <td><font class="Star">*</font></td>
    <td>
		<widget class="CCountrySelect" field="shipping_country" onChange="javascript: populateStates(this,'shipping');" fieldId="shipping_country_select">
    </td>
    <td>
    </td>
</tr>
<tr valign="middle">
    <td align="right">Zip code</td>
    <td><font class="Star">*</font></td>
    <td nowrap>
        <input type="text" name="shipping_zipcode" value="{shipping_zipcode:r}" size="32" maxlength="32">
    </td>
    <td>
    </td>
</tr>

<widget module="UPSOnlineTools" template="modules/UPSOnlineTools/notice_register.tpl">

<tbody IF="showMembership">
<tr>
    <td colspan="4">&nbsp;</td>
</tr>
<tr valign="middle">
    <td colspan="4"><b>Membership</b><br><hr size="1" noshade></td>
</tr>
<tr valign="middle">
    <td align="right">Request membership</td>
    <td>&nbsp;</td>
    <td><widget class="CMembershipSelect" field="pending_membership">
	</td>
</tr>
</tbody>

{*extraFields*}
<widget module="WholesaleTrading" template="modules/WholesaleTrading/profile_form.tpl">
<widget module="Newsletters" template="modules/Newsletters/subscription_form.tpl">

</table>

<br>
<div IF="GDLibLoaded">
<widget template="common/spambot_arrest.tpl" id="on_register"></div>

<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr valign="middle">
    <td width="160">&nbsp;</td>
    <td >
        By clicking "SUBMIT" you agree with our <a href="cart.php?target=help&amp;mode=terms_conditions" style="TEXT-DECORATION: underline" target="_blank">Terms &amp; Conditions.</a><br>
        <br>
        <input type="hidden" foreach="dialog.allparams,param,v" name="{param}" value="{v}"/>
        <input type="hidden" name="action" value="{mode}">
        <widget class="CSubmit" href="javascript: document.registration_form.submit()">
        <br>
    </td>
</tr>
</table>
</form>

<widget template="js/select_states_end_js.tpl">
