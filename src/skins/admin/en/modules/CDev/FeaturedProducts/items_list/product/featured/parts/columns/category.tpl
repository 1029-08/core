{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Item category
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 * @ListChild (list="itemsList.product.admin.featured.columns", weight="40")
 *}

<td><a class="category" href="{buildURL(#category#,##,_ARRAY_(#category_id#^product.category.getCategoryId()))}">{product.category.getName():h}</a></td>
