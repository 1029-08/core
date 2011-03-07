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

namespace XLite\View\FormField\Select;
 
/**
 * Category selector
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Categories extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Return field template
     *
     * @return string
     * @access protected
     * @since  3.0.0
     */
    protected function getFieldTemplate()
    {
        return 'select_category.tpl';
    }

    /**
     * Return default options list
     * 
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getDefaultOptions()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Category')->getCategories();
    }

    /**
     * isCategorySelected 
     * 
     * @param integer $categoryId Cateory ID to check
     *  
     * @return boolean 
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function isCategorySelected($categoryId)
    {
        return (bool) \Includes\Utils\ArrayManager::searchInObjectsArray(
            $this->getValue(),
            'category_id',
            $categoryId,
            false
        );
    }

    /**
     * getIndentation 
     * 
     * @param \XLite\Model\Category $category   Category model object
     * @param integer               $multiplier Level's multiplier
     *  
     * @return integer 
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getIndentation(\XLite\Model\Category $category, $multiplier)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Category')->getCategoryDepth(
            $category->getCategoryId()
        ) * $multiplier - 1;
    }
}
