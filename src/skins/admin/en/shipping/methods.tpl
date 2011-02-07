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

<div class="right-panel">
  <widget class="\XLite\View\EditorLanguageSelector" />
</div>

<script type="text/javascript" language="JavaScript 1.2">
<!-- 

var CheckBoxes = new Array();

function populateChecked(class_name, check_id)
{
  var CheckBoxesArray = CheckBoxes[class_name];
  CheckBoxesArray[CheckBoxesArray.length] = check_id;
}

function setChecked(class_name, check)
{
  var CheckBoxesArray = CheckBoxes[class_name];

  if (CheckBoxesArray) {
        for (var i = 0; i < CheckBoxesArray.length; i++) {
          var Element = document.getElementById(CheckBoxesArray[i]);
            if (Element) {
              Element.checked = check;
            }
        }
  }
}

function setHeaderChecked(class_name)
{
  var Element = document.getElementById("enable_method_" + class_name);
    if (Element && !Element.checked) {
      Element.checked = true;
    }
}

function onDeleteButton(method_id)
{
  formName = 'delete_method';
  document.forms[formName].elements['method_id'].value = method_id;
  document.forms[formName].submit();
}

// -->
</script>

Use this section to define your store's shipping methods.

<hr />

<form action="admin.php" name="delete_method" method="post">
  
  <input type="hidden" name="target" value="shipping_methods" />
  <input type="hidden" name="action" value="delete" />
  <input type="hidden" name="method_id" value="" />

</form>

{foreach:getShippingProcessors(),processor}

<script type="text/javascript" language="JavaScript 1.2">
<!--

CheckBoxes["{processor.getProcessorId()}"] = new Array();

-->
</script>

<form action="admin.php" name="shipping_method_{processor.getProcessorId()}" method="post">
  
  <input type="hidden" name="target" value="shipping_methods" />
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="method_id" value="" />

  <table cellpadding="0" cellspacing="0" border="0" width="600">

    <tr>
      <td><br />&nbsp;</td>
    </tr>

    <tr class="dialog-box">
      <td class="admin-head" colspan=5>{processor.getProcessorName()}</td>
    </tr>

    <tr IF="processor.getProcessorId()=#ups#">
      <td align="right">&nbsp;<widget module="CDev\UPSOnlineTools" template="modules/CDev/UPSOnlineTools/settings_link.tpl" /></td>
    </tr>

    <tr>
      <td>

        <table class="data-table">

          <tr>
            <th width="90%">Shipping method</th>
            <th>Pos.</th>
            <th>Active<br />
              <input id="enable_method_{processor.getProcessorId()}" type="checkbox" onClick="this.blur();setChecked('{processor.getProcessorId()}',this.checked);" />
            </th>
            <th valign="top">&nbsp;</th>
          </tr>

          <tr FOREACH="processor.getShippingMethods(),shipping_idx,method" class="{getRowClass(shipping_idx,#dialog-box#,#highlight#)}">
            <td>
              <input type="text" name="methods[{method.getMethodId()}][name]" size="50" value="{method.getName()}" IF="processor.isMethodNamesAdjustable()" />
              <span IF="!processor.isMethodNamesAdjustable()">{method.getName()}</span>
            </td>
            <td><input type="text" name="methods[{method.getMethodId()}][position]" size="4" value="{method.getPosition()}" /></td>
            <td align="center">
              <input id="shipping_enabled_{method.getMethodId()}" type="checkbox" name="methods[{method.getMethodId()}][enabled]" checked="{method.getEnabled()}" onClick="this.blur();" />
              <script language="Javascript">populateChecked("{processor.getProcessorId()}", "shipping_enabled_{method.getMethodId()}");</script>
              <script language="Javascript" IF="method.getEnabled()">setHeaderChecked("{processor.getProcessorId()}");</script>
            </td>
            <td>
              <input type="button" name="delete" value="Delete" onclick="javascript: onDeleteButton('{method.getMethodId()}');" />
            </td>
          </tr>

          <widget module="CDev\UPSOnlineTools" template="modules/CDev/UPSOnlineTools/settings_disclaimer.tpl" IF="processor.getProcessorId()=#ups#"/>

        </table>

      </td>
    </tr>

    <tr>
      <td colspan="4">
        <br />
        <input type="submit" value="Update" class="DialogMainButton" />
      </td>
    </tr>

  </table>

</form>

{end:}

<form action="admin.php" method="post">

  <input type="hidden" name="target" value="shipping_methods" />
  <input type="hidden" name="action" value="add" />

  <table cellpadding="0" cellspacing="0" border="0">
    
    <tr>
      <td>&nbsp;</td>
    </tr>

    <tr class="dialog-box">
      <td class="admin-title">Add shipping method</td>
    </tr>

    <tr>
      <td>&nbsp;</td>
    </tr>

    <tr>
      <td>

        <table class="data-table">

          <tr>
            <th>Shipping method</th>
            <th>Pos.</th>
          </tr>

          <tr class="dialog-box">
            <td>
              <input type="text" name="name" size="50" value="{name}" />
            </td>
            <td>
              <input type="text" name="position" size="4" value="{position}" />
            </td>
          </tr>

        </table>

      </td>
    </tr>

    <tr>
      <td colspan="5"><br /><input type="submit" value="Add" /></td>
    </tr>

    <tr IF="!moduleArrayPointer=moduleArraySize">
      <td colspan="5"><br /><hr style="background-color: #E5EBEF; height: 2px; border: 0" /><br /></td>
    </tr>

  </table>

</form>

