{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * ____file_title____
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
{if:membership_history}
<a IF="mode=#modify#" href="javascript: if ( confirm('Are you sure you want to change mempership?') ) grantMembership()"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"> <font class="FormButton">Grant membership</font></a>
{else:}
<a IF="mode=#modify#" href="javascript: grantMembership()"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"> <font class="FormButton">Grant membership</font></a>
{end:}
