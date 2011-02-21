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

namespace XLite\Model;

/**
 * Category
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 *
 * @Entity (repositoryClass="\XLite\Model\Repo\Category")
 * @Table  (name="categories",
 *      indexes={
 *          @Index (name="lpos", columns={"lpos"}),
 *          @Index (name="rpos", columns={"rpos"}),
 *          @Index (name="enabled", columns={"enabled"}),
 *          @Index (name="cleanURL", columns={"cleanURL"})
 *      }
 * )
 */
class Category extends \XLite\Model\Base\I18n
{
    /**
     * Node unique ID 
     * 
     * @var    integer
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="uinteger")
     */
    protected $category_id;

    /**
     * Node left value 
     * 
     * @var    integer
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     *
     * @Column (type="integer")
     */
    protected $lpos;

    /**
     * Node right value 
     * 
     * @var    integer
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     *
     * @Column (type="integer")
     */
    protected $rpos;

    /**
     * Node status
     * 
     * @var    boolean
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Node clean (SEO-friendly) URL
     * 
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     *
     * @Column (type="string", length="255")
     */
    protected $cleanURL = '';

    /**
     * Whether to display the category title, or not
     * 
     * @var    integer
     * @access protected
     * @since  3.0.0
     *
     * @Column (type="boolean")
     */
    protected $show_title = true;

    /**
     * Some cached flags
     * 
     * @var    \XLite\Model\Category\QuickFlags
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     * 
     * @OneToOne (targetEntity="XLite\Model\Category\QuickFlags", mappedBy="category", cascade={"all"})
     */
    protected $quickFlags;

    /**
     * Many-to-one relation with memberships table
     * 
     * @var    \Doctrine\Common\Collections\ArrayCollection
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     *
     * @ManyToOne  (targetEntity="XLite\Model\Membership")
     * @JoinColumn (name="membership_id", referencedColumnName="membership_id")
     */
    protected $membership;

    /**
     * One-to-one relation with category_images table
     * 
     * @var    \XLite\Model\Image\Category\Image
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     *
     * @OneToOne  (targetEntity="XLite\Model\Image\Category\Image", mappedBy="category", cascade={"all"})
     */
    protected $image;

    /**
     * Relation to a CategoryProducts entities
     * 
     * @var    \Doctrine\Common\Collections\ArrayCollection
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     *
     * @OneToMany (targetEntity="XLite\Model\CategoryProducts", mappedBy="category", cascade={"all"})
     * @OrderBy   ({"orderby" = "ASC"})
     */
    protected $categoryProducts;

    /**
     * Child categories
     * 
     * @var    \Doctrine\Common\Collections\ArrayCollection
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     *
     * @OneToMany (targetEntity="XLite\Model\Category", mappedBy="parent", cascade={"all"})
     */
    protected $childs;

    /**
     * Parent category
     * 
     * @var    \XLite\Model\Category
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     *
     * @ManyToOne  (targetEntity="XLite\Model\Category", inversedBy="childs")
     * @JoinColumn (name="parent_id", referencedColumnName="category_id")
     */
    protected $parent;

    /**
     * Set parent 
     * 
     * @param \XLite\Model\Category $parent Parent category
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function setParent(\XLite\Model\Category $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Set image 
     * 
     * @param \XLite\Model\Image\Category\Image $image Image
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function setImage(\XLite\Model\Image\Category\Image $image = null)
    {
        $this->image = $image;
    }

    /**
     * Check if category has image 
     * 
     * @return boolean 
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function hasImage()
    {
        return !is_null($this->getImage());
    }

    /**
     * Get the number of subcategories 
     * 
     * @return integer|void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getSubCategoriesCount()
    {
        $method = 'getSubcategoriesCount'
            . ($this->getRepository()->getEnabledCondition() ? 'Enabled' : 'All');

        // $method assembled from 'getSubcategoriesCount' + 'Enabled' or 'All'
        return $this->getQuickFlags() ? $this->getQuickFlags()->$method() : null;
    }

    /**
     * Check if category has subcategories
     * 
     * @return boolean 
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function hasSubcategories()
    {
        return 0 < $this->getSubCategoriesCount();
    }

    /**
     * Return subcategories list
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getSubcategories()
    {
        return $this->getChilds();
    }

    /**
     * Return siblings list
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getSiblings()
    {
        return $this->getRepository()->getSiblings($this);
    }

    /**
     * Gets full path to the category as a string: <parent category>/.../<category name>
     * 
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getStringPath()
    {
        $path = array();

        foreach ($this->getRepository()->getCategoryPath($this->getCategoryId()) as $category) {
            $path[] = $category->getName();
        }

        return implode('/', $path);
    }

    /**
     * Return number of products associated with the category
     *
     * TODO: check if result of "getProducts()" is cached by Doctrine
     * 
     * @return integer 
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getProductsCount()
    {
        return count($this->getProducts());
    }

    /**
     * Return products list
     * 
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *  
     * @return array|integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getProducts(\XLite\Core\CommonCell $cnd = null, $countOnly = false)
    {
        if (!isset($cnd)) {
            $cnd = new \XLite\Core\CommonCell();
        }

        // Main condition for this search
        $cnd->{\XLite\Model\Repo\Product::P_CATEGORY_ID} = $this->getCategoryId();

        return \XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd, $countOnly);
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function __construct(array $data = array())
    {
        $this->categoryProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->childs = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }
}
