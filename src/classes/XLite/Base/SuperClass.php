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
 * @subpackage Base
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Base;

/**
 * SuperClass 
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
abstract class SuperClass
{
    /**
     * Protected constructor.
     * It's not possible to instantiate a derived class (using the "new" operator) 
     * until that child class is not implemented public constructor
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function __construct()
    {
    }


    /**
     * Stop script execution
     * FIXME - must be static
     *
     * @param string $message Text to display
     *
     * @return void
     * @access protected
     * @since  3.0
     */
    protected function doDie($message)
    {
        if (!($this instanceof \XLite\Logger)) {
            \XLite\Logger::getInstance()->log($message, PEAR_LOG_ERR);
        }

        if (
            $this instanceof XLite
            || \XLite::getInstance()->getOptions(array('log_details', 'suppress_errors'))
        ) {
            $message = 'Internal error. Contact the site administrator.';
        }

        die ($message);
    }

    /**
     * Language label translation short method
     *
     * @param string $name      Label name
     * @param array  $arguments Substitution arguments
     * @param string $code      Language code OPTIONAL
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected static function t($name, array $arguments = array(), $code = null)
    {
        return \XLite\Core\Translation::getInstance()->translate($name, $arguments, $code);
    }
}
