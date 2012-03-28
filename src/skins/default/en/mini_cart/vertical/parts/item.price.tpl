{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Display vertical minicart item price
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 * @ListChild (list="minicart.vertical.item", weight="10")
 *}
<span class="item-price"><widget class="XLite\VIew\Surcharge" surcharge="{item.getNetPrice()}" currency="{cart.getCurrency()}" /></span>
<span class="delimiter">&times;</span>
<span class="item-qty">{item.getAmount()}</span>
