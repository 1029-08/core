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
 * @since      3.0.0
 */

namespace XLite\View\Button;

/**
 * Abstract button
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
abstract class AButton extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */

    const PARAM_LABEL    = 'label';
    const PARAM_STYLE    = 'style';
    const PARAM_DISABLED = 'disabled';
    const PARAM_ID       = 'id';


    /**
     * allowedJSEvents 
     * 
     * @var    string
     * @access protected
     * @since  3.0.0
     */
    protected $allowedJSEvents = array(
        'onclick' => 'One click',
    );

    /**
     * getDefaultLabel
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getDefaultLabel()
    {
        return '--- Button title is not defined ---';
    }

    /**
     * Return button text 
     * 
     * @return string
     * @access protected
     * @since  3.0.0
     */
    protected function getButtonLabel()
    {
        return $this->t($this->getParam(self::PARAM_LABEL));
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
            self::PARAM_LABEL    => new \XLite\Model\WidgetParam\String('Label', $this->getDefaultLabel(), true),
            self::PARAM_STYLE    => new \XLite\Model\WidgetParam\String('Button style', ''),
            self::PARAM_DISABLED => new \XLite\Model\WidgetParam\Bool('Disabled', 0),
            self::PARAM_ID       => new \XLite\Model\WidgetParam\String('Button ID', ''),
        );
    }

    /**
     * getClass 
     * 
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getClass()
    {
        return $this->getParam(self::PARAM_STYLE);
    }

    /**
     * getId 
     * 
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getId()
    {
        return $this->getParam(self::PARAM_ID);
    }


    /**
     * Get a list of CSS files required to display the widget properly 
     * 
     * @return array
     * @access public
     * @since  3.0.0
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'button/css/button.css';

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     * 
     * @return void
     * @access public
     * @since  3.0.0
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/button.js';

        return $list;
    }
}

