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
 * @since     1.0.0
 */

namespace XLite\Model\Repo;

/**
 * DB-based configuration registry
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class Config extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Repository type
     *
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $type = self::TYPE_SERVICE;

    /**
     * Default 'order by' field name
     *
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $defaultOrderBy = 'orderby';

    /**
     * List of options which are not allowed
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $disabledOptions = array();

    /**
     * Alternative record identifiers
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $alternativeIdentifier = array(
        array('category', 'name'),
    );

    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string $alias Table alias OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function createQueryBuilder($alias = null)
    {
        return $this->prepareOptionsAvailabilityCondition(parent::createQueryBuilder($alias));
    }

    /**
     * Get the list of options of the specified category
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getByCategory($category, $force = false, $doNotProcess = false)
    {
        $data = null;

        if (!$force) {
            $data = $this->getFromCache('category', array('category' => $category));
        }

        if (!isset($data)) {
            $data = $this->defineByCategoryQuery($category)->getResult();
            if (!$doNotProcess) {
                $data = $this->processOptions($data);
                $this->saveToCache($data, 'category', array('category' => $category));
            }
        }

        return $data;
    }

    /**
     * Find all visible settings by category name
     *
     * @param string $category Category name
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function findByCategoryAndVisible($category)
    {
        return $this->defineByCategoryAndVisibleQuery($category)->getResult();
    }

    /**
     * Get the list of all options
     *
     * @param boolean $force Do not use cache OPTIONAL
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getAllOptions($force = false)
    {
        $data = null;

        if (!$force) {
            $data = $this->getFromCache('all');
        }

        if (!isset($data)) {
            $data = $this->defineAllOptionsQuery()->getResult();
            $data = $this->detachList($data);
            $data = $this->processOptions($data);
            $this->saveToCache($data, 'all');
        }

        return $data;
    }

    /**
     * Preprocess options and transform its to the hierarchy of \XLite\Core\CommonCell objects
     *
     * @param array $data Array of options data gathered from the database
     *
     * @return \XLite\Core\CommonCell
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function processOptions($data)
    {

        $config = new \XLite\Core\CommonCell();

        foreach ($data as $option) {

            $category = $option->getCategory();
            $name     = $option->getName();
            $type     = $option->getType();
            $value    = $option->getValue();

            $isModuleConfig = false !== strpos($category, '\\');

            if ($isModuleConfig) {

                // Process module config

                list($author, $module) = explode('\\', $category);

                if (!isset($config->$author)) {
                    $config->$author = new \XLite\Core\CommonCell();
                }

                if (!isset($config->$author->$module)) {
                    $config->$author->$module = new \XLite\Core\CommonCell();
                }

            } elseif (!isset($config->$category)) {
                $config->$category = new \XLite\Core\CommonCell();
            }

            if ('checkbox' === $type) {
                $value = ('Y' == $value || '1' === $value);

            } elseif ('serialized' === $type) {
                $value = unserialize($value);
            }

            if ($this->checkNameAndValue($category, $name, $value)) {

                if ($isModuleConfig) {
                    $config->$author->$module->$name = $value;
                } else {
                    $config->$category->$name = $value;
                }
            }
        }

        // Add human readable store country and state names for Company options
        if (isset($config->Company)) {
            $config->Company->locationCountry = \XLite\Core\Database::getRepo('XLite\Model\Country')
                ->find($config->Company->location_country);

            $config->Company->locationState = \XLite\Core\Database::getRepo('XLite\Model\State')
                ->findById($config->Company->location_state, $config->Company->location_custom_state);
        }

        // Add human readable default country name for General options
        if (isset($config->General)) {
            $config->General->defaultCountry = \XLite\Core\Database::getRepo('XLite\Model\Country')
                ->find($config->General->default_country);

            $config->General->defaultState = \XLite\Core\Database::getRepo('XLite\Model\State')
                ->findById($config->General->default_state, $config->General->default_custom_state);

            // Get default language object
            if (isset($config->General->default_language)) {
                $config->General->defaultLanguage = \XLite\Core\Database::getRepo('XLite\Model\Language')
                    ->findOneByCode($config->General->default_language);
            }

            if (!isset($config->General->defaultLanguage)) {
                $config->General->defaultLanguage = \XLite\Core\Database::getRepo('XLite\Model\Language')
                    ->getDefaultLanguage();
            }

            // Type cast
            $config->General->minimal_order_amount = doubleval($config->General->minimal_order_amount);
            $config->General->maximal_order_amount = doubleval($config->General->maximal_order_amount);
        }

        return $config;
    }

    /**
     * Create new option / Update option value
     *
     * @param array $data Option data in the following format
     *
     * @return void
     * @throws \Exception
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function createOption($data)
    {
        // Array of allowed fields and flag required/optional
        $fields = array(
            'category' => 1,
            'name'     => 1,
            'value'    => 1,
            'type'     => 0,
            'orderby'  => 0,
        );

        $errorFields = array();

        foreach ($fields as $field => $required) {
            if (isset($data[$field])) {
                $fields[$field] = $data[$field];

            } elseif ($required) {
                $errorFields[] = $field;
            }
        }

        if (!empty($errorFields)) {
            throw new \Exception(
                'createOptions() failed: The following required fields are missed: ' .
                implode(', ', $errorFields)
            );
        }

        if (isset($fields['type']) && !$this->isValidOptionType($fields['type'])) {
            throw new \Exception('createOptions() failed: Wrong option type: ' . $type);
        }

        $option = $this->findOneBy(array('name' => $fields['name'], 'category' => $fields['category']));

        // Existing option: unset key fields
        if ($option) {
            $option->setValue($fields['value']);

        } else {
            // Create a new option
            $option = new \XLite\Model\Config();
            $option->map($fields);
        }

        \XLite\Core\Database::getEM()->persist($option);
        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Define cache cells
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();

        $list['all'] = array();
        $list['category'] = array(
            self::ATTRS_CACHE_CELL => array('category')
        );

        return $list;
    }

    /**
     * Remove option from the "black list"
     *
     * @param string $category Option category
     * @param string $name     Option name
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function enableOption($category, $name)
    {
        unset($this->disabledOptions[$category][array_search($name, $this->disabledOptions[$category])]);
    }

    /**
     * Add option to the "black list"
     *
     * @param string $category Option category
     * @param string $name     Option name
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function disableOption($category, $name)
    {
        if (!isset($this->disabledOptions[$category])) {
            $this->disabledOptions[$category] = array();
        }

        $this->disabledOptions[$category][] = $name;
    }

    /**
     * Return query (and its params) which is used to filter options
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getOptionsAvailabilityCondition()
    {
        $conditions = array();
        $params = array();

        foreach ($this->disabledOptions as $category => $options) {

            $condition = 'c.category = :category' . $category;
            $params['category' . $category] = $category;

            list($keys, $options) = \XLite\Core\Database::prepareArray($options, $category);
            $condition .= ' AND c.name IN (' . implode(',', $keys) . ')';
            $params += $options;

            $conditions[] = 'NOT (' . $condition . ')';
        }

        return array(empty($conditions) ? null : '(' . implode(') AND (', $conditions) . ')', $params);
    }

    /**
     * Add "filter" condition to the query builder
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Current query builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function prepareOptionsAvailabilityCondition(\Doctrine\ORM\QueryBuilder $qb)
    {
        list($condition, $params) = $this->getOptionsAvailabilityCondition();

        return isset($condition)
            ? $qb->andWhere($condition)->setParameters($qb->getParameters() + $params)
            : $qb;
    }

    /**
     * Define query builder for getByCategory()
     *
     * @param string $category Category name
     *
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineByCategoryQuery($category)
    {
        return $this->createQueryBuilder()
            ->andWhere('c.category = :category')
            ->setParameter('category', $category);
    }

    /**
     * Define query for findByCategoryAndVisible() method
     *
     * @param string $category Category name
     *
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineByCategoryAndVisibleQuery($category)
    {
        return $this->createQueryBuilder()
            ->andWhere('c.category = :category AND c.type != :empty')
            ->setParameter('category', $category)
            ->setParameter('empty', '');
    }

    /**
     * Define query builder for getAllOptions()
     *
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineAllOptionsQuery()
    {
        return $this->createQueryBuilder();
    }

    /**
     * Check (and modify) option name and value
     *
     * @param string &$category Option category
     * @param string &$name     Option name
     * @param mixed  &$value    Option value
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function checkNameAndValue(&$category, &$name, &$value)
    {
        return true;
    }

    /**
     * Check if option type is a valid
     *
     * @param string $optionType Option type
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function isValidOptionType($optionType)
    {
        $simple = in_array(
            $optionType,
            array(
                '',
                'text',
                'textarea',
                'checkbox',
                'country',
                'state',
                'select',
                'serialized',
                'separator'
            )
        );

        if (!$simple && preg_match('/^XLite\\\(Module\\\.+\\\)?View\\\FormField\\\/Ss', $optionType)) {
            $simple = true;
        }

        return $simple;
    }
}
