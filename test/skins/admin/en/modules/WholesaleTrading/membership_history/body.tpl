{if:!isEmpty(membership_history)}
<span id="membership_history" style="DISPLAY: none;">
<table border="0" cellpadding="0" cellspacing="0">
<tr class="TableHead"><td>
<table border="0" cellpadding="2" cellspacing="2">
<tr class="TableHead">
<td><b>Membership</b></td><td><b>Exp. date</b></td><td><b>Change date</b></td>
<tr>
<tr FOREACH="membershipHistory,v" class="Center">
<td>{if:v.current}<b>{end:}{if:v.membership}{v.membership}{else:}<i>Not assigned{end:}</i>{if:v.current}</b>{end:}</td>
<td>{if:v.current}<b>{end:}{if:v.membership_exp_date}{date_format(v.membership_exp_date)}{else:}<i>Never</i>{end:}{if:v.current}</b>{end:}</td>
<td>{if:v.current}<b>{end:}{if:v.date}{time_format(v.date)}{else:}<i>Unknown</i>{end:}{if:v.current}</b>{end:}</td>
</tr>
</table>
</td></tr>
</table>
</span>
{end:}