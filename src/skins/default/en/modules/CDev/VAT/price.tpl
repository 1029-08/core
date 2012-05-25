{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Product price value
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *
 * @ListChild (list="product.plain_price", weight="20")
 *}

{if:isVATApplicable()}
<li IF="isDisplayedPriceIncludesVAT()" class="vat-price"><span class="vat-note-product-price">{t(#incl.VAT#)}</span></li>
<li IF="!isDisplayedPriceIncludesVAT()" class="vat-price"><span class="vat-note-product-price">{t(#excl.VAT#)}</span></li>
{end:}
