{* SVN $Id$ *}

{* Product search form template *}

<widget class="XLite_View_Form_Search_Product_Simple" name="search_form" />

<TABLE border="0" cellpadding="0" cellspacing="0">
<TR>
	<TD><IMG src="images/searchbox_left.gif" width="9" height="78" alt=""></TD>
	<TD width="100%" class="SearchBoxBG">
		<TABLE>
		<TR>
			<TD colspan="2">Find product:</TD>
		</TR>
		<TR>
		    <TD><SPAN IF="!substring:r"><INPUT type="text" name="substring" style="width:75pt;color:#888888" value="Find product" onFocus="this.value=''; this.style.color='#000000';"></SPAN>
			    <SPAN IF="substring:r"><INPUT type="text" name="substring" style="width:75pt" value="{substring:r}"></SPAN>
			</TD>
		    <TD><widget class="XLite_View_Button_Submit" label="Go" /></TD>
		</TR>
		<TR IF="xlite.AdvancedSearchEnabled">
			<TD>
			    &nbsp;<A href="cart.php?target=advanced_search" title="Advanced Search" class="AdvancedSearchLink">Advanced search</A>
		    </TD>
		</TR>
		</TABLE>
	</TD>
	<TD><IMG src="images/searchbox_right.gif" width="9" height="78" alt=""></TD>
</TR>

</TABLE>

<widget name="search_form" end />

</FORM>
<BR>
