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
{if:clone}
    <tr IF="!isSelected(cloneOrder,#payedByGC#,#0#)">
        <td nowrap colspan="4" align="right" class="ProductDetailsTitle">Paid with GC:</td>
        <td align="right">{price_format(cloneOrder,#payedByGC#):h}</td>
    </tr>
{else:}
	<tr IF="!isSelected(order,#payedByGC#,#0#)">
        <td nowrap colspan="4" align="right" class="ProductDetailsTitle">{if:mode=#invoice#}Paid with GC{else:}{if:!order.gc.validate()=#1#}<a href="admin.php?target=gift_certificate&gcid={order.gc.gcid}" onClick="this.blur()"><img src="skins/admin/en/images/go.gif" width="13" height="13" border="0" align="absmiddle"> Paid with GC</a>{else:}<a href="javascript:alert('The \'{order.gcid}\' GC was deleted.')" onClick="this.blur()">Paid with GC <font color="#FF0000">(!)</font></a>{end:}{end:}</td>
		<td align="right">{price_format(order,#payedByGC#):h}</td>
	</tr>
{end:}
