{* E-mail sent to admin when customer modifies his profile *}
<html>
<head><title>Profile modified</title></head>
<body>
<p>The profile was modified: {profile.login}

<p align="left">Profile information:

<p align="left">
<table border="0" cellspacing="0" cellpadding="2">

<!-- ********************** BILLING ADDRESS *************************** -->

<tr>
    <td colspan="2"><b>Billing Address</b><br><hr size="1" noshade></td>
</tr>
<tr>
    <td align="right">Title:</td>
    <td>{profile.billing_title:h}</td>
</tr>
<tr>
    <td align="right">First Name:</td>
    <td>{profile.billing_firstname:h}</td>
</tr>
<tr>
    <td align="right">Last Name:</td>
    <td>{profile.billing_lastname:h}</td>
</tr>
<tr>
    <td align="right">Company:</td>
    <td>{profile.billing_company:h}</td>        
</tr>
<tr>
    <td align="right">Phone:</td>
    <td>{profile.billing_phone:h}</td>    
</tr>
<tr>
    <td align="right">Fax:</td>
    <td>{profile.billing_fax:h}</td>
</tr>
<tr>
    <td align="right">Address:</td>
    <td>{profile.billing_address:h}</td>
</tr>
<tr>
    <td align="right">City:</td>
    <td>{profile.billing_city:h}</td>
</tr>
<tr>
    <td align="right">State:</td>
    <td>{profile.billingState.state:h}</td>
</tr>
<tr>
    <td align="right">Country:</td>
    <td>{profile.billingCountry.country:h}</td>
</tr>
<tr>
    <td align="right">Zip code:</td>
    <td>{profile.billing_zipcode:h}</td>
</tr>

<tr>
    <td colspan="2">&nbsp;</td>
</tr>

<!-- ********************* SHIPPING ADDRESS ************************** -->


<tr>
    <td colspan="2"><b>Shipping Address</b><br><hr size="1" noshade></td>
</tr>
<tr>
    <td align="right">Title:</td>
    <td>{profile.shipping_title:h}</td>
</tr>
<tr>
    <td align="right">First Name:</td>
    <td>{profile.shipping_firstname:h}</td>
</tr>
<tr>
    <td align="right">Last Name:</td>
    <td>{profile.shipping_lastname:h}</td>
</tr>
<tr>
    <td align="right">Company:</td>
    <td>{profile.shipping_company:h}</td>        
</tr>
<tr>
    <td align="right">Phone:</td>
    <td>{profile.shipping_phone:h}</td>    
</tr>
<tr>
    <td align="right">Fax:</td>
    <td>{profile.shipping_fax:h}</td>
</tr>
<tr>
    <td align="right">Address:</td>
    <td>{profile.shipping_address:h}</td>
</tr>
<tr>
    <td align="right">City:</td>
    <td>{profile.shipping_city:h}</td>
</tr>
<tr>
    <td align="right">State:</td>
    <td>{profile.shippingState.state:h}</td>
</tr>
<tr>
    <td align="right">Country:</td>
    <td>{profile.shippingCountry.country:h}</td>
</tr>
<tr>
    <td align="right">Zip code:</td>
    <td>{profile.shipping_zipcode:h}</td>
</tr>

<tr>
    <td colspan="2">&nbsp;</td>
</tr>

<tbody IF="config.Memberships.memberships">
<tr>
    <td colspan="2"><b>Signup for membership</b><br><hr size="1" noshade></td>
</tr>
<tr>
    <td align="right">Sign-up membership:</td>
    <td>{profile.pending_membership:h}</td>
</tr>
<tr>
    <td align="right">Granted membership:</td>
    <td>{profile.membership:h}</td>
</tr>
<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<widget module=WholesaleTrading template="modules/WholesaleTrading/wholesaler_details.tpl" profile={profile}>
</tbody>

</table>

<p>{signature:h}
</body>
</html>
