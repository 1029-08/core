{* SVN $Id$ *}
<tbody>

  <tr>
    <td colspan=2>&nbsp;</td>
  </tr>

  <tr id="optionsTitle">
    <td class="ProductDetailsTitle" colspan="2">Options</td>
  </tr>

  <tr>
    <td class="Line" height="1" colspan="2"><img src="images/spacer.gif" width="1" height="1" alt="" /></td>
  </tr>

  <tr>
    <td colspan=2>&nbsp;</td>
  </tr>

  <tr IF="product.hasExpandedOptions()">
    <td colspan="2">

      <table cellpadding="0" cellspacing="0" bgcolor="#cccccc">

        <tr bgcolor="#ffffff">
        	<td>&nbsp;</td>
        	<td FOREACH="product.expandedOptionsNames,option" nowrap>
    	      <strong>{option:h}</strong>&nbsp;&nbsp;
        	</td>
        	<td IF="product.xlite.mm.activeModules.InventoryTracking"><strong>Quantity in stock</strong></td>
        	<td IF="product.is(#priceAvailable#)" align="right"><strong>Price</strong></td>
        </tr>

        <tbody FOREACH="product.expandedItems,key,opts">

          <tr>
          	<td FOREACH="opts,o" class="Line" height=1><img src="images/spacer.gif" width=1 height=1 border=0 alt=""></td>
            <td class="Line" height=1><img src="images/spacer.gif" width=1 height=1 border=0 alt=""></td>
	          <td IF="product.xlite.mm.activeModules.InventoryTracking" class="Line" height=1><img src="images/spacer.gif" width=1 height=1 border=0 alt=""></td>
          	<td IF="product.is(#priceAvailable#)" class="Line" height=1><img src="images/spacer.gif" width=1 height=1 border=0 alt=""></td>
          </tr>

          <tr bgcolor="#ffffff">
          	<td>
            	<input type="radio" name="OptionSetIndex[{product.product_id}]" value="{key}" checked="{option_selected(product.product_id,key)}">
          	</td>
          	<td FOREACH="opts,option" nowrap height="25">
            	{option.option}&nbsp;&nbsp;
          	</td>
          	<td nowrap align="left" IF="product.xlite.mm.activeModules.InventoryTracking">
            	{if:product.getAmountByOptions(key)=-1}
	              unlimited
            	{else:}
              	{product.getAmountByOptions(key)}
            	{end:}
          	</td>
          	<td nowrap IF="product.is(#priceAvailable#)" align="left">
            	&nbsp;&nbsp;{price_format(product.getFullPrice(#1#,key)):h}
	          </td>
          </tr>
        </tbody>

      </table>

    </td>
  </tr>

  <tr>
    <td colspan=2>&nbsp;</td>
  </tr>

  <tr FOREACH="product.flatOptions,option">
	  <td IF="option.opttype=#Text#"  width="30%" height=25 valign=middle class="ProductDetails">{option.opttext:h}:&nbsp;</td>
  	<td IF="option.opttype=#Textarea#"  width="30%" height=25 valign=top class="ProductDetails">{option.opttext:h}:&nbsp;</td>
    <td IF="option.empty">
      <input type="text" IF="option.opttype=#Text#" name="product_options[{option.optclass:h}]" value="" size="{option.cols}"/>
      <textarea IF="option.opttype=#Textarea#" cols="{option.cols}" rows="{option.rows}" name="product_options[{option.optclass:h}]"></textarea>
    </td>
  </tr>

</tbody>

<widget module="ProductOptions" template="modules/ProductOptions/options_validation_js.tpl">
<widget module="ProductOptions" template="modules/ProductOptions/options_exception.tpl">
