<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr> 
    <td>&nbsp;&nbsp;&nbsp;</td>
    <td class="VertMenuItems" valign="top">
        {wrap(auth.profile,#login#,#20#)}<br>
        Logged in!<br>
        <br>
        <a href="admin.php?target=login&action=logoff" class="SidebarItems"><input type="image" src="images/go.gif" width="13" height="13" border="0" align="absmiddle"> Logoff</a>
        <br>
        <br>
    </td>
</tr>
<tr IF="recentAdmins"><td colspan=2>Logins history:<br><br></td></tr>
<tbody FOREACH="recentAdmins,recentAdmin">
<tr><td align=left colspan=2>{time_format(recentAdmin,#last_login#)}</td></tr>
<tr><td align=right colspan=2>{wrap(recentAdmin,#login#,#25#)}<br><br></td></tr>
</tbody>
</table>
