
<form action="admin.php" method=POST name=add_modify_form enctype="multipart/form-data">
<input type="hidden" foreach="dialog.allparams,param,v" name="{param}" value="{v}"/>
<input IF="mode=#add#" type=hidden name=action value="save_banner"/>
<input IF="mode=#modify#" type=hidden name=action value="update_banner"/>

<table border=0>
<tr>
    <td>
    <table border=0 cellpadding=3>
    <tr>
        <td nowrap>Banner name</td>
        <td class=Star>*</td>
        <td nowrap><input type=text name=name value="{name:r}" size=35 maxlength=255><widget class="CRequiredValidator" field="name"></td>
    </tr>

    <!-- IMAGE BANNER -->
    <tbody IF="type=#image#">
    <tr>
        <td valign=top>Image</td>
        <td valign=top class=Star>*</td>
        <td>
            <span IF="mode=#modify#"><img src="{shopUrl(#cart.php#)}?target=image&action=banner_image&id={banner_id}&rnd={rand()}" border=0><br></span>
			<widget class="CImageUpload" field="banner" actionName="images" formName="add_modify_form" object="{banner}">
        </td>
    </tr>
    <tr>
        <td nowrap>Alt. tag</td>
        <td>&nbsp;</td>
        <td><input type=text name=alt value="{alt:r}" size=35 maxlength=255></td>
    </tr>
    </tbody>
    <!-- /IMAGE BANNER -->

    <tr>
        <td>Appearance</td>
        <td>&nbsp;</td>
        <td>
        <select name=link_target>
            <option value="_blank" selected="link_target=#_blank#">Link opens new browser window</option>
            <option value="_top" selected="link_target=#_top#">Link in same browser window</option>
        </select>
        </td>
    </tr>

    <tr>
        <td valign=top>Text <span IF="type=#rich#"><br>(banner HTML body)</span></td>
        <td>&nbsp;</td>
        <td><textarea id="banner_body" name=body cols=35 rows=4>{body:h}</textarea></td>
    </tr>

    <!-- RICH BANNER -->
    <tbody IF="type=#rich#">
    <tr>
        <td colspan=2>&nbsp;</td>
        <td>
            <a href="#" onclick="addLink('[url]');"><u><img src="images/modules/Affiliate/open_a.gif" border=0 align=absmiddle> Add link opening tag</u></a>  &nbsp;&nbsp;<a href="#" onclick="addLink('[/url]');"><u><img src="images/modules/Affiliate/close_a.gif" border=0 align=absmiddle> Add link closing tag</u></a> &nbsp;&nbsp;<a href="#" onclick="addLink('[obj]');"><u><img src="images/modules/Affiliate/add_obj.gif" border=0 align=absmiddle> Add media object</u></a>
            
            <script language="JavaScript">
            <!--
            function addLink(link) {
                document.add_modify_form.banner_body.value += link;
                document.add_modify_form.banner_body.focus();
            }
            // -->
            </script>
 
         </td>
    </tr>
    <tr>
        <td valign=top>Media object</td>
        <td valign=top>&nbsp;</td>
        <td>
            <input type=file name=banner>
        </td>
    </tr>
    <tr>
        <td>Width (flash only)</td>
        <td valign=top>&nbsp;</td>
        <td><input type=text name=width value="{width}" size=5 maxlength=5></td>
    </tr>
    <tr>
        <td>Height (flash only)</td>
        <td valign=top>&nbsp;</td>
        <td><input type=text name=height value="{height}" size=5 maxlength=5></td>
    </tr>
    </tbody>
    <!-- /RICH BANNER -->
    
    <!-- IMAGE BANNER -->
    <tr IF="type=#image#">
        <td nowrap>Text alignment</td>
        <td>&nbsp;</td>
        <td>
        <select name=align> 
            <option value=bottom selected="align=#bottom#">Bottom</option>
            <option value=top selected="align=#top#">Top</option>
            <option value=left selected="align=#left#">Left</option>
            <option value=right selected="align=#right#">Right</option>
        </select>
        </td>
    </tr>
    <!-- /IMAGE BANNER -->

    <tr>
        <td>Availability</td>
        <td>&nbsp;</td>
        <td>
        <select name=enabled>
            <option value=1 selected="enabled=#1#">Enabled</option>
            <option value=0 selected="enabled=#0#">Disabled</option>
        </select>
        </td>
    </tr>
    <tr>
        <td colspan=2>&nbsp;</td>
        <td>
        <input type=submit name=save value="Save banner">
        </td>
    </tr>
    </table>
    </td>
    <td IF="mode=#modify#" align=center valign=top width="100%">
    <font class=AdminHead>Preview:</font><br><br>
    <widget class="CBanner" mode="modify" type="js" banner="{banner}">
    </td>
</tr>    
<tr><td colspan=2>&nbsp;</td></tr>
<tr>
    <td colspan=2><a href="admin.php?target=banners"><img src="skins/admin/en/images/go.gif" width="13" height="13" border="0" align="absmiddle"><b> List all banners</b></a></p></td>
</tr>
</table>
