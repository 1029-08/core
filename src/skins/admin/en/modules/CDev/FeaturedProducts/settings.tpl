{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * ____file_title____
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *}
{if:option.name=#featured_products_look#}
  <select name="{option.name}">
    <option value="list" selected="{option.value=#list#}">List</option>
    <option value="grid" selected="{option.value=#grid#}">Grid</option>
    <option value="table" selected="{option.value=#table#}">Table</option>
  </select>
{end:}
