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

<div class="items-list widgetclass-{getWidgetClass()} widgettarget-{getWidgetTarget()} sessioncell-{getSessionCell()}">
  <div IF="pager.isVisible()" class="list-pager">{pager.display()}</div>

  <div IF="isHeaderVisible()" class="list-header">{displayViewListContent(#itemsList.admin.header#)}</div>

  <widget template="{getPageBodyTemplate()}" />

  <div IF="pager.isVisible()" class="list-pager">{pager.display()}</div>

  <div IF="isFooterVisible()" class="list-footer">{displayViewListContent(#itemsList.admin.footer#)}</div>

</div>
