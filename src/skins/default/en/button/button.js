/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * ____file_title____
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 */

function setFormAttribute(form, name, value)
{   
  form.elements[name].value = value;
}

function setFormAction(form, action)
{   
    setFormAttribute('action', action);
}

function submitForm(form, attrs)
{
	for (name in attrs) {
		if (form.elements[name]) {
			form.elements[name].value = attrs[name];
		}
	}

	form.submit();
}

function submitFormDefault(form, action)
{
	var attrs = [];
	attrs['action'] = action;

	submitForm(form, attrs);
}

