{* SVN $Id$ *}
<div id="lc-minicart-{displayMode}" class="lc-minicart-{displayMode} {collapsed}">

  <div class="cart-link">
    <a href="{buildURL(#cart#)}"><img src="images/spacer.gif" width="16" height="16" /></a>
  </div>

  <div class="cart-items" IF="cart.empty">
    <p class="cart-empty">Cart is empty</p>
  </div>

  <div class="cart-items" IF="!cart.empty">
    <p><span class="toggle-button"><a href="{buildURL(#cart#)}" onClick="javascript:xlite_minicart_toggle('lc-minicart-{displayMode}'); return false;">{cart.getItemsCount()} item(s)</a> </span></p>
    <div class="items-list">
      <ul>
        <li FOREACH="getItemsList(),item">
          <span class="item-name"><a href="{buildURL(#product#,##,_ARRAY_(#product_id#^item.product_id,#category_id#^item.category_id))}">{item.name}</a></span>
          <span class="item-price">{price_format(item,#price#):h}</span><span class="delimiter">x</span><span class="item-qty">{item.amount}</span>
        </li>
      </ul>
      <p IF="isTruncated()" class="other-items"><a href="{buildURL(#cart#)}">Other items</a></p>
    </div>
  </div>

  <div class="cart-totals" IF="!cart.empty">
    <p><span class="delimiter">/</span><span class="cart-total">{price_format(cart,#total#):h}</span></p>
  </div>

  <div id="lc-minilist-{displayMode}" class="lc-minilist lc-minilist-{displayMode}" IF="countWishlistProducts()">
    <p><a href="{buildURL(#wishlist#)}">Wish list: {countWishlistProducts()} item(s)</a></p>
  </div>

  <div class="cart-checkout" IF="!cart.empty">
    <widget class="XLite_View_Button_Link" label="Checkout" location="{buildURL(#checkout#)}">
  </div>

</div>
