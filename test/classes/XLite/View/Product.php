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
 * Product details widget
 *
 * @package    XLite
 * @subpackage View
 * @since      3.0
 */
class XLite_View_Product extends XLite_View_Dialog
{
    /**
     * Targets this widget is allowed for
     *
     * @var    array
     * @access protected
     * @since  3.0.0 EE
     */
    protected $allowedTargets = array('product');

    /**
     * Return title
     *
     * @return string
     * @access protected
     * @since  3.0.0 EE
     */
    protected function getHead()
    {
        return $this->getProduct()->get('name');
    }

    /**
     * Return templates directory name
     *
     * @return string
     * @access protected
     * @since  3.0.0 EE
     */
    protected function getDir()
    {
        return 'product_details';
    }

    /**
     * Check if widget is visible
     *
     * @return bool
     * @access protected
     * @since  3.0.0 EE
     */
    public function isVisible()
    {
        return parent::isVisible()
            && $this->getProduct()->is('available');
    }

    /**
     * Get previous product 
     * 
     * @return XLite_Model_Product
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getPreviousProduct()
    {
        if (!isset($this->previousProduct)) {
            $this->detectPrevNext();
        }

        return $this->previousProduct;
    }

    /**
     * Get next product 
     * 
     * @return XLite_Model_Product
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getNextProduct()
    {
        if (!isset($this->nextProduct)) {
            $this->detectPrevNext();
        }

        return $this->nextProduct;
    }

    /**
     * Detect previous and next product
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function detectPrevNext()
    {
        $this->previousProduct = false;
        $this->nextProduct = false;
        $currentProduct = $this->getProduct();
        $found = false;
        $prev = false;
        foreach ($this->getCategory()->getProducts() as $p) {
            if ($found) {
                $this->nextProduct = $p;
                break;
            }
            if ($currentProduct->get('product_id') == $p->get('product_id')) {
                $this->previousProduct = $prev;
                $found = true;
            }
            $prev = $p;
        }
    }
}

