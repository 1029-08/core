<p align=justify>This section displays order placement statistics for today, this week and this month.</p>

<br>

<p>
<table border=0 cellpadding=0 cellspacing=0 width="80%">
<tr>
<td bgcolor=#dddddd>
    <table cellpadding=3 cellspacing=1 border=0 width="100%">
    <tr class=TableHead bgcolor=#ffffff>
        <td>Status</td>
        <td align=center>Today</td>
        <td align=center>This week</td>
        <td align=center>This month</td>
    </tr>
	<tr FOREACH="stats.orders,status" class="DialogBox">
		<td>{status.name}</td>
		<td align=center>{status.statistics.today}</td>
		<td align=center>{status.statistics.week}</td>
		<td align=center>{status.statistics.month}</td>
	</tr>
	<tr class="DialogBox">
        <td align="right"><b>Gross total:</b></td>
		<td align=center>{price_format(stats.total.today):h}</td>
        <td align=center>{price_format(stats.total.week):h}</td>
		<td align=center>{price_format(stats.total.month):h}</td>
	</tr>
    <tr class="DialogBox">
        <td align="right"><b>Payments received:</b></td>
        <td align=center>{price_format(stats.paid.today):h}</td>
        <td align=center>{price_format(stats.paid.week):h}</td>
        <td align=center>{price_format(stats.paid.month):h}</td>
    </tr>
    </table>
</td>
</tr>
</table>
</p>

<p>
<widget class="XLite_View_Button" label="Perform order search" href="admin.php?target=order_list" font="FormButton">
