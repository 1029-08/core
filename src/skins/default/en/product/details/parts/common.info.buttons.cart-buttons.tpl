{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Product details buttons block
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *
 * @ListChild (list="product.details.page.info.buttons", weight="10")
 * @ListChild (list="product.details.page.info.buttons-added", weight="10")
 * @ListChild (list="product.details.quicklook.info.buttons", weight="20")
 * @ListChild (list="product.details.quicklook.info.buttons-added", weight="20")
 *}

<div class="buttons-row" IF="!product.inventory.isOutOfStock()">
  {displayNestedViewListContent(#cart-buttons#)}
</div>
