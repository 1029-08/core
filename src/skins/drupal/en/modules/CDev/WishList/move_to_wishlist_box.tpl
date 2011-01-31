{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Move to wishlist box
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 * @ListChild (list="cart.item.actions", weight="40")
 *}
<widget class="\XLite\Module\CDev\WishList\View\Form\Cart\Item\MoveToWishlist" name="item_move" item="{item}" cartId="{cart_id}" />
  
  <div class="item-buttons">
    <widget class="\XLite\View\Button\Regular" style="aux-button move-to-wishlist" label="Move to wishlist" />
    <div class="move-quantity" style="display: none;">
      Qty: <input type="text" name="amount" value="{item.amount}" />
    </div>
  </div>

<widget name="item_move" end />
