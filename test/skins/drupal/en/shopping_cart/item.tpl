{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Cart item widget
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<td class="delete-from-cart">
  <widget class="XLite_View_Button_Image" label="Delete item" action="delete" formParams="{_ARRAY_(#cart_id#^cart_id)}" />
</td>

<td class="item-thumbnail" IF="item.hasThumbnail()">
  <a href="{item.url}"><img src="{item.thumbnailURL}" alt="{item.name}" /></a>
</td>

<td class="item-info">
  <p class="item-title"><a href="{item.url}">{item.name}</a></p>
  <p class="item-sku" IF="{item.sku}">SKU: {item.sku}</p>
  <p class="item-weight" IF="{item.weight}">Weight: {item.weight} {config.General.weight_symbol}</p>
  <div class="item-options">
    <widget module="ProductOptions" class="XLite_Module_ProductOptions_View_SelectedOptions" item="{item}" source="cart" item_id="{cart_id}"/>
  </div>
</td>

<td class="item-actions">

  <div class="item-sums">
    <span class="item-price">{price_format(item,#price#):h}</span>
    <span class="sums-multiply">x</span>
    <span class="item-quantity"><input type="text" name="amount[{cart_id}]" value="{item.amount}" size="3" maxlength="6" /></span>
    <span class="sums-equals">=</span>
    <span class="item-subtotal">{price_format(item,#total#):h}</span>
  </div>

  <p class="cart-error-message" IF="!item.valid">(!) This product is out of stock or it has been disabled for sale.</p>
  <widget module="GoogleCheckout" template="modules/GoogleCheckout/shopping_cart/item.tpl" />

  <div class="item-buttons">
    <span class="move-to-wishlist"><a href="{buildUrl(#wishlist#,#move#,_ARRAY_(#cart_id#^cart_id))}">Move to wishlist</a></span>
  </div>

</td>
