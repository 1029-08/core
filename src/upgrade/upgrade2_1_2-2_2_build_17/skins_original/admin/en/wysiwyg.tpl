<script language="Javascript">
<!-- 

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

// -->
</script>

This section helps you to fully customize the design of your store.<br>
<span id="notes_url" style="display:"><a href="javascript:ShowNotes();" class="NavigationPath" onClick="this.blur()"><b>How to use this section &gt;&gt;&gt;</b></a></span>
<span id="notes_body" style="display: none">
<p class="adminParagraph">
<table border=0 cellpadding=5 cellspacing=0>
    <tr>
        <td>&nbsp;&nbsp;</td>
        <td class="TableHead">&nbsp;&nbsp;</td>
        <td>&nbsp;</td>
        <td>
When you click on the <b>&quot;Create HTML design templates&quot;</b> button, your store's current design is exported into a set of HTML pages located in the 'var/html/' subfolder of your LiteCommerce software installation. Download the set to your local computer; the set is suitable for editing in most HTML editors, including WYSIWYG editors such as MS FrontPage and Macromedia DreamWeaver. When you finish editing the design templates, upload them back to the server and click on the <b>&quot;Publish new design&quot;</b> button for your modifications to take effect on your store.
        </td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
</table>
</p>
</span>

<hr>
<p>

<form action="admin.php" method="POST" name="wysiwyg_import_export">
<input type="hidden" name="target" value="wysiwyg">
<input type="hidden" name="action" value="export">
<input type=button value="Create HTML design templates" onClick="document.wysiwyg_import_export.submit()">
<br><br>
<input type=button value="Publish new design" onClick="document.wysiwyg_import_export.action.value='import'; document.wysiwyg_import_export.submit()">
</form>

<p><b>Note:</b> For your convenience it is recommended that you use LiteCommerce Control Panel to create, download and publish HTML design templates.</p>

<p IF="shortcuts">
<br>
<b>The following is the list of pages most likely to be edited:</b>
<p foreach="shortcuts,template,comment"><a href="{template}"><u>{comment}</u></a></p>
</p>
