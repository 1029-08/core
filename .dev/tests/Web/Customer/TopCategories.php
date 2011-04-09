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
 * @package    Tests
 * @subpackage Web
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      1.0.0
 */

require_once __DIR__ . '/ACustomer.php';

class XLite_Web_Customer_TopCategories extends XLite_Web_Customer_ACustomer
{
    /**
     * Test the widget in "list" display mode
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function testListMode()
    {
       $this->testDisplayMode('list');
    }

    /**
     * Test the widgte in "tree" display mode
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function testTreeMode()
    {
        $this->testDisplayMode('tree');
    }

    /**
     * Test the widget in "path" display mode (default mode)
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function testPathMode()
    {
        $this->testDisplayMode('path');
    }

    /**
     * Test the widget in the specified display mode
     * 
     * @param string $mode Display mode ('list', 'path', 'tree')
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function testDisplayMode($mode)
    {
        $this->setDisplayMode($mode);

        $this->openAndWait('store/main');
        $this->testRootCategories($mode);

        $child = $this->getRandomCategory(1);
        $root = is_object($child)
            ? $this->getParentCategory($child->getCategoryId())
            : $this->getRandomCategory(0);

        $this->assertTrue(
            is_object($root),
            'Can\'t find a root category for TopCategories testing (\'' . $mode . '\' mode)'
        );

        $rootName = $root->getName();
        $rootDepth = $this->getRepo()->getCategoryDepth($root->getCategoryId());
        $rootId = $root->getCategoryId();

        // Check whether the random root category is not in the active trail
        $this->assertCategoryNotActiveTrail(
            $rootId,
            '\'' . $rootName . '\' category is in the active trail on the store home page'
        );

        $childId = false;
        if (is_object($child)) {
            $childName = $child->getName();
            $childDepth = $this->getRepo()->getCategoryDepth($child->getCategoryId());
            $childId = $child->getCategoryId();

            if ($mode!='tree') {
               // Make sure the child category is not visible on the home page
                $this->assertCategoryNotVisible(
                    $childId,
                    '\'' . $childName . '\' subcategory is visible on the store home page'
                );
            }
        }

        // Open the root category page
        $rootURL = $this->getJSExpression('jQuery("'.$this->getLinkSelector($rootName, $rootDepth).'")');
        $this->openAndWait($rootURL);
        $this->testRootCategories($mode);

        // Check whether the opened root category is in the active trail
        $this->assertCategoryActiveTrail(
            $rootId,
            'Selected \'' . $rootName . '\' category is not in the active trail'
        );

        // If it is not "list" mode test subcategories ('list' mode doesn't list subcategories at all)
        if ($mode!='list') {

             // Check whether all subcategories of the selected one are shown on the page
            $this->testSubcategories($rootId);

            if ($childId) {
                // Make sure the child category is not in the active trail on the root category page
                $this->assertCategoryNotActiveTrail(
                    $childId,
                    '\'' . $childName . '\' category is in the active trail on the page of its parent category (\'' . $rootName . '\')'
                );

                // Open the child category page
                $childURL = $this->getJSExpression('jQuery("'.$this->getLinkSelector($childName, $childDepth).'")');
                $this->openAndWait($childURL);
                $this->testRootCategories($mode);

                // Check whether the parent root category is still in the active trail
                $this->assertCategoryActiveTrail(
                    $rootId,
                    'Parent \'' . $rootName . '\' category is not in the active trail'
                );

                // Check whether the selected child category is in the active trail
                $this->assertCategoryActiveTrail(
                    $childId,
                    'Child \'' . $childName . '\' category is not in the active trail'
                );

                // Check whether all subcategories of the selected child category are shown on the page
                $this->testSubcategories($childId);
               
            }
        } 

    }

    /**
     * Test whether the widget displays all root categories and has a correct structure
     * 
     * @param string $mode Display mode
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function testRootCategories($mode)
    {
        // Check the widget
        $widgetSelector = $this->getWidgetSelector();
        $this->assertJqueryPresent(
            $widgetSelector,
            'TopCategories widget is missing (\'' . $mode . '\' display mode)'
        );

        // Make sure the widget lists all root categories
        $this->testSubcategories(0);

         if ($mode == 'list') {
            // There should be no links to subcategories
            $this->assertJqueryNotPresent(
                $widgetSelector . ' ul',
                'TopCategories widget displays subcategories (\'' . $mode . '\' display mode)'
            );
        }

        if ($mode=='path') {
            // There should be no links to subcategories beyond the active trail
            $this->assertJqueryNotPresent(
                $widgetSelector . ' li:not(.active-trail) ul',
                'TopCategories widget displays subcategories beyond the active trail (\'' . $mode . '\' display mode)'
            );
        }
    }

    /**
     * Check whether the widget displays all subcategories of a category with the specifeid ID
     * 
     * @param int $categoryId ID of the category that should display all subcategories
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function testSubcategories($categoryId = 0)
    {
       $categories = $this->getSubcategories($categoryId);

        foreach ($categories as $category) {
            $name = $category->getName();
            $depth = $this->getRepo()->getCategoryDepth($category->getCategoryId());

            $selector = $this->getLinkSelector($name, $depth);

            // echo "\nTesting \"$name\" (depth: $depth, parent ID: $categoryId) => selector: $selector\n";

            $this->assertJqueryPresent(
                $selector,
                'Link to \'' . $name . '\' category is missing in TopCategories widget'
            );
        }
    }

    /**
     * Asserts whether the widget displays a link to a category
     * 
     * @param int    $categoryId ID of the category
     * @param string $message    Message to be displayed on an error
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function assertCategoryVisible($categoryId, $message)
    {
        $category = $this->getCategory($categoryId);
        $name = $category->getName();
        $depth = $this->getRepo()->getCategoryDepth($category->getCategoryId());
        $selector = $this->getLinkSelector($name, $depth);
        $this->assertJqueryPresent($selector, $message);
    }

    /**
     * Asserts whether the widget doesn't display a link to a category
     * 
     * @param int    $categoryId ID of the category
     * @param string $message    Message to be displayed on an error
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function assertCategoryNotVisible($categoryId, $message)
    {
        $category = $this->getCategory($categoryId);
        $name = $category->getName();
        $depth = $this->getRepo()->getCategoryDepth($category->getCategoryId());
        $selector = $this->getLinkSelector($name, $depth);
        $this->assertJqueryNotPresent($selector, $message);
    }

    /**
     * Asserts whether the widget displays a category link in the active trail
     * 
     * @param int    $categoryId ID of the category
     * @param string $message    Message to be displayed on an error
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function assertCategoryActiveTrail($categoryId, $message)
    {
        $category = $this->getCategory($categoryId);
        $name = $category->getName();
        $depth = $this->getRepo()->getCategoryDepth($category->getCategoryId());
        $selector = str_replace('li', 'li.active-trail', $this->getLinkSelector($name, $depth));
        $this->assertJqueryPresent($selector, $message);
    }

    /**
     * Asserts whether the widget doesn't display a category link in the active trail
     * 
     * @param int    $categoryId ID of the category
     * @param string $message    Message to be displayed on an error
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function assertCategoryNotActiveTrail($categoryId, $message)
    {
        $category = $this->getCategory($categoryId);
        $name = $category->getName();
        $depth = $this->getRepo()->getCategoryDepth($category->getCategoryId());
        $selector = str_replace('li', 'li.active-trail', $this->getLinkSelector($name, $depth));
        $this->assertJqueryNotPresent($selector, $message);
    }

    /**
     * Returns a jQuery selector to the widget element
     * 
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getWidgetSelector()
    {
        return 'ul.catalog-categories:visible';
    }

    /**
     * Returns a jQuery selector to a link
     * 
     * @param string $name  Category name
     * @param int    $depth Category depth (1 for root categories)
     *  
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getLinkSelector($name, $depth)
    {
        return $this->getWidgetSelector() . ' > li' .str_repeat(' > ul > li', ($depth)) . " a:contains($name)";
    }

    /**
     * Returns a Doctrine repository for the categories model
     * 
     * @return object
     * @access private
     * @see    ____func_see____
     * @since  1.0.0
     */
    private function getRepo()
    {
        return \Xlite\Core\Database::getRepo('XLite\Model\Category');
    }

