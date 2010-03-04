{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Products list template
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<ul class="list-body list-body-list">

  <li FOREACH="getData(),product" class="item">

    <widget class="XLite_View_AddedToCartMark" product="{product}" />
    <a IF="config.General.show_thumbnails&product.hasThumbnail()" class="product-thumbnail" href="{buildURL(#product#,##,_ARRAY_(#product_id#^product.product_id,#category_id#^category_id))}"><img src="{product.thumbnailURL}" alt="" /></a>
    <div class="body">
      <a href="{buildURL(#product#,##,_ARRAY_(#product_id#^product.product_id,#category_id#^category_id))}" class="product-name">{product.name:h}</a>
      <br />
      <div IF="isShowDescription()" class="product-description">{truncate(product,#brief_description#,#300#):h}</div>
      <widget class="XLite_View_Price" product="{product}" displayOnlyPrice="true" IF="isShowPrice()" />
      <widget class="XLite_View_BuyNow" product="{product}" IF="isShowAdd2Cart()" />
    </div>

  </li>

</ul>
