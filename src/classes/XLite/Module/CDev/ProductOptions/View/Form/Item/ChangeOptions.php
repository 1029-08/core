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
 * @subpackage View
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      1.0.0
 */

namespace XLite\Module\CDev\ProductOptions\View\Form\Item;

/**
 * Change options form
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   1.0.0
 */
class ChangeOptions extends \XLite\View\Form\AForm
{
    /**
     * Widge parameters names 
     */

    const PARAM_SOURCE     = 'source';
    const PARAM_STORAGE_ID = 'storage_id';
    const PARAM_ITEM_ID    = 'item_id';


    /**
     * Current form name
     *
     * @return string
     * @access protected
     * @since  1.0.0
     */
    protected function getFormName()
    {
        return 'change_options';
    }

    /**
     * getDefaultTarget
     *
     * @return string
     * @access protected
     * @since  1.0.0
     */
    protected function getDefaultTarget()
    {
        return 'change_options';
    }

    /**
     * getDefaultAction
     *
     * @return string
     * @access protected
     * @since  1.0.0
     */
    protected function getDefaultAction()
    {
        return 'change';
    }

    /** 
     * Define widget parameters
     *
     * @return void
     * @access protected
     * @since  1.0.0
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_SOURCE     => new \XLite\Model\WidgetParam\String('Source', \XLite\Core\Request::getInstance()->source),
            self::PARAM_STORAGE_ID => new \XLite\Model\WidgetParam\Int('Storage id', \XLite\Core\Request::getInstance()->storage_id),
            self::PARAM_ITEM_ID    => new \XLite\Model\WidgetParam\Int('Item id', \XLite\Core\Request::getInstance()->item_id),
        );
    }

    /**
     * Initialization
     * 
     * @return void
     * @access protected
     * @since  1.0.0
     */
    protected function initView()
    {
        parent::initView();

        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue($this->getFormDefaultParams());
    }

    /**
     * Get form default parameters
     * 
     * @return array
     * @access protected
     * @since  1.0.0
     */
    protected function getFormDefaultParams()
    {
        return array(
            'source'     => $this->getParam(self::PARAM_SOURCE),
            'storage_id' => $this->getParam(self::PARAM_STORAGE_ID),
            'item_id'    => $this->getParam(self::PARAM_ITEM_ID),
        );
    }
}

