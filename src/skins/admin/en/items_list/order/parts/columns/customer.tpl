{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Item name
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 * @ListChild (list="itemsList.order.admin.search.columns", weight="60")
 *}

<td class="customer"><a class="customer" href="{buildURL(#profile#,##,_ARRAY_(#profile_id#^order.orig_profile.getProfileId()))}">{order.profile.billing_address.title:h} {order.profile.billing_address.firstname:h} {order.profile.billing_address.lastname:h}</a></td>
