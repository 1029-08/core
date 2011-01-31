{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * ____file_title____
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<table border=0>
<tr class="TableHead">
    <th valign="top" nowrap>Order #</th>
    <th valign="top" align=left>Status</th>
    <widget module="CDev\AntiFraud" template="modules/CDev/AntiFraud/orders/label.tpl">
    <th valign="top" nowrap align=left>Date</th>
    <th valign="top" nowrap align=left>Customer</th>
    <th valign="top" align=center>Total</th>
    <th valign="top">&nbsp;</th>
</tr>
<tr FOREACH="recentOrders,order_idx,order" class="{getRowClass(order_idx,##,#TableRow#)}">
    <td>&nbsp;<a href="admin.php?target=order&order_id={order.order_id}" onClick="this.blur()"><u>{order.order_id}</u></a></td>
    <td><widget template="common/order_status.tpl"></td>
	<widget module="CDev\AntiFraud" template="modules/CDev/AntiFraud/orders/factor.tpl">
    <td nowrap><a href="admin.php?target=order&order_id={order.order_id}" onClick="this.blur()">{formatTime(order.date)}</a></td>
    <td nowrap>
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr>
    		<td width=90% nowrap><a href="admin.php?target=order&order_id={order.order_id}" onClick="this.blur()">{order.profile.billing_address.title:h} {order.profile.billing_address.firstname:h} {order.profile.billing_address.lastname:h}</a></td>
    		<td width=10% nowrap align=right>(<a href="admin.php?target=order&order_id={order.order_id}" onClick="this.blur()">{order.profile.login}</a>)</td>
		</tr>
		</table>
    </td>
    <td align=right>{price_format(order,#total#):h}</td>
    <td nowrap align=right>&nbsp;<a href="admin.php?target=order&order_id={order.order_id}" onClick="this.blur()"><u>details</u></a>&nbsp;&gt;&gt;</td>
</tr>
<tr>
    <td colspan=6>&nbsp;</td>
</tr>
</table>
