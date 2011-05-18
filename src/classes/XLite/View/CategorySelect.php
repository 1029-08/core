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
 * PHP version 5.3.0
 *
 * @category  LiteCommerce
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\View;


/**
 * Category selector
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class CategorySelect extends \XLite\View\AView
{
    /**
     * Category selector options constants
     */
    const PARAM_ALL_OPTION           = 'allOption';
    const PARAM_NONE_OPTION          = 'noneOption';
    const PARAM_ROOT_OPTION          = 'rootOption';
    const PARAM_FIELD_NAME           = 'fieldName';
    const PARAM_SELECTED_CATEGORY_ID = 'selectedCategoryId';
    const PARAM_CURRENT_CATEGORY_ID  = 'currentCategoryId';
    const PARAM_IGNORE_CURRENT_PATH  = 'ignoreCurrentPath';


    /**
     * categories
     *
     * @var   mixed
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $categories = null;

    /**
     * field
     *
     * @var   mixed
     * @see   ____var_see____
     * @since 1.0.0
     */
    public $field;

    /**
     * formName
     *
     * @var   mixed
     * @see   ____var_see____
     * @since 1.0.0
     */
    public $formName;

    /**
     * selectedCategory
     *
     * @var   mixed
     * @see   ____var_see____
     * @since 1.0.0
     */
    public $selectedCategory = null;


    /**
     * Get categories list
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getCategories()
    {
        $this->categories = \XLite\Core\Database::getRepo('\XLite\Model\Category')->getCategories();

        $categoryId = $this->getParam(self::PARAM_CURRENT_CATEGORY_ID);

        if (
            !empty($this->categories)
            && 0 < $categoryId
            && $this->getParam(self::PARAM_IGNORE_CURRENT_PATH)
        ) {

            $currentCategory = \XLite\Core\Database::getRepo('\XLite\Model\Category')->getCategory($categoryId);

            $categories = array();

            if (isset($currentCategory)) {
                foreach ($this->categories as $id => $category) {
                    if (!($category->lpos >= $currentCategory->lpos && $category->rpos <= $currentCategory->rpos)) {
                        $categories[] = $category;
                    }
                }
            }

            $this->categories = $categories;
        }

        return $this->categories;
    }

    /**
     * Check - display 'No categories' option or not
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isDisplayNoCategories()
    {
        return !$this->getParam(self::PARAM_ALL_OPTION) && !$this->getCategories();
    }


    /**
     * Return widget default template
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDefaultTemplate()
    {
        return 'common/select_category.tpl';
    }

    /**
     * Define widget parameters
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ALL_OPTION           => new \XLite\Model\WidgetParam\Bool('Display All option', false),
            self::PARAM_NONE_OPTION          => new \XLite\Model\WidgetParam\Bool('Display None option', false),
            self::PARAM_ROOT_OPTION          => new \XLite\Model\WidgetParam\Bool('Display [Root level] option', false),
            self::PARAM_FIELD_NAME           => new \XLite\Model\WidgetParam\String('Field name', ''),
            self::PARAM_SELECTED_CATEGORY_ID => new \XLite\Model\WidgetParam\Int('Selected category id', 0),
            self::PARAM_CURRENT_CATEGORY_ID  => new \XLite\Model\WidgetParam\Int('Current category id', 0),
            self::PARAM_IGNORE_CURRENT_PATH  => new \XLite\Model\WidgetParam\Bool('Ignore current path', false),
        );
    }

    /**
     * Get categories condition
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getCategoriesCondition()
    {
        return array(null, null, null, null);
    }

    /**
     * Check - specified category selected or not
     *
     * @param \XLite\Model\Category $category Category
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function isCategorySelected(\XLite\Model\Category $category)
    {
        $categoryId = $this->getParam(self::PARAM_SELECTED_CATEGORY_ID);

        if (!is_numeric($categoryId) || 1 > $categoryId) {
            $categoryId = 0;
        }

        return $category->category_id == $categoryId;
    }

    /**
     * getSelectedCategory
     * TODO: check if we need this function
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getSelectedCategory()
    {
        if (is_null($this->selectedCategory) && !is_null($this->field)) {
            $this->selectedCategory = $this->get('component.' . $this->field);
        }

        return $this->selectedCategory;
    }

    /**
     * setFieldName
     * TODO: check if we need this function
     *
     * @param string $name Field name
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function setFieldName($name)
    {
        $this->formField = $name;

        $pos = strpos($name, '[');

        $this->field = (false === $pos) ? $name : substr($name, $pos + 1, -1);
    }

    /**
     * getIndentation
     *
     * @param \XLite\Model\Category $category   Category model object
     * @param integer               $multiplier Level's multiplier
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getIndentation(\XLite\Model\Category $category, $multiplier)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Category')->getCategoryDepth(
            $category->getCategoryId()
        ) * $multiplier - 1;
    }
}
