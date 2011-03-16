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

namespace XLite\Module\CDev\DrupalConnector\Model;

/**
 * \XLite\Module\CDev\DrupalConnector\Model\Profile 
 * 
 * @package    XLite
 * @subpackage Model
 * @see        ____class_see____
 * @since      3.0.0
 */
class Profile extends \XLite\Model\Profile implements \XLite\Base\IDecorator
{
    /**
     * User roles defined on Drupal side
     *
     * @var    \XLite\Module\CDev\DrupalConnector\Model\DrupalRole
     * @see    ____var_see____
     * @since  3.0.0
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\DrupalConnector\Model\DrupalRole", mappedBy="profile", cascade={"all"})
     */
    protected $drupalRoles;

    /**
     * prepareCreate 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function prepareCreate()
    {
        parent::prepareCreate();

        if (\XLite\Module\CDev\DrupalConnector\Handler::getInstance()->checkCurrentCMS()) {
            $this->setCmsName(\XLite\Module\CDev\DrupalConnector\Handler::getInstance()->getCMSName());
        }
    }

    /**
     * Get CMS profile 
     * 
     * @return object|void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getCMSProfile()
    {
        return \XLite\Module\CDev\DrupalConnector\Handler::getInstance()->checkCurrentCMS() && $this->getCMSProfileId()
            ? user_load($this->getCMSProfileId())
            : null;
    }

    /**
     * Update user's Drupal roles
     * 
     * @param array $newDrupalRoles Array of Drupal role IDs
     *  
     * @return void
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function updateDrupalRoles($newDrupalRoles)
    {
        $processedRoles = array();

        $drupalRoles = $this->getDrupalRoles();

        if ($drupalRoles) {

            // Remove roles that is not in new roles array
            foreach ($this->getDrupalRoles() as $drupalRole) {

                if (!in_array($drupalRole->getDrupalRoleId(), $newDrupalRoles)) {
                    \XLite\Core\Database::getEM()->remove($drupalRole);
            
                } else {
                    $processedRoles[] = $drupalRole->getDrupalRoleId();
                }
            }
        }

        // Get roles to add 
        $rolesToAdd = array_diff($newDrupalRoles, $processedRoles);

        // Create new roles
        foreach ($rolesToAdd as $roleId) {
            $newDrupalRole = new \XLite\Module\CDev\DrupalConnector\Model\DrupalRole();
            $newDrupalRole->setProfile($this);
            $newDrupalRole->setDrupalRoleId($roleId);

            $this->addDrupalRoles($newDrupalRole);
        }
    }
}