    /**
     * Returns a category model
     * 
     * @param int $id Category ID
     *  
     * @return \XLite\Model\Category
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getCategory($id = null)
    {
        return $this->getRepo()->getCategory($id);
    }

    /**
     * Returns subcategories model
     * 
     * @param int $categoryId ID of the parent category
     *  
     * @return object
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getSubcategories($categoryId)
    {
        return $this->getRepo()->getSubcategories($categoryId);
    }

    /**
     * Returns a random category of the specified depth
     * 
     * @param int $depth Category depth
     *  
     * @return \XLite\Model\Category
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getRandomCategory($depth = 1)
    {
        $result = null;

        foreach ($this->getRepo()->findAll() as $c) {
            if ($this->getRepo()->getCategoryDepth($c->getCategoryId()) == $depth) {
                $result = $c;
                break;
            }
        }

        $this->assertTrue(
            !is_null($result),
            "Can't find a test category of the $depth depth level"
        );

        return $result;
    }

    /**
     * Returns a parent category
     * 
     * @param int $id ID of the child category
     *  
     * @return \XLite\Model\Category
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getParentCategory($id = 0)
    {
        $parent = $this->getRepo()->getCategory($id)->getParent();
        return ($parent->getCategoryId() > 1) ? $parent : null;
    }

    /**
     * Switches the widget to the specifed display mode
     * 
     * @param string $mode Display mode ('list', 'path' or 'tree')
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function setDisplayMode($mode = 'path')
    {
        $id = $this->findWidgetID('\\XLite\\View\\TopCategories');
        $this->assertFalse(is_null($id), "Can't find the widget in the database");

        $r = $this->query("UPDATE drupal_block_lc_widget_settings SET value='$mode' WHERE bid='$id' AND name='displayMode'");

    }


}
 
