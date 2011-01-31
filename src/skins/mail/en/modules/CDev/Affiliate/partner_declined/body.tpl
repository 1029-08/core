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
<html>
<head><title>Partner declined</title></head>
<body>
<p>Dear {profile.login}!</p>

<p>Your partner registration has been declined by the shop administrator.</p>

<p IF="profile.reason">Reason: {profile.reason}</p>

<p>{signature:h}</p>
</body>
</html>
