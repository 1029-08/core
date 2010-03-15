/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Send to friend popup
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 */

// Open Send to friend popup
function openSendToFriendPopup(event)
{
  var result = false;

  var productId = getProductIdFromClassName(this);
  if (productId) {
    result = !!openBlockUIPopup('send_friend', '', {product_id: productId});
  }

  return !result;
}

// Document onready event handler
$(document).ready(
  function() {
    $('a.send-to-friend').click(openSendToFriendPopup);
  }
);
