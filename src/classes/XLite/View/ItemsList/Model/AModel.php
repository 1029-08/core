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
 * @copyright Copyright (c) 2011-2012 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.15
 */

namespace XLite\View\ItemsList\Model;

/**
 * Abstract admin model-based items list
 *
 * @see   ____class_see____
 * @since 1.0.15
 */
abstract class AModel extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Sortable types
     */
    const SORT_TYPE_NONE  = 0;
    const SORT_TYPE_MOVE  = 1;
    const SORT_TYPE_INPUT = 2;

    /**
     * Create inline position
     */
    const CREATE_INLINE_NONE   = 0;
    const CREATE_INLINE_TOP    = 1;
    const CREATE_INLINE_BOTTOM = 2;


    /**
     * Hightlight step
     *
     * @var   integer
     * @see   ____var_see____
     * @since 1.0.15
     */
    protected $hightlightStep = 2;

    /**
     * Error messages
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.15
     */
    protected $errorMessages = array();

    /**
     * Request data
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.15
     */
    protected $requestData;

    /**
     * Inline fields
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.15
     */
    protected $inlineFields;

    /**
     * Dump entity
     *
     * @var   \XLite\Model\AEntity
     * @see   ____var_see____
     * @since 1.0.15
     */
    protected $dumpEntity;

    // {{{ Fields

    /**
     * Get data prefix
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.15
     */
    public function getDataPrefix()
    {
        return 'data';
    }

    /**
     * Get data prefix for remove cells
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.15
     */
    public function getRemoveDataPrefix()
    {
        return 'delete';
    }

    /**
     * Get data prefix for select cells
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.15
     */
    public function getSelectorDataPrefix()
    {
        return 'select';
    }

    /**
     * Get data prefix for new data
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.15
     */
    public function getCreateDataPrefix()
    {
        return 'new';
    }

    /**
     * Get self
     *
     * @return \XLite\View\ItemsList\Model\AModel
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getSelf()
    {
        return $this;
    }

    // }}}

    // {{{ Model processing

    /**
     * Get field objects list (only inline-based form fields)
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    abstract protected function getFieldObjects();

    /**
     * Define repository name
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.15
     */
    abstract protected function defineRepositoryName();

    /**
     * Quick process
     *
     * @param array $parameters Parameters OPTIONAL
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.15
     */
    public function processQuick(array $parameters = array())
    {
        $this->setWidgetParams($parameters);
        $this->init();
        $this->process();
    }


    /**
     * Process
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.15
     */
    public function process()
    {
        $this->processRemove();
        $this->processCreate();
        $this->processUpdate();

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo($this->defineRepositoryName());
    }

    // {{{ Create

    /**
     * Get create field classes
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getCreateFieldClasses()
    {
        return array();
    }

    /**
     * Process create new entities
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function processCreate()
    {
        $count = 0;

        foreach ($this->getNewDataLine() as $key => $line) {

            if ($this->isNewLineSufficient($line, $key)) {
                $entity = $this->createEntity();
                $fields = $this->createInlineFields($line, $entity);

                $validated = 0 < count($fields);
                foreach ($fields as $inline) {
                    $validated = $this->validateCell($inline, $key) && $validated;
                }

                if ($validated) {
                    foreach ($fields as $inline) {
                        $this->saveCell($inline);
                    }
                    \XLite\Core\Database::getEM()->persist($entity);
                    $count++;
                }
            }
        }

        if (0 < $count) {
            $label = $this->getCreateMessage($count);
            if ($label) {
                \XLite\Core\TopMessage::getInstance()->addInfo($label);
            }
        }
    }

    /**
     * Get create message
     *
     * @param integer $count Count
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getCreateMessage($count)
    {
        return \XLite\Core\Translation::lbl('X entities has been created', array('count' => $count));
    }

    /**
     * Create entity
     *
     * @return \XLite\Model\AEntity
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function createEntity()
    {
        $entityClass = $this->defineRepositoryName();

        return new $entityClass;
    }

    /**
     * Get dump entity
     *
     * @return \XLite\Model\AEntity
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getDumpEntity()
    {
        if (!isset($this->dumpEntity)) {
            $this->dumpEntity = $this->createEntity();
        }

        return $this->dumpEntity;
    }

    /**
     * Get new data line
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getNewDataLine()
    {
        $data = $this->getRequestData();
        $prefix = $this->getCreateDataPrefix();

        return (isset($data[$prefix]) && is_array($data[$prefix])) ? $data[$prefix] : array();
    }

    /**
     * Check - new line is sufficient or not
     *
     * @param array   $line Data line
     * @param integer $key  Field key gathered from request data, eg: new[this-key][field-name] (see ..\AInline::processCreate())
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function isNewLineSufficient(array $line, $key)
    {
        return 0 !== $key && 0 < count($line);
    }

    /**
     * Create inline fields list
     *
     * @param array                $line   Line data
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function createInlineFields(array $line, \XLite\Model\AEntity $entity)
    {
        $list = array();

        foreach ($this->getCreateFieldClasses() as $object) {
            $this->prepareInlineField($object, $entity);
            $list[] = $object;
        }

        return $list;
    }

    // }}}

    // {{{ Remove

    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getRemoveMessage($count)
    {
        return \XLite\Core\Translation::lbl('X entities has been removed', array('count' => $count));
    }

    /**
     * Process remove
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function processRemove()
    {
        $count = 0;

        $repo = $this->getRepository();
        foreach ($this->getEntityIdListForRemove() as $id) {
            $entity = $repo->find($id);
            if ($entity && $this->removeEntity($entity)) {
                $count++;
            }
        }

        if (0 < $count) {
            $label = $this->getRemoveMessage($count);
            if ($label) {
                \XLite\Core\TopMessage::getInstance()->addInfo($label);
            }
        }

        return $count;
    }

    /**
     * Get entity's ID list for remove
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.17
     */
    protected function getEntityIdListForRemove()
    {
        $data = $this->getRequestData();
        $prefix = $this->getRemoveDataPrefix();

        $list = array();

        if (isset($data[$prefix]) && is_array($data[$prefix]) && $data[$prefix]) {
            foreach ($data[$prefix] as $id => $allow) {
                if ($allow) {
                    $list[] = $id;
                }
            }
        }

        return $list;
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.17
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        \XLite\Core\Database::getEM()->remove($entity);

        return true;
    }

    // }}}

    // {{{ Update

    /**
     * Process update
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function processUpdate()
    {
        $result = true;

        if ($this->isActiveModelProcessing()) {
            $result = $this->validateUpdate();

            if ($result) {
                $result = $this->update();

            } else {
                $this->processUpdateErrors();
            }
        }

        return $result;
    }

    /**
     * Check - moel processing is active or not
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function isActiveModelProcessing()
    {
        return $this->hasResults() && $this->getFieldObjects();
    }

    /**
     * Validate data
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function validateUpdate()
    {
        $validated = true;

        foreach ($this->prepareInlineFields() as $field) {
            $validated = $this->validateCell($field) && $validated;
        }

        return $validated;
    }

    /**
     * Save data
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function update()
    {
        $count = 0;

        foreach ($this->prepareInlineFields() as $field) {
            $count++;
            $this->saveCell($field);
        }

        return $count;
    }

    /**
     * Process errors
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function processUpdateErrors()
    {
        \XLite\Core\TopMessage::getInstance()->addBatch($this->getErrorMessages(), \XLite\Core\TopMessage::ERROR);

        // Run controller's method
        $this->setActionError();
    }

    /**
     * Validate inline field
     *
     * @param \XLite\View\FormField\Inline\AInline $inline Inline field
     * @param integer                              $key    Field key gathered from request data, eg: new[this-key][field-name] (see ..\AInline::processCreate()) OPTIONAL
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function validateCell(\XLite\View\FormField\Inline\AInline $inline, $key = null)
    {
        $inline->setValueFromRequest($this->getRequestData(), $key);
        list($flag, $message) = $inline->validate();
        if (!$flag) {
            $this->addErrorMessage($inline, $message);
        }

        return $flag;
    }

    /**
     * Save cell
     *
     * @param \XLite\View\FormField\Inline\AInline $inline Inline field
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function saveCell(\XLite\View\FormField\Inline\AInline $inline)
    {
        $inline->saveValue();
    }

    /**
     * Get inline fields
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function prepareInlineFields()
    {
        if (!isset($this->inlineFields)) {
            $this->inlineFields = $this->defineInlineFields();
        }

        return $this->inlineFields;
    }

    /**
     * Define inline fields
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function defineInlineFields()
    {
        $list = array();

        foreach ($this->getPageData() as $entity) {
            foreach ($this->getFieldObjects() as $object) {
                $this->prepareInlineField($object, $entity);
                $list[] = $object;
            }
        }

        return $list;
    }

    /**
     * Get inline field
     *
     * @param \XLite\View\FormField\Inline\AInline $field  Field
     * @param \XLite\Model\AEntity                 $entity Entity
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function prepareInlineField(\XLite\View\FormField\Inline\AInline $field, \XLite\Model\AEntity $entity)
    {
        $field->setWidgetParams(array('entity' => $entity, 'itemsList' => $this));
    }

    // }}}

    // {{{ Misc.

    /**
     * Get request data
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getRequestData()
    {
        if (!isset($this->requestData)) {
            $this->requestData = $this->defineRequestData();
        }

        return $this->requestData;
    }

    /**
     * Define request data
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function defineRequestData()
    {
        return \XLite\Core\Request::getInstance()->getData();
    }

    /**
     * Add error message
     *
     * @param \XLite\View\Inline\AInline $inline  Inline field
     * @param string                     $message Message
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function addErrorMessage(\XLite\View\Inline\AInline $inline, $message)
    {
        $this->errorMessages[] = $inline->getLabel() . ': ' . $message;
    }

    /**
     * Get error messages
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getErrorMessages()
    {
        return $this->errorMessages;
    }

    // }}}

    // {{{ Content helpers

    /**
     * Get a list of CSS files
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/model/style.css';

        return $list;
    }

    /**
     * Check - body tempalte is visible or not
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.16
     */
    protected function isPageBodyVisible()
    {
        return $this->hasResults() || static::CREATE_INLINE_NONE != $this->isInlineCreation();
    }

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.16
     */
    protected function isPagerVisible()
    {
        return $this->isPageBodyVisible() && $this->getPager();
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getPageBodyDir()
    {
        return 'model';
    }

    /**
     * Get line class
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getLineClass($index, \XLite\Model\AEntity $entity)
    {
        return implode(' ', $this->defineLineClass($index, $entity));
    }

    /**
     * Define line class as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity)
    {
        $classes = array('line');

        if (0 === $index) {
            $classes[] = 'first';
        }

        if ($this->getItemsCount() == $index + 1) {
            $classes[] = 'last';
        }

        if (0 === ($index + 1) % $this->hightlightStep) {
            $classes[] = 'even';
        }

        $classes[] = 'entity-' . $entity->getUniqueIdentifier();

        return $classes;
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Infinity';
    }

    /**
     * Return internal list name
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getListName()
    {
        return parent::getListName() . '.' . implode('.', $this->getListNameSuffixes());
    }

    /**
     * Get list name suffixes
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getListNameSuffixes()
    {
        $parts = explode('\\', get_called_class());

        $names = array();
        if ('Module' === $parts[1]) {
            $names[] = strtolower($parts[2]);
            $names[] = strtolower($parts[3]);
        }

        $names[] = strtolower($parts[count($parts) - 1]);

        return $names;
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        return \XLite\Core\Converter::buildURL(
            $column[static::COLUMN_LINK], 
            '', 
            array($entity->getUniqueIdentifierName() => $entity->getUniqueIdentifier())
        );
    }

    /**
     * Get container class
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getContainerClass()
    {
        return 'widget items-list'
            . ' widgetclass-' . $this->getWidgetClass()
            . ' widgettarget-' . $this->getWidgetTarget()
            . ' sessioncell-' . $this->getSessionCell();
    }

    /**
     * Get container attributes
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.23
     */
    protected function getContainerAttributes()
    {
        return array(
            'class' => $this->getContainerClass(),
        );
    }

    /**
     * Get container attributes as string
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.23
     */
    protected function getContainerAttributesAsString()
    {
        $list = array();
        foreach ($this->getContainerAttributes() as $name => $value) {
            $list[] = $name . '="' . func_htmlspecialchars($value) . '"';
        }

        return implode(' ', $list);
    }


    // }}}

    // {{{ Line behaviors

    /**
     * Mark list as sortable
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_NONE;
    }

    /**
     * Mark list as switchyabvle (enable / disable)
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function isSwitchable()
    {
        return false;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function isSelectable()
    {
        return false;
    }

    /**
     * Creation button position
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Get create entity URL
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getCreateURL()
    {
        return null;
    }

    /**
     * Get create button label
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getCreateButtonLabel()
    {
        return 'Create';
    }

    /**
     * Get entity position
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getEntityPosition(\XLite\Model\AEntity $entity)
    {
        return $entity->getOrder();
    }

    // }}}

    // {{{ Sticky panel

    /**
     * Check - sticky panel is visible or not
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.16
     */
    protected function isPanelVisible()
    {
        return $this->getPanelClass() && $this->isPageBodyVisible();
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsListForm';
    }

    // }}}

    // {{{ Data

    /**
     * Return coupons list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo($this->defineRepositoryName())->search($cnd, $countOnly);
    }

    // }}}
}

