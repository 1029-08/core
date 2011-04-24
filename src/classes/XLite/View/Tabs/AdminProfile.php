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
 * @since     1.0.0
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to user profile section
 * 
 * @see   ____class_see____
 * @since 1.0.0
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class AdminProfile extends \XLite\View\Tabs\ATabs
{
    /**
     * User profile object
     *
     * @var   \XLite\Model\Profile
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $profile;

    /**
     * Description of tabs related to user profile section and their targets
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $tabs = array(
        'profile' => array(
            'title'    => 'Account details',
            'template' => 'profile/account.tpl',
        ),
        'address_book' => array(
            'title'    => 'Address book',
            'template' => 'profile/address_book.tpl',
        ),
    );


    /**
     * init 
     * 
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function init()
    {
        parent::init();

        if (\XLite\Controller\Admin\Profile::getInstance()->isRegisterMode()) {

            foreach ($this->tabs as $key => $tab) {

                if ('profile' != $key) {

                    unset($this->tabs[$key]);
                }
            }            
        }
    }

    /**
     * getProfile 
     * 
     * @return \XLite\Model\Profile
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getProfile()
    {
        if (!isset($this->profile)) {

            $profileId = \XLite\Core\Request::getInstance()->profile_id;

            if (isset($profileId)) {

                $this->profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId);
            
            } else {

                $this->profile = \XLite\Core\Auth::getInstance()->getProfile();
            }
        }

        return $this->profile;
    }


    /**
     * Returns an URL to a tab
     * 
     * @param string $target Tab target
     *  
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function buildTabURL($target)
    {
        $profileId = \XLite\Core\Request::getInstance()->profile_id;

        return $this->buildURL($target, '', isset($profileId) ? array('profile_id' => $profileId) : array());
    }
}
