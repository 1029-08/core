<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * @file
 * Stub functions. They are needed since Drupal does not support full-pledged callbacks
 *
 * @category  Litecommerce connector
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

/**
 * Return LC controller title
 *
 * @return string
 * @see    ____func_see____
 * @since  1.0.0
 */
function lcConnectorGetControllerTitle()
{
    return \XLite\Module\CDev\DrupalConnector\Drupal\Controller::getInstance()->getTitle();
}

/**
 * Process LC controller title
 *
 * @param string $title Title
 *
 * @return string
 * @see    ____func_see____
 * @since  1.0.0
 */
function lcConnectorProcessControllerTitle($title)
{
    return \XLite\Core\Translation::lbl($title);
}

/**
 * Return LC controller page content
 *
 * @return string
 * @see    ____func_see____
 * @since  1.0.0
 */
function lcConnectorGetControllerContent()
{
    return \XLite\Module\CDev\DrupalConnector\Drupal\Controller::getInstance()->getContent();
}

/**
 * Validate widget details form
 *
 * @param array &$form      Form description
 * @param array &$formState Form state
 *
 * @return void
 * @see    ____func_see____
 * @since  1.0.0
 */
function lcConnectorValidateWidgetModifyForm(array &$form, array &$formState)
{
    return \XLite\Module\CDev\DrupalConnector\Drupal\Admin::getInstance()->validateWidgetModifyForm(
        $form,
        $formState
    );
}

/**
 * Submit widget details form
 *
 * @param array &$form       Form description
 * @param array &$form_state Form state
 *
 * @return void
 * @see    ____func_see____
 * @since  1.0.0
 */
function lcConnectorSubmitWidgetModifyForm(array &$form, array &$formState)
{
    return \XLite\Module\CDev\DrupalConnector\Drupal\Admin::getInstance()->submitWidgetModifyForm(
        $form,
        $formState
    );
}

/**
 * Submit widget delete confirmation form
 *
 * @param array &$form       Form description
 * @param array &$form_state Form state
 *
 * @return void
 * @see    ____func_see____
 * @since  1.0.0
 */
function lcConnectorSubmitWidgetDeleteForm(array &$form, array &$formState)
{
    return \XLite\Module\CDev\DrupalConnector\Drupal\Admin::getInstance()->submitWidgetDeleteForm(
        $form,
        $formState
    );
}

/**
 * Submit user profile/register form
 *
 * @param array &$form       Form description
 * @param array &$form_state Form state
 *
 * @return void
 * @see    ____func_see____
 * @since  1.0.0
 */
function lcConnectorUserProfileFormSubmit(array &$form, array &$formState)
{
    return \XLite\Module\CDev\DrupalConnector\Drupal\Admin::getInstance()->submitUserProfileForm(
        $form,
        $formState
    );
}

/**
 * Submit admin permissions form
 *
 * @param array &$form       Form description
 * @param array &$form_state Form state
 *
 * @return void
 * @see    ____func_see____
 * @since  1.0.0
 */
function lcConnectorUserPermissionsSubmit(array &$form, array &$formState)
{
    return \XLite\Module\CDev\DrupalConnector\Drupal\Admin::getInstance()->submitUserPermissionsForm(
        $form,
        $formState
    );
}

/**
 * Do user accounts synchronization in batch mode
 *
 * @param array &$context Batch process context data
 *
 * @return void
 * @see    ____func_see____
 * @since  1.0.0
 */
function lcConnectorUserSync(array &$context)
{
    return \XLite\Module\CDev\DrupalConnector\Drupal\UserSync::getInstance()->doUserSynchronization(
        $context
    );
}

/**
 * Finalize user accounts synchronization batch process
 *
 * @param boolean $success    Batch process status
 * @param array   $results    Batch process results array
 * @param array   $operations Batch process operations array
 *
 * @return void
 * @see    ____func_see____
 * @since  1.0.0
 */
function lcConnectorUserSyncFinishedCallback($success, array $results, array $operations)
{
    return \XLite\Module\CDev\DrupalConnector\Drupal\UserSync::getInstance()->doUserSyncFinished(
        $success,
        $results,
        $operations
    );
}
