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
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     3.0.0
 */

namespace XLite\View\ItemsList\Product\Customer;

/**
 * Search 
 * 
 * @see   ____class_see____
 * @since 3.0.0
 */
class Search extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    /**
     * Widget param names 
     */
    const PARAM_SUBSTRING         = 'substring';
    const PARAM_CATEGORY_ID       = 'categoryId';
    const PARAM_SEARCH_IN_SUBCATS = 'searchInSubcats';
    const PARAM_INCLUDING         = 'including';
    const PARAM_BY_TITLE          = 'by_title';
    const PARAM_BY_DESCR          = 'by_descr';
    const PARAM_BY_SKU            = 'by_sku';


    /**
     * Widget target 
     */
    const WIDGET_TARGET = 'search';


    /**
     * Return search parameters TODO refactor with XLite\View\ItemsList\Product\Admin\Search::getSearchParams() 
     * 
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    static public function getSearchParams()
    {
        return array(
            \XLite\Model\Repo\Product::P_SUBSTRING   => self::PARAM_SUBSTRING,
            \XLite\Model\Repo\Product::P_CATEGORY_ID => self::PARAM_CATEGORY_ID,
            \XLite\Model\Repo\Product::P_INCLUDING   => self::PARAM_INCLUDING,
            \XLite\Model\Repo\Product::P_BY_TITLE    => self::PARAM_BY_TITLE,
            \XLite\Model\Repo\Product::P_BY_DESCR    => self::PARAM_BY_DESCR,
            \XLite\Model\Repo\Product::P_BY_SKU      => self::PARAM_BY_SKU,
        );
    }

    /** 
     * Return list of targets allowed for this widget
     *
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = self::WIDGET_TARGET;
    
        return $result;
    }


    /** 
     * Return target to retrive this widget from AJAX
     * 
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected static function getWidgetTarget()
    {
        return self::WIDGET_TARGET;
    }


    /** 
     * Returns CSS classes for the container element
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' products-search-result';
    }

    /** 
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        // Static call of the non-static function
        $list[] = parent::getDir() . '/search/search.css';

        return $list;
    }

    /** 
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        // Static call of the non-static function
        $list[] = parent::getDir() . '/search/controller.js';

        return $list;
    }


    /**
     * Return title
     *
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getHead()
    {
        return $this->getItemsCount() . ' products found';
    }

    /** 
     * Check if head title is visible
     * 
     * @return boolean
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function isHeadVisible()
    {
        return true;
    }

    /** 
     * Check if header is visible
     * 
     * @return boolean
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function isHeaderVisible()
    {   
        return 0 < $this->getItemsCount();
    }   

    /**
     * Check if pager is visible
     * 
     * @return boolean
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function isPagerVisible()
    {
        return 0 < $this->getItemsCount();
    }

    /**
     * isFooterVisible
     *
     * @return boolean 
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function isFooterVisible()
    {
        return true;
    }

    /**
     * Search widget must be visible always.
     * 
     * @return boolean
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function isVisible()
    {
        return true;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getPagerClass()
    {
        return '\XLite\View\Pager\Customer\Product\Search';
    }

    /**
     * Get URL common parameters
     *
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getCommonParams()
    {
        return parent::getCommonParams() + array('mode' => 'search');
    }

    /**
     * Define widget parameters TODO refactor with XLite\View\ItemsList\Product\Admin\Search::defineWidgetParams()
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_SUBSTRING => new \XLite\Model\WidgetParam\String(
                'Substring', ''
            ),
            self::PARAM_CATEGORY_ID => new \XLite\Model\WidgetParam\Int(
                'Category ID', 0
            ),
            self::PARAM_INCLUDING => new \XLite\Model\WidgetParam\Set(
                'Including', 
                \XLite\Model\Repo\Product::INCLUDING_PHRASE, 
                array(
                    \XLite\Model\Repo\Product::INCLUDING_ALL,
                    \XLite\Model\Repo\Product::INCLUDING_ANY,
                    \XLite\Model\Repo\Product::INCLUDING_PHRASE,
                )
            ),
            self::PARAM_BY_TITLE => new \XLite\Model\WidgetParam\Checkbox(
                'Search in title', 0
            ),
            self::PARAM_BY_DESCR => new \XLite\Model\WidgetParam\Checkbox(
                'Search in description', 0
            ),
            self::PARAM_BY_SKU => new \XLite\Model\WidgetParam\String(
                'Search in SKU', 0
            ),  
        );
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge(
            $this->requestParams, 
            \XLite\View\ItemsList\Product\Customer\Search::getSearchParams()
        );
    }

    /**
     * Return params list to use for search TODO refactor with XLite\View\ItemsList\Product\Admin\Search::getSearchCondition()
     *   
     * @return \XLite\Core\CommonCell
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (\XLite\View\ItemsList\Product\Customer\Search::getSearchParams() as $modelParam => $requestParam) {

            $paramValue = $this->getParam($requestParam);
 
            if ('' !== $paramValue && 0 !== $paramValue) {
 
                $result->$modelParam = $paramValue;

            }

        }

        return $result;
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Product')->search(
            $this->prepareCnd($cnd), 
            $countOnly
        );
    }

    /**
     * Prepare search condition before search
     * 
     * @param \XLite\Core\CommonCell $cnd Search condition
     *  
     * @return \XLite\Core\CommonCell
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function prepareCnd(\XLite\Core\CommonCell $cnd)
    {
        // In the Customer zone we search in subcategories always.
        $cnd->{\XLite\Model\Repo\Product::P_SEARCH_IN_SUBCATS} = 'Y';

        return $cnd;
    }
}
