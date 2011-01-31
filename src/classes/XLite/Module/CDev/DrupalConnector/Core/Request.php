<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * LiteCommerce
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@litecommerce.com so we can send you a copy immediately.
 * 
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage Core
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Module\CDev\DrupalConnector\Core;

/**
 * Request 
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Request extends \XLite\Core\Request implements \XLite\Base\IDecorator
{
    /**
     * Wrapper for sanitize()
     *
     * @param mixed $data Data to sanitize
     *
     * @return mixed
     * @access protected
     * @since  3.0.0
     */
    protected function prepare($data)
    {
        $data = parent::prepare($data);

        // Fix double-escaping problems caused by "magic quotes" for a stand-alone mode and admin side
        if (!\XLite\Module\CDev\DrupalConnector\Handler::isCMSStarted() && 1 === get_magic_quotes_gpc()) {
            $data = $this->doUnescape($data);
        }

        return $data;
    }
}
