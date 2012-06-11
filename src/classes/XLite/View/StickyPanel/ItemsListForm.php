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
 * @since     1.0.15
 */

namespace XLite\View\StickyPanel;

/**
 * Panel form items list-based form 
 * 
 * @see   ____class_see____
 * @since 1.0.15
 */
class ItemsListForm extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Buttons list (cache)
     * 
     * @var   array
     * @see   ____var_see____
     * @since 1.0.24
     */
    protected $buttonsList;

    /**
     * Additional buttons (cache)
     * 
     * @var   array
     * @see   ____var_see____
     * @since 1.0.24
     */
    protected $additionalButtons;

    /**
     * Get buttons widgets
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getButtons()
    {
        if (!isset($this->buttonsList)) {
            $this->buttonsList = $this->defineButtons();
        }

        return $this->buttonsList;
    }

    /**
     * Define buttons widgets
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function defineButtons()
    {
        $list = array(
            'save' => $this->getWidget(
                array(
                    'style'    => 'action submit',
                    'label'    => \XLite\Core\Translation::lbl('Save changes'),
                    'disabled' => true,
                ),
                'XLite\View\Button\Submit'
            ),
            'cancel' => $this->getWidget(
                array(
                    'template' => 'items_list/model/cancel.tpl',
                )
            ),
        );

        if ($this->getAdditionalButtons()) {
            $list['additional'] = $this->getWidget(
                array(
                    'template' => 'items_list/model/additional_buttons.tpl',
                )
            );
        }

        return $list;
    }

    /**
     * Get additional buttons 
     * 
     * @return array
     * @see    ____func_see____
     * @since  1.0.24
     */
    protected function getAdditionalButtons()
    {
        if (!isset($this->additionalButtons)) {
            $this->additionalButtons = $this->defineAdditionalButtons();
        }

        return $this->additionalButtons;
    }

    /**
     * Define additional buttons 
     * 
     * @return array
     * @see    ____func_see____
     * @since  1.0.24
     */
    protected function defineAdditionalButtons()
    {
        return array();
    }

    /**
     * Get class
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.10
     */
    protected function getClass()
    {
        $class = parent::getClass();

        if ($this->getAdditionalButtons()) {
            $class = trim($class . ' has-add-buttons');
        }

        return $class;
    }
}

