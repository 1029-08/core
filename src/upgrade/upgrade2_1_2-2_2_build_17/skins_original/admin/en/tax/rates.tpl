<form action="admin.php" method="POST">
<input type="hidden" name="target" value="taxes">
<input type="hidden" name="page" value="{page}">
<input type="hidden" name="action" value="update_rates">
<table border="0" cellspacing="0" cellpadding="0">
<tr><td class="CenterBorder">
<table border="0" cellspacing="1" cellpadding="3">
<tr>
	<th class="TableHead" colspan="{getMaxColspan(#1#)}">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td align="left">
				<a href="admin.php?target=taxes&page={page}&action=all&open=1" title="Expand all"><img src="images/plus.gif" border="0"></a>/<a href="admin.php?target=taxes&page={page}&action=all&open=0" title="Collapse all"><img src="images/minus.gif" border="0"></a>
			</td>
			<th align="center">
			&nbsp;&nbsp;Condition&nbsp;&nbsp;
			</th>
			<td align="right">
			<span style="font-weight: normal;"><a href="admin.php?target=taxes&page=add_rate&mode=add&ind="><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"> Add rate/condition</a></span>&nbsp;&nbsp;</th>
			</td>
		</tr>
		</table>
	<th class="TableHead">&nbsp;&nbsp;Tax&nbsp;&nbsp;</th>
	<th class="TableHead">&nbsp;&nbsp;%&nbsp;&nbsp;</th>
	<th class="TableHead">Pos.</th>
	<th class="TableHead">Actions</th>
</tr>
<tbody FOREACH="_rates,ind,tax">
<tr class="{getRowClass(ind,#DialogBox#,#TableRow#)}">
{getLevels(ind):h}
{if:isCondition(tax)}
<td colspan="{getColspan(ind,#3#)}" bgcolor="#e8e8e8"> <table border="0" cellpadding="0" cellspacing="0">
<tr><td width="12">
<a name="{ind}">
{if:isOpen(tax)}<a href="admin.php?target=taxes&page={page}&action=close&ind={ind}#{ind}" title="Collapse condition"><img src="images/minus.gif" border="0"></a>{else:}<a href="admin.php?target=taxes&page={page}&action=open&ind={ind}#{ind}" title="Expand condition"><img src="images/plus.gif" border="0"></a>{end:}</td>
<td>&nbsp;{getCondition(tax)}&nbsp;</td><td width="10%" nowrap>&nbsp;<a href="admin.php?target=taxes&page=add_rate&mode=add&ind={getPath(ind)}"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"> Add rate/condition</a></td></tr></table>   
</td>
{end:}
{if:isConditionalAction(tax)}
<td colspan="{getColspan(ind)}">{getCondition(tax)}</td><td>{getCondVarName(tax)}</td><td><input type="text" name="varvalue[{ind}]" value="{getCondVarValue(tax)}" size="15"></td>
{end:}
{if:isAction(tax)}
<td colspan="{getColspan(ind)}">&nbsp;</td><td>{getVarName(tax)}</td><td><input type="text" name="varvalue[{ind}]" value="{getVarValue(tax)}" size="15"></td>
{end:}
<td><input type="text" name="pos[{ind}]" value="{getTreePos(ind)}" size="3"></td><td nowrap>&nbsp;&nbsp;<a href="admin.php?target=taxes&page=add_rate&mode=edit&ind={getPath(ind)}"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"> Edit</a>&nbsp;&nbsp;&nbsp;<a href="admin.php?target=taxes&page=rates&action=delete_rate&ind={ind}"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"> Delete</a>&nbsp;&nbsp;</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
	<td>
	<br>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td><input type="submit" value=" Update "></td>
			<td align=right><a href="javascript:void(0);" onClick="this.blur();window.open('admin.php?target=taxes&action=calculator','tax_calculator','width=600,height=550,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"> Tax calculator</a></td>
		</tr>
	</table>
	</td>
</tr>
</table>
</form>

