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
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

/**
* Recently viewed page controller
*
* @package LiteCommerce
* @access public
* @version $Id$
*/
class XLite_Module_ProductAdviser_Controller_Customer_RecentlyViewed extends XLite_Controller_Customer_Abstract
{	
	/**
	 * The number of products displayed on widget page
	 * 
	 * @var    integer
	 * @access public
	 * @see    ____var_see____
	 * @since  3.0.0
	 */
	public $productsNumber = 0;


	/**
     * Common method to determine current location 
     * 
     * @return array 
     * @access protected 
     * @since  3.0.0 EE
     */      
    protected function getLocation()
    {
        return 'Recently viewed products';
    }


    /**
     * Get page instance data (name and URL)
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getPageInstanceData()
    {
        $this->target = 'recently_viewed';

        return parent::getPageInstanceData();
    }

    /**
     * Get page type name
     * 
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getTitle()
    {
        return 'Recently viewed products';
    }
}
