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
 * @since     3.0.0
 */

namespace XLite\View\FormField;

/**
 * Abstract form field
 * 
 * @see   ____class_see____
 * @since 3.0.0
 */
abstract class AFormField extends \XLite\View\AView
{
    /**
     * Widget param names 
     */

    const PARAM_VALUE      = 'value';
    const PARAM_REQUIRED   = 'required';
    const PARAM_ATTRIBUTES = 'attributes';
    const PARAM_NAME       = 'fieldName';
    const PARAM_LABEL      = 'label';
    const PARAM_COMMENT    = 'comment';
    const PARAM_FIELD_ONLY = 'fieldOnly';

    const PARAM_IS_ALLOWED_FOR_CUSTOMER = 'isAllowedForCustomer';

    /**
     * Available field types
     */

    const FIELD_TYPE_LABEL     = 'label';
    const FIELD_TYPE_TEXT      = 'text';
    const FIELD_TYPE_PASSWORD  = 'password';
    const FIELD_TYPE_SELECT    = 'select';
    const FIELD_TYPE_CHECKBOX  = 'checkbox';
    const FIELD_TYPE_TEXTAREA  = 'textarea';
    const FIELD_TYPE_SEPARATOR = 'separator';


    /**
     * name 
     * 
     * @var   string
     * @see   ____var_see____
     * @since 3.0.0
     */
    protected $name = null;

    /**
     * validityFlag
     *
     * @var   boolean
     * @see   ____var_see____
     * @since 3.0.0
     */
    protected $validityFlag = null;

    /**
     * Determines if this field is visible for customers or not 
     * 
     * @var   boolean
     * @see   ____var_see____
     * @since 3.0.0
     */
    protected $isAllowedForCustomer = true;


