<form name="deleteForm" action="admin.php" method="POST">
<input type="hidden" name="target" value="categories">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="category_id" value="{category_id}">

<table border="0">
<tr>
	<td colspan="3">
All subcategories and products under the following categories will be removed:
	</td>
</tr>
<tr>
	<td colspan="3">
    <b>{foreach:categories,key,cat}{cat.name}<br>{end:}</b>
	</td>
</tr>
<tr><td colspan=3>&nbsp;</td></tr>
<tr>
	<td colspan="3" class="AdminTitle">
Warning: this operation can not be reverted!
	</td>
</tr>
<tr><td colspan=3>&nbsp;</td></tr>
<tr>	
	<td colspan=3>Are you sure you want to continue?<br><br>
		<input type="submit" value="Yes" class="DialogMainButton">&nbsp;&nbsp;
		<input type="button" value="No" onclick="javascript: document.location='admin.php?target=categories&category_id={category_id}'">
	</td>
</tr>	
</table>
</form>
