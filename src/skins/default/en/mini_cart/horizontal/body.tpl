{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Minicart (horizontal)
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *}
<div id="lc-minicart-{getParam(%static::PARAM_DISPLAY_MODE%)}" class="lc-minicart lc-minicart-{getParam(%static::PARAM_DISPLAY_MODE%)} {getCollapsed()}">

  {displayViewListContent(#minicart.horizontal.children#)}

</div>

{displayViewListContent(#minicart.horizontal.base#)}
