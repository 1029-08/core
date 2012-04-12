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

namespace XLite\Controller\Customer;

/**
 * Abstract controller for Customer interface
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
abstract class ACustomer extends \XLite\Controller\AController
{
    /**
     * cart
     *
     * @var   \XLite\Model\Cart
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $cart;

    /**
     * Initial cart fingerprint
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $initialCartFingerprint;

    /**
     * Breadcrumbs
     *
     * @var   \XLite\View\Location
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $locationPath;

    // {{{ Breadcrumbs

    /**
     * Return current location path
     *
     * @return \XLite\View\Location
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getLocationPath()
    {
        if (!isset($this->locationPath)) {
            $this->defineLocationPath();
        }

        return $this->locationPath;
    }

    /**
     * Method to create the location line
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineLocationPath()
    {
        $this->locationPath = array();

        // Ability to add part to the line
        $this->addBaseLocation();

        // Ability to define last element in path via short function
        $location = $this->getLocation();

        if ($location) {
            $this->addLocationNode($location);
        }
    }

    /**
     * Common method to determine current location
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getLocation()
    {
        return null;
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function addBaseLocation()
    {
        // Common element for all location lines
        $this->locationPath[] = new \XLite\View\Location\Node\Home();
    }

    /**
     * Add node to the location line
     *
     * @param string $name     Node title
     * @param string $link     Node link OPTIONAL
     * @param array  $subnodes Node subnodes OPTIONAL
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function addLocationNode($name, $link = null, array $subnodes = null)
    {
        $this->locationPath[] = \XLite\View\Location\Node::create($name, $link, $subnodes);
    }

    // }}}

    /**
     * Return current category Id
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getCategoryId()
    {
        $categoryID = parent::getCategoryId();

        if (LC_USE_CLEAN_URLS && !isset($categoryID)) {
            $cleanURL = \XLite\Core\Request::getInstance()->cleanURLCat;

            if (!empty($cleanURL)) {
                $category   = \XLite\Core\Database::getRepo('\XLite\Model\Category')->findOneByCleanURL($cleanURL);
                $categoryID = $this->category_id = isset($category) ? $category->getCategoryId() : false;

                \XLite\Core\Request::getInstance()->category_id = $categoryID;
            }
        }

        return $categoryID ?: $this->getRootCategoryId();
    }

    /**
     * Return cart instance
     *
     * @return \XLite\Model\Order
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getCart()
    {
        return \XLite\Model\Cart::getInstance();
    }

    /**
     * Get the full URL of the page
     * Example: getShopURL('cart.php') = "http://domain/dir/cart.php
     *
     * @param string  $url    Relative URL OPTIONAL
     * @param boolean $secure Flag to use HTTPS OPTIONAL
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getShopURL($url = '', $secure = false)
    {
        return parent::getShopURL($url, \XLite\Core\Config::getInstance()->Security->full_customer_security ?: $secure);
    }

    /**
     * Check if cuurrent user is logged in
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isLogged()
    {
        return \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Handles the request
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function handleRequest()
    {
        if (!$this->checkStorefrontAccessability()) {
            $this->closeStorefront();
        }

        // Save initial cart fingerprint
        $this->initialCartFingerprint = $this->getCart()->getEventFingerprint();

        return parent::handleRequest();
    }


    /**
     * Stub for the CMS connectors
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function checkStorefrontAccessability()
    {
        return !\XLite\Core\Config::getInstance()->General->shop_closed;
    }

    /**
     * Perform some actions to prohibit access to storefornt
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function closeStorefront()
    {
        include LC_DIR_SKINS . '/storefront_closed.html';
        exit (0);
    }

    /**
     * Return template to use in a CMS
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getCMSTemplate()
    {
        return 'center_top.tpl';
    }

    /**
     * Select template to use
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getViewerTemplate()
    {
        return $this->getParam(self::PARAM_IS_EXPORTED) ? $this->getCMSTemplate() : parent::getViewerTemplate();
    }

    /**
     * Recalculates the shopping cart
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function updateCart()
    {
        $this->getCart()->updateOrder();
        \XLite\Core\Database::getRepo('XLite\Model\Cart')->update($this->getCart());

        $this->assembleEvent();

        $this->initialCartFingerprint = $this->getCart()->getEventFingerprint();
    }

    /**
     * Assemble updateCart event
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function assembleEvent()
    {
        $result = false;

        $old = $this->initialCartFingerprint;
        $new = $this->getCart()->getEventFingerprint();
        $items = array();

        // Assembly changed
        foreach ($new['items'] as $n => $cell) {

            $found = false;

            foreach ($old['items'] as $i => $oldCell) {

                if ($cell['key'] == $oldCell['key']) {

                    if ($cell['quantity'] != $oldCell['quantity']) {
                        $cell['quantity_change'] = $cell['quantity'] - $oldCell['quantity'];
                        $items[] = $cell;
                    }

                    unset($old['items'][$i]);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $cell['quantity_change'] = $cell['quantity'];
                $items[] = $cell;
            }
        }

        // Assemble removed
        foreach ($old['items'] as $cell) {
            $cell['quantity_change'] = $cell['quantity'] * -1;
            $items[] = $cell;
        }

        if ($items) {
            \XLite\Core\Event::updateCart(array('items' => $items));
            $result = true;
        }

        return $result;
    }

    /**
     * isCartProcessed
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function isCartProcessed()
    {
        return $this->getCart()->isProcessed() || $this->getCart()->isQueued();
    }

    /**
     * Get or create cart profile
     *
     * @return \XLite\Model\Profile
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getCartProfile()
    {
        $profile = $this->getCart()->getProfile();

        if (!$profile) {
            $profile = new \XLite\Model\Profile;
            $profile->setLogin('');
            $profile->setOrder($this->getCart());
            $profile->create();

            $this->getCart()->setProfile($profile);

            \XLite\Core\Auth::getInstance()->loginProfile($profile);

            \XLite\Core\Database::getEM()->persist($profile);
            \XLite\Core\Database::getEM()->flush();
        }

        return $profile;
    }
}
