{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Vertical minicart items block_
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 * @ListChild (list="#minicart.vertical.children", weight="10")
 *}
<div class="cart-items" IF="cart.isEmpty()">
  <p class="cart.isEmpty()">{t(#Cart is empty#)}</p>
</div>

<div class="cart-items" IF="!cart.isEmpty()">
  <p><span class="toggle-button"><a href="{buildURL(#cart#)}" onClick="javascript:xlite_minicart_toggle('lc-minicart-{displayMode}'); return false;">{t(#X item(s)#,_ARRAY_(#count#^cart.getItemsCount()))}</a></span></p>
  <div class="items-list">
    <ul>
      <li FOREACH="getItemsList(),item">
        {displayViewListContent(#minicart.vertical.item#,_ARRAY_(#item#^item))}
      </li>
    </ul>
    <p IF="isTruncated()" class="other-items"><a href="{buildURL(#cart#)}">{t(#Other items#)}</a></p>
  </div>
</div>
