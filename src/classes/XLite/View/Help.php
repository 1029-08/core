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
 * @subpackage View
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\View;

/**
 * Help dialog
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 *
 * @ListChild (list="center")
 */
class Help extends \XLite\View\SectionDialog
{
    /**
     * Define sections list
     * 
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function defineSections()
    {
        return array(
            'terms_conditions' => array(
                'head' => 'Terms and conditions',
                'body' => 'terms_conditions.tpl',
            ),
            'privacy_statement' => array(
                'head' => 'Privacy statement',
                'body' => 'privacy_statement.tpl',
            ),
            'contactus' => array(
                'head' => 'Contact us',
                'body' => 'contactus.tpl',
            ),
            'contactusMessage' => array(
                'head' => 'Message is sent',
                'body' => 'contactus_message.tpl',
            ),
        );
    }

    /**
     * Return list of allowed targets
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'help';

        return $list;
    }

}
