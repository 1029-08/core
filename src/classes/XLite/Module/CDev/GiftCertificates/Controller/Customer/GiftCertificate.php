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
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Module\CDev\GiftCertificates\Controller\Customer;

/**
 * Gift certificate
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class GiftCertificate extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Month duration in seconds
     */
    const MONTH = 2592000;
    
    /**
     * Controller parameters
     * 
     * @var    string
     * @access public
     * @see    ____var_see____
     * @since  3.0.0
     */
    public $params = array('target', 'gcid');

    /**
     * Gift certificate (cache)
     * 
     * @var    \XLite\Module\CDev\GiftCertificates\Model\GiftCertificate
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $gc = null;

    /**
     * Return current page title
     * 
     * @return string
     * @access public
     * @since  3.0.0
     */
    public function getTitle()
    {
        return \XLite\Core\Request::getInstance()->gcid
            ? 'Update gift certificate'
            : 'Add gift certificate';
    }

    /**
     * Common method to determine current location 
     * 
     * @return array
     * @access protected 
     * @since  3.0.0
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Get gift certificate 
     * 
     * @return \XLite\Module\CDev\GiftCertificates\Model\GiftCertificate
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getGc()
    {
        if (is_null($this->gc)) {

            if (\XLite\Core\Request::getInstance()->gcid) {

                // Get from request
                $this->gc = new \XLite\Module\CDev\GiftCertificates\Model\GiftCertificate(
                    \XLite\Core\Request::getInstance()->gcid
                );

            } else {

                // Set default form values
                $this->gc = new \XLite\Module\CDev\GiftCertificates\Model\GiftCertificate();
                $this->assembleDefaultCertificate();
            }
        }

        return $this->gc;
    }

    /**
     * Assemble default gift certificate 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function assembleDefaultCertificate()
    {
        $this->gc->set('send_via', 'E');
        $this->gc->set('border', 'no_border');
        if ($this->auth->isLogged()) {
            $profile = $this->auth->get('profile');

            $name = $profile->get('billing_title');

            if ($profile->get('billing_firstname')) {
                $name .= ($name ? ' ' : '') . $profile->get('billing_firstname');
            }

            if ($profile->get('billing_lastname')) {
                $name .= ($name ? ' ' : '') . $profile->get('billing_lastname');
            }

            $this->gc->set('purchaser', $name);
        }
        $this->gc->set('recipient_country', $this->config->General->default_country);
    }

    /**
     * Add new gift certificate
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionAdd()
    {
        $this->saveGC();

        $found = false;

        foreach ($this->getCart()->get('items') as $item) {
            if ($item->get('gcid') == $this->getGC()->get('gcid')) {
                $item->set('GC', $this->getGC());
                $item->update();
                $found = true;
            }
        }

        if (!$found) {
            $oi = new \XLite\Model\OrderItem();
            $oi->set('GC', $this->getGC());
            $this->getCart()->addItem($oi);
        }

        if ($this->getCart()->isPersistent) {
            $this->getCart()->calcTotals();
            $this->getCart()->update();

            foreach ($this->getCart()->get('items') as $item) {
                if ($item->get('gcid') == $this->getGC()->get('gcid')) {
                    $this->getCart()->updateItem($item);
                }
            }
        }

        $this->set('returnUrl', $this->buildURL('cart'));
    }

    /**
     * Apply gift certificate
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionApply()
    {
        $gcid = trim(\XLite\Core\Request::getInstance()->gcid);
        $this->getCart()->set('GC', new \XLite\Module\CDev\GiftCertificates\Model\GiftCertificate($gcid));
        $this->getCart()->update();

        $this->set('returnUrl', $this->buildURL('checkout'));
    }

    /**
     * Delete e-card
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionDeleteEcard()
    {
        $this->saveGC();

        if (!is_null($this->getGC())) {
            $gc = $this->getGC();
            $gc->set('ecard_id', 0);
            $gc->update();
            $this->set('returnUrl', $this->buildURL('gift_certificate', '', array('gcid' => $gc->get('gcid'))));
        }
    }

    /**
     * Save gift certificate 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function saveGC()
    {
        if (isset($this->border)) {
            $this->border = str_replace(array('.', '/'), array('', ''), $this->border);
        }

        if (!is_null($this->getGC())) {
            $gc = $this->getGC();
            $gc->setProperties(\XLite\Core\Request::getInstance()->getData());
            $gc->set('status', 'D');
            $gc->set('debit', $gc->get('amount'));
            $gc->set('add_date', time());
            if (!$gc->get('expiration_date')) {
                $gc->set('expiration_date', time() + self::MONTH * $gc->getDefaultExpirationPeriod());
            }

            if ($gc->get('gcid')) {
                $gc->update();

            } else {
                $gc->set('gcid', $gc->generateGC());
                
                if ($this->auth->isLogged()) {
                    $gc->set('profile_id', $this->auth->getProfile()->get('profile_id'));
                }

                $gc->create();
            }
        }
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
        $this->target = 'gift_certificate';

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
    public function getPageTypeName()
    {
        return 'Add gift certificate';
    }

}

