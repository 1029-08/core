<script language="Javascript">
<!-- 
	var deleted_memberships = new Array();
	var updated_memberships = new Array();
	var selected = false;

    function SelectAll() 
	{
        selected = !selected;
        setChecked(selected);
    }	
	
	function setChecked(check) 
	{
        for (var i = 0; i < deleted_memberships.length; i++) {
            var element = document.getElementById(deleted_memberships[i]);
            element.checked = check;
        }
	}
	
	function isChecked()
	{
		var isChecked = false;
        for (var i = 0; i < deleted_memberships.length; i++) {
            var element = document.getElementById(deleted_memberships[i]);
			if (element.checked) 
				isChecked = true;
        }
		return isChecked;
	}

	function updateAction()
	{
		for (var i = 0; i < updated_memberships.length; i++) {
		    var elementi = document.getElementById(updated_memberships[i]);
			for (var j = 0; j < updated_memberships.length; j++) {
				var elementj = document.getElementById(updated_memberships[j]);
				if (elementi.value == elementj.value && i!=j) {
					alert("Duplicate membership names are not allowed");	
					return false;	
				} 
			}
		}	
		document.update_membership_form.submit();
	}

	function deleteAction() 
	{
		if (!isChecked()) { 
			alert("You have nothing to delete");
			return false;
		}
        
        var count   = getDeletedMembershipsCount();
        var message = "You are about to delete selected membership" + (count > 1 ? "s" : "") + ".\n\nAre you sure you want to delete " + (count > 1 ? "them" : "it") + "?";
        if (confirm(message)) {
			document.update_membership_form.action.value = 'delete';
			document.update_membership_form.submit();	
			return true;
		}
		return false;	
	}

    function getDeletedMembershipsCount()
    {
        var count = 0;
        for (var i = 0; i < deleted_memberships.length; i++) {
            var element = document.getElementById(deleted_memberships[i]);
            if (element.checked) { 
                count++;
            }
        }    

        return count;
    }

    function openUserSearchWindow(membership_name, count)
    {
        var url = false;
        if (count != "unused") {
            window.open("admin.php?membership=" + membership_name + "&user_type=all&target=users&mode=search&search=Search");
        }
    }
// -->
</script>

<p>Use this section to review the list of existing membership levels and add new ones.</p>

<span IF="memberships">

<form action="admin.php" method="POST" name="update_membership_form">
<input type="hidden" name="target" value="memberships">
<input type="hidden" name="action" value="update">

<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td class="CenterBorder">
  	<table border="0" cellspacing="1" cellpadding="2">
		<tr class="TableHead">
			<th>Pos.</th>
			<th>Membership name</th>
			<th>Requested/Granted</th>
            <th><input type="checkbox" style="margin-left: 2px; margin-top: 2px; margin-right: 2px; margin-bottom: 2px;" onClick="this.blur();SelectAll();"></th>
		</tr>
		<tr FOREACH="memberships,membership_id,membership" height="24" class="{getRowClass(membership_id,#TableRow#,#DialogBox#)}">
			<td><input type="text" name="update_memberships[{membership_id}][orderby]" value="{membership.orderby}" size="4"></td>
			<td><input type="text" id="{membership_id}" name="update_memberships[{membership_id}][membership]" value="{membership.membership}" size="32"></td>
			<td style="text-align: center;">
                {if:membership.getRequestedCount()}
                    <a href="admin.php?membership=pending_membership&user_type=all&target=users&mode=search&search=Search" target="_blank">{membership.getRequestedCount()}</a>
                {else:}
                    unused
                {end:}/{if:membership.getGrantedCount()}
                    <a href="admin.php?membership={membership.membership:u}&user_type=all&target=users&mode=search&search=Search" target="_blank">{membership.getGrantedCount()}</a>
                {else:}
                    unused
                {end:}
            </td>
            <td><input style="margin-left: 2px; margin-top: 2px; margin-right: 2px; margin-bottom: 2px;" type="checkbox" id="deleted_{membership_id}" name="deleted_memberships[]" value="{membership_id}" onClick="this.blur();"></td>
			<script>
				updated_memberships.push("{membership_id}");
				deleted_memberships.push("deleted_{membership_id}");
			</script>
		</tr>
	</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
	                <td><input type="button" value=" Update " onClick="javascript: updateAction();"></td>
                    <td align="right"><input type="button" value="Delete selected" onClick="javascript: deleteAction();"></td>
                </tr>
            </table>
        </td>
	</tr>	
</table>
</form>
<br>
</span>

<span IF="!memberships">
No memberships defined.
</span>

<hr>

<form action="admin.php" method="POST">
<input type="hidden" name="target" value="memberships">
<input type="hidden" name="action" value="add">
<table cellpadding="0" cellspacing="0" border="0">
    <tr><td>&nbsp;</td></tr>
	<tr class="DialogBox">
		<td class="AdminTitle">Add new membership level</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
    <tr>
        <td class="CenterBorder">
			<table cellspacing="1" cellpadding="2" border="0">	
				<tr>
					<th class="TableHead">Pos.</th>
					<th class="TableHead">Membership name</th>
				</tr>
				<tr class="DialogBox">
					<td><input type="text" name="new_membership[orderby]" value="{new_membership.orderby}" size="4"></td>
					<td><input type="text" name="new_membership[membership]" value="{new_membership.membership}" size="32"></td>
				</tr>
			</table>
		</td>
		<td>
            <table cellspacing="1" cellpadding="2" border="0">  
                <tr>
					<td>&nbsp;</td>
                </tr>
                <tr class="DialogBox">
					<td><widget class="CMembershipValidator" field="new_membership[membership]" action="add"></td>
                </tr>
            </table>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td align="right"><input type="submit" value=" Add "></td>
	</tr>
</table>	
</form>
