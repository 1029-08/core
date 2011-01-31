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

namespace XLite\Module\CDev\Promotion\Model;

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class BonusPrice extends \XLite\Model\AModel
{
    public $fields = array(
        "offer_id" => 0,
        "product_id" => 0,
        "category_id" => 0,
        "bonusType" => '$',
        "price" => 0);
    public $alias = "special_offer_bonusprices";
    public $primaryKey = array('offer_id',"product_id");

    function getProduct()
    {
        if ($this->get('product_id')) {
            $product = new \XLite\Model\Product($this->get('product_id'));
        } else {
            $product = null;
        }
        return $product;
    }

    function getCategory()
    {
        if ($this->get('category_id')) {
            $category = new \XLite\Model\Category($this->get('category_id'));
        } else {
            $category = null;
        }
        return $category;
    }

}
