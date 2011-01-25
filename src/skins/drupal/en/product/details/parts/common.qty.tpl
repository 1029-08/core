{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Quantity input box
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *
 * @ListChild (list="product.details.page.info.buttons.cart-buttons", weight="0")
 * @ListChild (list="product.details.page.info.buttons-added.cart-buttons", weight="0")
 * @ListChild (list="product.details.quicklook.info.buttons", weight="10")
 * @ListChild (list="product.details.quicklook.info.buttons-added", weight="10")
 *}
<span class="product-qty">{t(#Qty#)}: <input type="text" value="{product.getMinPurchaseLimit()}" class="quantity field-requred field-integer field-positive field-non-zero" name="amount" title="{t(#Quantity#)}" /></span>
