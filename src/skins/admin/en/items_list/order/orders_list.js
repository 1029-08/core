/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Orders list controller
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 */

// Orders list class
function OrdersList(cell, URLParams, URLAJAXParams)
{
  if (!cell) {
    return;
  }

  this.constructor.prototype.constructor(cell, URLParams, URLAJAXParams);
}


OrdersList.prototype = new ItemsList();
OrdersList.prototype.constructor = ItemsList;