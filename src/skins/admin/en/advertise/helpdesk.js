/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Helpdesk link controller
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 */
jQuery(document).ready(
  function() {
    jQuery('a.helpdesk').click(
      function (event) {
        event.stopPropagation();
        return !displayHelpdeskForm();
      }
    );
  }
);

function displayHelpdeskForm(subject, message)
{
  var form = jQuery('form.helpdesk').eq(0);
  if (!form.length) {
    return false;
  }

  form = form.clone(true);
  var div = jQuery(document.createElement('div'));
  form.show();

  if (subject) {
    jQuery('input[name="subject"]', form).val(subject);
  }

  if (message) {
    jQuery('textarea[name="message"]', form).val(message);
  }

  div.append(form);

  popup.open(div);

  return true;
}

