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
<a name="test_"></a>
<form action="admin.php#test_" method="POST" name="shipping_test">
    <input type="hidden" name="target" value="{target}">
    <input type="hidden" name="action" value="test">

<table width="100%" cellspacing="0" cellpadding="5" border="0">
<tr valign="top">
    <td>
        <b>Original address:</b><hr>
        <table width="100%">
		<tr><td>Address</td><td>{config.Company.location_address}</td></tr>
        <tr><td>City</td><td>{config.Company.location_city}</td></tr>
		<tr><td>State</td><td>{config.Company.locationState.state}</td></tr>
        <tr><td>Country</td><td>{config.Company.location_country}</td></tr>
        <tr><td>Zip/Postal code</td><td>{config.Company.location_zipcode}</td></tr>
        </table>
    </td>
    <td>
        <b>Destination address:</b><hr>
        <table width="100%">
        <tr><td>City</td><td><input type="text" name="destinationCity" value="{destinationCity}"></td></tr>
        <tr>
            <td>State</td>
            <td><widget class="\XLite\View\StateSelect" field="destination_state" state="{destination_state}" isLinked=1 />
  			    <widget class="\XLite\Validator\StateValidator" field="destination_state" countryField="destination_country">
            </td>
        </tr>
        <tr valign="middle">
            <td>Other state (specify)</td>
            <td><input type="text" name="destination_custom_state" value="{destination_custom_state:r}" size="32" maxlength="64" /><td>
        </tr>
        <tr>
            <td>Country</td>
            <td><widget class="\XLite\View\CountrySelect" field="destination_country" country="{destination_country}" /></td>
        </tr>
        <tr><td>Zip/Postal code</td><td><input type="text" name="destinationZipCode" value="{destinationZipCode}"></td></tr>
        <tr><td>Weight ({weightUnit:h})</td><td><input type="text" name="pounds" size="10" value="{pounds:r}"></td></tr>
        </table>
    </td>
</tr>
<tr>
	<td colspan="2"><hr></td>
</tr>
<tr>
	<td>&nbsp;</td>
    <td align="right">
        <input type="submit" class="DialogMainButton" value=" Run test ">
    </td>
</tr>
</table>
</form>
{if:testResult}
<span class="ErrorMessage" IF="ups.error">
    {ups.error:h}
</span>
<p>
<span IF="ups.xmlError">
    <pre>{ups.response:h}</pre>
</span>
<p>
<b>Shipping Rates:</b>
<span IF="rates">
<table border="1" cellspacing="0">
<tr><th>Shipping #</th><th>Shipping Method</th><th>Rate</th></tr>
<tr FOREACH="rates,id,rate">
<td>{id}</td><td>{rate.shipping.nameUPS:h}</td><td>{price_format(rate.rate):h}</td></tr>
</table>
</span>
<span IF="!rates">
No shipping rates
</span>
{end:}