    /**
     * Return field type
     *
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    abstract public function getFieldType();


    /**
     * Return field template
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    abstract protected function getFieldTemplate();


    /**
     * Return field name
     *
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getName()
    {
        return $this->getParam(self::PARAM_NAME);
    }

    /**
     * Return field value
     * 
     * @return mixed
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getValue()
    {
        return $this->getParam(self::PARAM_VALUE);
    }

    /**
     * setValue 
     * 
     * @param mixed $value Value to set
     *  
     * @return void
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function setValue($value)
    {
        $this->getWidgetParams(self::PARAM_VALUE)->setValue($value);
    }

    /**
     * getLabel 
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getLabel()
    {
        return $this->getParam(self::PARAM_LABEL);
    }

    /**
     * Return a value for the "id" attribute of the field input tag
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getFieldId()
    {
        return strtolower(strtr($this->getName(), array('['=>'-', ']'=>'', '_'=>'-')));
    }

    /**
     * Validate field value
     *
     * @return mixed
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function validate()
    {
        return array($this->getValidityFlag(), $this->getValidityFlag() ? null : $this->getRequiredFieldErrorMessage());
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/form_field.css';

        return $list;
    }

    /**
     * Save current form reference and sections list, and initialize the cache
     *
     * @param array $params Widget params OPTIONAL
     *
     * @return void
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function __construct(array $params = array())
    {
        if (isset($params[self::PARAM_NAME])) {
            $this->name = $params[self::PARAM_NAME];
        };

        parent::__construct($params);
    }


    /**
     * Return widget default template
     *
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getDefaultTemplate()
    {
        return 'form_field.tpl';
    }

    /**
     * Return widget template
     *
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getTemplate()
    {
        return $this->getParam(self::PARAM_FIELD_ONLY)
            ? $this->getDir() . LC_DS . $this->getFieldTemplate()
            : $this->getDefaultTemplate();
    }

    /**
     * Return name of the folder with templates
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getDir()
    {
        return 'form_field';
    }

    /**
     * checkSavedValue 
     * 
     * @return boolean 
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function checkSavedValue()
    {
        return !is_null($this->callFormMethod('getSavedData', array($this->getName())));
    }

    /**
     * getValidityFlag 
     * 
     * @return boolean 
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getValidityFlag()
    {
        if (!isset($this->validityFlag)) {
            $this->validityFlag = $this->checkFieldValidity();
        }

        return $this->validityFlag;
    }

    /**
     * getCommonAttributes 
     * 
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getCommonAttributes()
    {
        return array(
            'id'   => $this->getFieldId(),
            'name' => $this->getName(),
        );
    }

    /**
     * setCommonAttributes 
     * 
     * @param array $attrs Field attributes to prepare
     *  
     * @return void
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function setCommonAttributes(array $attrs)
    {
        foreach ($this->getCommonAttributes() as $name => $value) {
            if (!isset($attrs[$name])) {
                $attrs[$name] = $value;
            }
        }

        return $attrs;
    }

    /**
     * prepareAttributes 
     * 
     * @param array $attrs Field attributes to prepare
     *  
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function prepareAttributes(array $attrs)
    {
        if (!$this->getValidityFlag() && $this->checkSavedValue()) {
            $attrs['class'] = (empty($attrs['class']) ? '' : $attrs['class'] . ' ') . 'form_field_error';
        }

        return $this->setCommonAttributes($attrs);
    }

    /**
     * Check if field is required
     * 
     * @return boolean 
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function isRequired()
    {
        return $this->getParam(self::PARAM_REQUIRED);
    }

    /**
     * getAttributes 
     * 
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getAttributes()
    {
        return $this->prepareAttributes($this->getParam(self::PARAM_ATTRIBUTES));
    }

    /**
     * Return HTML representation for widget attributes
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getAttributesCode()
    {
        $result = '';

        foreach ($this->getAttributes() as $name => $value) {
            $result .= ' ' . $name . '="' . $value . '"';
        }

        return $result;
    }

    /**
     * Some JavaScript code to insert 
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getInlineJSCode()
    {
        return null;
    }

    /**
     * getDefaultName 
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getDefaultName()
    {
        return null;
    }

    /**
     * getDefaultValue 
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getDefaultValue()
    {
        return isset($this->name) ? $this->callFormMethod('getDefaultFieldValue', array($this->name)) : null;
    }

    /**
     * getDefaultLabel 
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getDefaultLabel()
    {
        return null;
    }

    /**
     * getDefaultAttributes 
     * 
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getDefaultAttributes()
    {
        return array();
    }

    /**
     * Define widget params 
     * 
     * @return void
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_NAME       => new \XLite\Model\WidgetParam\String('Name', $this->getDefaultName()),
            self::PARAM_VALUE      => new \XLite\Model\WidgetParam\String('Value', $this->getDefaultValue()),
            self::PARAM_LABEL      => new \XLite\Model\WidgetParam\String('Label', $this->getDefaultLabel()),
            self::PARAM_REQUIRED   => new \XLite\Model\WidgetParam\Bool('Required', false),
            self::PARAM_COMMENT    => new \XLite\Model\WidgetParam\String('Comment', null),
            self::PARAM_ATTRIBUTES => new \XLite\Model\WidgetParam\Collection('Attributes', $this->getDefaultAttributes()),

            self::PARAM_IS_ALLOWED_FOR_CUSTOMER => new \XLite\Model\WidgetParam\Bool(
                'Is allowed for customer',
                $this->isAllowedForCustomer
            ),
            self::PARAM_FIELD_ONLY    => new \XLite\Model\WidgetParam\Bool(
                'Skip wrapping with label and required flag, display just a field itself',
                false
            ),
        );
    }

    /**
     * Check field value validity
     * 
     * @return boolean 
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function checkFieldValue()
    {
        return '' != $this->getValue();
    }

    /**
     * checkFieldValidity 
     * 
     * @return boolean 
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function checkFieldValidity()
    {
        return !$this->isRequired() || $this->checkFieldValue();
    }

    /**
     * getRequiredFieldErrorMessage 
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getRequiredFieldErrorMessage()
    {
        return 'The "' . $this->getLabel() . '" field is empty';
    }

    /**
     * checkFieldAccessability 
     * 
     * @return boolean 
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function checkFieldAccessability()
    {
        return $this->getParam(self::PARAM_IS_ALLOWED_FOR_CUSTOMER) || \XLite::isAdminZone();
    }

    /**
     * callFormMethod 
     * 
     * @param string $method Class method to call
     * @param array  $args   Call arguments OPTIONAL
     *  
     * @return mixed
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function callFormMethod($method, array $args = array())
    {
        return call_user_func_array(array(\XLite\View\Model\AModel::getCurrentForm(), $method), $args);
    }

    /** 
     * Check if widget is visible
     *
     * @return boolean 
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function isVisible()
    {   
        return parent::isVisible() && $this->checkFieldAccessability();
    }
}
