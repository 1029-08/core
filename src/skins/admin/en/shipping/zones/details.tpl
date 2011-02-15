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

<script type="text/javascript" language="JavaScript 1.2">
<!--

var msg_err_zone_rename='Zone name cannot be empty, please specify it';

function onZoneSubmit()
{
  if (document.forms['zoneform'].elements['zone_name'].value == '') {
    alert(msg_err_zone_rename);
    return false;

  } else {
    saveSelects(new Array('zone_countries','zone_states'));
    document.forms['zoneform'].submit();
  }
}

function visibleBox(id, status)
{
  var Element = document.getElementById(id);
	if (Element) {
	  Element.style.display = ((status) ? "" : "none");
  }
}

function ShowNotes()
{
  visibleBox("notes_url", false);
	visibleBox("notes_body", true);
}

-->
</script>

Use this section to define shipping zones.

<span id="notes_url" style="display:"><a href="javascript:ShowNotes();" class="navigation-path" onclick="this.blur()"><b>How to define shipping zones &gt;&gt;&gt;</b></a></span>

<span id="notes_body" style="display: none"><br /><br />
Select a country or a state from a list, specify the zone where the country or state should be listed and click on the 'Apply' button. To select more than one country/state, hold down the CTRL key while making a selection. A zone can contain either countries or states. You cannot include both states and countries into the same zone.  
</span>

<hr />

<br /><br />

<form action="admin.php" method="post" name="zoneform">

  <input type="hidden" name="target" value="shipping_zones" />
  <input type="hidden" name="action" value="update" IF="!mode=#add#" />
  <input type="hidden" name="action" value="create" IF="mode=#add#" />
  <input type="hidden" name="zoneid" value="{zone.getZoneId()}" />

  <table cellpadding="3" cellspacing="1" width="700">

    <tr>
      <td colspan="3">
        Zone name:
        <input type="text" size="50" name="zone_name" value="{zone.getZoneName()}" />
        &nbsp;&nbsp;
        <widget class="\XLite\View\Button\Regular" IF="!mode=#add#" label="Update" jsCode="javascript: onZoneSubmit();" />
        <widget class="\XLite\View\Button\Regular" IF="mode=#add#" label="Create" jsCode="javascript: onZoneSubmit();" />
        <br /><br />
      </td>
    </tr>

    <tbody IF="zone.getIsDefault()=0">

    {* Countries *}

	  <tr class="dialog-box">
	  	<td class="admin-head" colspan="3"><br />Countries<hr /></td>
	  </tr>

    <tr>
      <td width="45%" align="center">Set</td>
      <td width="10%">&nbsp;</td>
      <td width="45%" align="center">Unset</td>
    </tr>

    <tr>
      <td>
        <input type="hidden" id="zone_countries_store" name="zone_countries_store" value="" />
        <select id="zone_countries" multiple="multiple" size="15" style="width: 100%;">
          <option FOREACH="zone.getZoneCountries(),cn" value="{cn.getCode()}">{cn.getCountry()}</option>
          <option value="">&nbsp;</option>
        </select>

        <script type="text/javascript">
        //<![CDATA[
        normalizeSelect('zone_countries');
        //]]>
        </script>

      </td>
      <td align="center">
        <widget class="\XLite\View\Button\Regular" label="&lt;&lt;" jsCode="javascript: moveSelect(document.getElementById('zone_countries'), document.getElementById('rest_countries'), 'R');" />
        <br /><br />
        <widget class="\XLite\View\Button\Regular" label="&gt;&gt;" jsCode="javascript: moveSelect(document.getElementById('zone_countries'), document.getElementById('rest_countries'), 'L');" />
      </td>
      <td>
        <select id="rest_countries" multiple="multiple" size="15" style="width: 100%;">
          <option FOREACH="zone.getZoneCountries(1),cn" value="{cn.getCode()}">{cn.getCountry()}</option>
        </select>
      </td>
    </tr>

    {* States *}

	  <tr class="dialog-box">
	  	<td class="admin-head" colspan="3"><br />States<hr /></td>
	  </tr>

    <tr>
      <td width="45%" align="center">Set</td>
      <td width="10%">&nbsp;</td>
      <td width="45%" align="center">Unset</td>
    </tr>

    <tr>
      <td>
        <input type="hidden" id="zone_states_store" name="zone_states_store" value="" />
        <select id="zone_states" multiple="multiple" size="15" style="width: 100%;">
          <option FOREACH="zone.getZoneStates(),st" value="{st.country.getCode()}_{st.getCode()}">{st.country.getCountry()}: {st.getState()}</option>
          <option value="">&nbsp;</option>
        </select>

        <script type="text/javascript">
        //<![CDATA[
        normalizeSelect('zone_states');
        //]]>
        </script>

      </td>
      <td align="center">
        <widget class="\XLite\View\Button\Regular" label="&lt;&lt;" jsCode="javascript: moveSelect(document.getElementById('zone_states'), document.getElementById('rest_states'), 'R');" />
        <br /><br />
        <widget class="\XLite\View\Button\Regular" label="&gt;&gt;" jsCode="javascript: moveSelect(document.getElementById('zone_states'), document.getElementById('rest_states'), 'L');" />
      </td>
      <td>
        <select id="rest_states" multiple="multiple" size="15" style="width: 100%;">
          <option FOREACH="zone.getZoneStates(1),st" value="{st.country.getCode()}_{st.getCode()}">{st.country.getCountry()}: {st.getState()}</option>
        </select>
      </td>
    </tr>

    {* City masks *}

	  <tr class="dialog-box">
	  	<td class="admin-head" colspan="3">City masks<hr /></td>
	  </tr>

    <tr>
      <td width="45%" align="center">Set</td>
      <td width="10%">&nbsp;</td>
      <td width="45%">Examples:</td>
    </tr>

    <tr>
      <td>
