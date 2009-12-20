<table border=0>
<tr>
    <td colspan=2>
        {if:invalid_field_order}
            <font class=AdminHead>Field order:</font>&nbsp;
            <font class="ValidateErrorMessage">&lt;&lt;&nbsp;Duplicate field: &quot;{invalid_field_name}&quot;</font></td>
        {else:}
            <font class=AdminHead>Field order:</font></td>
        {end:}
</tr>
<tr FOREACH="xlite.factory.Product.importFields,id,fields">
    <td width=1>{id}:</td>
    <td width=99%>
        <select name="product_layout[{id}]">
            <option FOREACH="fields,field,value" value="{field}" selected="{isOrderFieldSelected(id,field,value)}">{field}</option>
        </select>
    </td>
</tr>
<tr>
    <td colspan="2">
    <input type=button value="Save field order" onClick="javascript: document.data_form.action.value='layout'; document.data_form.submit();">
    </td>
</tr>
</table>
