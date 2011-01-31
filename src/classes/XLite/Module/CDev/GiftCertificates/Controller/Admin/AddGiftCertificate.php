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

namespace XLite\Module\CDev\GiftCertificates\Controller\Admin;

/**
 * Add gift certificate
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class AddGiftCertificate extends \XLite\Controller\Admin\AAdmin
{
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
     * Gift Certificate object 
     * 
     * @var    \XLite\Module\CDev\GiftCertificates\Model\GiftCertificate
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $gc = null;

    /**
     * Get GC object
     * 
     * @return \XLite\Module\CDev\GiftCertificates\Model\GiftCertificate
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getGC()
    {
        if (is_null($this->gc)) {

            if (\XLite\Core\Request::getInstance()->gcid) {
                $this->gc = new \XLite\Module\CDev\GiftCertificates\Model\GiftCertificate(
                    \XLite\Core\Request::getInstance()->gcid
                );

            } else {
                $this->setDefaultGiftCertificate();
            }
        }

        return $this->gc;
    }

    /**
     * Set default gift certificate 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function setDefaultGiftCertificate()
    {
        $this->gc = new \XLite\Module\CDev\GiftCertificates\Model\GiftCertificate();
        $this->gc->set('send_via', 'E');
        $this->gc->set('border', 'no_border');

        $auth = \XLite\Core\Auth::getInstance();

        if ($auth->isLogged()) {
            $profile = $auth->getProfile();
            $this->gc->set(
                'purchaser',
                $profile->get('billing_title')
                . ' '
                . $profile->get('billing_firstname')
                . ' '
                . $profile->get('billing_lastname')
            );
        }

        $this->gc->set('recipient_country', $this->config->General->default_country);
    }

    /**
     * Fill GC form 
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function fillForm()
    {
        $this->set('properties', $this->getGC()->get('properties'));

        if (!$this->get('expiration_date')) {
            $month = 30 * 24 * 3600;
            $this->set('expiration_date', time() + $month * $this->getGC()->get('defaultExpirationPeriod'));
        }

        parent::fillForm();
    }

    /**
     * Add
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionAdd()
    {
        $this->sendGC();
        $this->set('returnUrl', $this->buildUrl('gift_certificates'));
    }

    /**
     * Select e-card
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionSelectEcard()
    {
        $this->saveGC();
        $this->set(
            'returnUrl',
            $this->buildUrl('gift_certificate_select_ecard', '', array('gcid' => $this->getGC()->get('gcid')))
        );
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
        $gc = $this->getGC();
        if (!is_null($gc)) {
            $gc->set('ecard_id', 0);
            $gc->update();

            $this->set('returnUrl', $this->buildUrl('gift_certificate', '', array('gcid' => $gc->get('gcid'))));
        }
    }

    /**
     * Preview e-card 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionPreviewEcard()
    {
        $this->saveGC();
        $this->set(
            'returnUrl',
            $this->buildUrl('preview_ecard', '', array('gcid' => $this->getGC()->get('gcid')))
        );
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
        $gc = $this->getGC();

        if (!is_null($gc)) {
            $gc->setProperties(\XLite\Core\Request::getInstance()->getData());
            $gc->set('add_date', time());
            $expirationDate = mktime(
                0, 0, 0,
                \XLite\Core\Request::getInstance()->expiration_dateMonth,
                \XLite\Core\Request::getInstance()->expiration_dateDay,
                \XLite\Core\Request::getInstance()->expiration_dateYear
            );
            $gc->set('expiration_date', $expirationDate);

            if (empty(\XLite\Core\Request::getInstance()->debit)) {
                $gc->set('debit', $gc->get('amount'));
            }

            if (!$gc->get('gcid')) {
                $gc->set('gcid', $gc->generateGC());
                $gc->create();
            }

            $gc->update();
        }
    }

    /**
     * Send gift certificate
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function sendGC()
    {
        $this->saveGC();

        $gc = $this->getGC();
        if (!is_null($gc)) {
            // Activate and send GC (for send_via = E)
            $gc->set('status', 'A');
            $gc->update();
        }
    }
}
