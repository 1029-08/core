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
 * @subpackage Controller
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Controller\Customer;

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
abstract class Catalog extends \XLite\Controller\Customer\ACustomer
{
    /**
     * getModelObject
     *
     * @return \XLite\Model\AEntity
     * @access protected
     * @since  3.0.0
     */
    abstract protected function getModelObject();


    /**
     * Return path for the current category
     * 
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getCategoryPath()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Category')->getCategoryPath($this->getCategoryId());
    }

    /**
     * Preprocessor for no-action ren
     *
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (!\XLite\Core\Request::getInstance()->isAJAX()) {
            \XLite\Core\Session::getInstance()->productListURL = $this->getUrl();
        }
    }

    /**
     * Return link to category page
     *
     * @param \XLite\Model\Category $category Category model object to use
     *
     * @return string
     * @access protected
     * @since  3.0.0
     */
    protected function getCategoryURL(\XLite\Model\Category $category)
    {
        return $this->buildURL('category', '', array('category_id' => $category->getCategoryId()));
    }

    /**
     * Prepare subnodes for the location path node
     *
     * @param \XLite\Model\Category $category Node category
     *
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getLocationNodeSubnodes(\XLite\Model\Category $category)
    {
        $nodes = array();

        foreach ($category->getSiblings() as $category) {
            $nodes[] = \XLite\View\Location\Node::create(
                $category->getName(),
                $this->getCategoryURL($category)
            );
        }

        return $nodes;
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        foreach ($this->getCategoryPath() as $category) {
            $this->addLocationNode(
                $category->getName(),
                $this->getCategoryURL($category),
                $this->getLocationNodeSubnodes($category)
            );
        }
    }

    /**
     * Return current (or default) category object
     *
     * @return \XLite\Model\Category
     * @access public
     * @since  3.0.0 EE
     */
    public function getCategory()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category')->getCategory($this->getCategoryId());
    }

    /**
     * Returns the page title (for the content area)
     *
     * @return string
     * @access public
     * @since  3.0.0
     */
    public function getTitle()
    {
        $model = $this->getModelObject();

        return ($model && $model->getName()) ? $model->getName() : parent::getTitle();
    }

    /**
     * Returns the page title (for the <title> tag)
     * 
     * @return string
     * @access public
     * @since  3.0.0
     */
    public function getPageTitle()
    {
        $model = $this->getModelObject();

        return ($model && $model->getMetaTitle()) ? $model->getMetaTitle() : $this->getTitle();
    }

    /**
     * getDescription
     *
     * @return string
     * @access public
     * @since  3.0.0
     */
    public function getDescription()
    {
        $model = $this->getModelObject();

        return $model ? $model->getDescription() : null;
    }

    /**
     * getMetaDescription
     *
     * @return string
     * @access public
     * @since  3.0.0
     */
    public function getMetaDescription()
    {
        $model = $this->getModelObject();

        return $model && $model->getMetaDesc() ? $model->getMetaDesc() : $this->getDescription();
    }

    /**
     * getKeywords
     *
     * @return string
     * @access public
     * @since  3.0.0
     */
    public function getKeywords()
    {
        $model = $this->getModelObject();

        return $model ? $model->getMetaTags() : null;
    }
}
