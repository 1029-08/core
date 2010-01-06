<tbody IF="showPartnerFields">
<tr> <td colspan="4">&nbsp;</td> </tr>
<tr valign="middle">
    <td colspan="4"><b>Partner information</b><br><hr size="1" align=left noshade width="80%"></td>
</tr>

<tr IF="profile.parentProfile" valign="middle">
    <td align="right">Parent profile</td>
    <td>&nbsp;</td>
    <td><a href="admin.php?target=profile&profile_id={parent}"><u>{profile.parentProfile.login:h}</u></a></td>
    <td>&nbsp;</td>
</tr>

<tr IF="profile.declinedPartner&reason" valign="middle">
    <td align="right">Decline reason</td>
    <td>&nbsp;</td>
    <td><input type="text" name="reason" value="{reason:r}" size="32" maxlength="128"></td>
    <td>&nbsp;</td>
</tr>

<tr valign="middle">
    <td align="right">Partner plan requested at sign-up</td>
    <td></td>
    <td>
        <select class="FixedSelect" disabled>
            <option value="" selected="{pending_plan=##}">- select plan -</option>
            <option FOREACH="xlite.factory.AffiliatePlan.findAll(),ap" value="{ap.plan_id}" selected="{pending_plan=ap.plan_id}">{ap.title:h}</option>
        </select>
        <input type=hidden name="pending_plan" value="{pending_plan}">
    </td>
    <td>&nbsp;</td>
</tr>
<tr valign="middle">
    <td align="right">Assigned partner plan</td>
    <td class=Star>*</td>
    <td>
        <select class="FixedSelect" name="plan">
            <option value="" selected="{plan=##}">- not assigned -</option>
            <option FOREACH="xlite.factory.AffiliatePlan.findAll(),ap" value="{ap.plan_id}" selected="{ap.plan_id=plan}">{ap.title:h}</option>
        </select>
    </td>
    <td>&nbsp;</td>
</tr>

<tr valign="middle">
    <td align="right">Sign-up date</td>
    <td>&nbsp;</td>
    <td>{time_format(partner_signup)}</td>
    <td>&nbsp;</td>
</tr>

<widget class="XLite_Module_Affiliate_View_PartnerField" template="modules/Affiliate/partner_field.tpl" formField="partner_fields" partnerFields="{partnerFields}" partner="{profile}">
</tbody>

<script language="JavaScript">
var partnerAccessLevel = {auth.partnerAccessLevel};
var declinedAccessLevel = {auth.declinedPartnerAccessLevel};
var declineUrl = "admin.php?target=decline_partner&profile_id={profile_id}&returnUrl={dialog.url:u}";
<!--
accessLevel = document.getElementById('access_level');
// define a new handler for access level changer
accessLevel.onchange = function() { changeAccessLevel(); };

function changeAccessLevel() {
    onAccessLevelChange();
    if (accessLevel.value == partnerAccessLevel) {
        alert('Please fill partner form fields after you submit the form.');
    } else if (accessLevel.value == declinedAccessLevel) {
        document.location = declineUrl;
    }
}
// -->
</script>