<textarea cols="40" rows="3" style="width: 100%;" name="zone_cities">
{foreach:zone.getZoneCities(),ct}
{ct}

{end:}
</textarea>
      </td>
      <td align="center">&nbsp;</td>
      <td>Examples</td>
    </tr>

    {* Zip code masks *}

	  <tr class="dialog-box">
	  	<td class="admin-head" colspan="3">Zip/postal code masks<hr /></td>
	  </tr>

    <tr>
      <td width="45%" align="center">Set</td>
      <td width="10%">&nbsp;</td>
      <td width="45%">Examples:</td>
    </tr>

    <tr>
      <td>
<textarea cols="40" rows="3" style="width: 100%;" name="zone_zipcodes">
{foreach:zone.getZoneZipCodes(),zp}
{zp}

{end:}
</textarea>
      </td>
      <td align="center">&nbsp;</td>
      <td>Examples</td>
    </tr>


    {* Address masks *}

	  <tr class="dialog-box">
	  	<td class="admin-head" colspan="3">Address masks<hr /></td>
	  </tr>

    <tr>
      <td width="45%" align="center">Set</td>
      <td width="10%">&nbsp;</td>
      <td width="45%">Examples:</td>
    </tr>

    <tr>
      <td>
<textarea cols="40" rows="3" style="width: 100%;" name="zone_addresses">
{foreach:zone.getZoneAddresses(),addr}
{addr}

{end:}
</textarea>
      </td>
      <td align="center">&nbsp;</td>
      <td>Examples</td>
    </tr>

    <tr>
      <td colspan="3"><br /><br /><hr />
        <widget class="\XLite\View\Button\Regular" jsCode="javascript: onZoneSubmit();" label="Save zone details" />
      </td>
    </tr>

    </tbody>

    <tbody IF="zone.getIsDefault()=1">

      <tr>
        <td colspan="3"><br />This is a default zone which covers all addresses. It's impossible to edit this zone's countries, states etc</td>
      </tr>

    </tbody>

  </table>

</form>

