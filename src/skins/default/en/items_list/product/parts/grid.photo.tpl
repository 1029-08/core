{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Products list (list variant)
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *
 * @ListChild (list="itemsList.product.grid.customer.info", weight="10")
 * @ListChild (list="itemsList.product.small_thumbnails.customer.info", weight="10")
 *}
<div class="product-photo">
  {displayNestedViewListContent(#photo#,_ARRAY_(#product#^product))}
</div>
