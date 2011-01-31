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
 * @subpackage Model
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Module\CDev\AOM\Model;

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class OrderStatus extends \XLite\Model\AModel
{
    public $fields = array(
        'status_id'	=> '',
        'status'	=> '',
        'name'		=> '',
        'notes'		=> '',
        'parent'	=> '',
        'email'		=> 	0, 
        'cust_email' => 0,
        'orderby'	=> '');
        
    public $alias		= 'order_statuses';
    public $primaryKey = array('status_id');
    public $defaultOrder = "orderby, parent, status";

    function getParentStatus()
    {
        if ($this->get('parent') == '')	{
            return null;
        } else {
            $parent = new \XLite\Module\CDev\AOM\Model\OrderStatus();
            $parent->find("status = '". $this->get('parent') ."'");
            return $parent;
        }
    }
}
