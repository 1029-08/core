{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Simple form for searching products template
 *   
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *}

<div class="simple-search-product-form">
  <widget class="\XLite\View\Form\Search\Product\Simple" name="simple_products_search" />
    <div class="simple-search-box">
      <input type="text" class="form-text" size="30" name="substring" value="{substring}" title="{t(#Search#)}" />
      <button type="submit" class="submit-button">{t(#Search#)}</button>
    </div>
  <widget name="simple_products_search" end />
</div>
