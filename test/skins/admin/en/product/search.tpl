<FORM name="search_form" action="admin.php" method="GET">
<span IF="dialog.extraParams=##"><input FOREACH="dialog.allParams,param,val" type="hidden" name="{param}" value="{val:r}"/></span>
<span IF="!dialog.extraParams=##"><input FOREACH="dialog.extraParams,param,val" type="hidden" name="{param}" value="{val:r}"/></span>
<input type="hidden" name="mode" value="search">

{extra_parameters:h}

<TABLE border=0>
<TBODY>
	<TR>
		<TD class="FormButton" noWrap height=10>Product SKU</TD>
		<TD width=10 height=10></TD>
		<TD height=10><INPUT size=6 name="search_productsku" value="{search_productsku}"></TD>
	</TR>
	<TR>
		<TD class="FormButton" noWrap height=10>Product Title</TD>
		<TD width=10 height=10></TD>
		<TD height=10><INPUT size=30 name="substring" value="{substring}"></TD>
	</TR>
	<TR>
		<TD class="FormButton" noWrap height=10>In category</TD>
		<TD width=10 height=10><FONT class="ErrorMessage">*</FONT></TD>
		<TD height=10>
            <widget class="XLite_View_CategorySelect"fieldName="search_category" allOption>
        </TD>
	</TR>
	<TR>
		<TD class="FormButton" noWrap height=10 colspan="3">
			Search in subcategories
			<input type="checkbox" name="subcategory_search" checked="{subcategory_search}">
		</TD>
	</TR>
	<widget module="ProductAdviser" template="modules/ProductAdviser/product_search.tpl">
    <widget module="InventoryTracking" template="modules/InventoryTracking/product_search.tpl">
    <TR><TD colspan=3>&nbsp;</TD></TR>
	<TR>
		<TD colspan=3>
		    <INPUT type="submit" value=" Search ">
        </TD>
	</TR>
</TBODY>
</TABLE>
</FORM>

<br>
<b>Note:</b> You can also <a href="admin.php?target=add_product"><u>add a new product</u></a>.
