{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Products list sorting control
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *
 * @ListChild (list="itemsList.header")
 *}

<div IF="isSortBySelectorVisible()" class="sort-box">

  <label for="{getSortWidgetId()}">{t(#Sort by#)}</label>
  <select class="sort-crit" id="{getSortWidgetId(true)}">
    <option FOREACH="sortByModes,key,name" value="{key}" selected="{isSortByModeSelected(key)}">{name}</option>
  </select>

  <a href="{getActionURL(_ARRAY_(#sortOrder#^getSortOrderToChange()))}" class="sort-order">{if:isSortOrderAsc()}&darr;{else:}&uarr;{end:}</a>

</div>
