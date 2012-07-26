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
 */

namespace Includes\Decorator\Plugin\Doctrine\Plugin\Money;

/**
 * Main 
 *
 */
class Main extends \Includes\Decorator\Plugin\Doctrine\Plugin\APlugin
{
    /**
     * List of <file, code> pairs (code replacements)
     *
     * @var array
     */
    protected $replacements = array();

    /**
     * Autogenerated net getter
     *
     * @var string
     */
    protected $templateGet = <<<CODE
    /**
     * Get <purpose> <fieldName>
     *
     * @return float
     */
    public function get<methodName>()
    {
        return \XLite\Logic\Price::getInstance()->apply(\$this, '<getter>', array(<behaviors>), '<purpose>');
    }
CODE;

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        // It's the metadata collected by Doctrine
        foreach ($this->getMetadata() as $main) {
            $node = static::getClassesTree()->find($main->name);

            // Process only certain classes
            if (!$node->isTopLevelNode() && !$node->isDecorator()) {

                foreach ($main->fieldMappings as $field => $info) {
                    if ('money' == $info['type']) {
                        $fieldName = str_replace('_', ' ', $field);
                        $fieldName = preg_replace('/([a-z])([A-Z])([a-z])/Sse', '"$1 " . strtolower("$2") . "$3"', $fieldName);

                        $purposes = array(
                            'net' => '',
                        );
                        $behaviors = array();

                        if (isset($info['options']) && is_array($info['options'])) {
                            foreach ($info['options'] as $option) {
                                if ($option instanceOf \XLite\Core\Doctrine\Annotation\Behavior) {
                                    $behaviors = array_merge($behaviors, $option->list);
    
                                } elseif ($option instanceOf \XLite\Core\Doctrine\Annotation\Purpose) {
                                    $purposes[$option->name] = $option->source;
                                }
                            }
                        }

                        foreach ($purposes as $purpose => $source) {
                            $camelField = ucfirst(\Doctrine\Common\Util\Inflector::camelize($field));
                            $source = $source
                                ? ucfirst($source) . $camelField
                                : $camelField;
                            $this->addReplacement(
                                $main,
                                'get',
                                array(
                                    '<getter>'     => 'get' . $source,
                                    '<fieldName>'  => $fieldName,
                                    '<methodName>' => ucfirst($purpose) . $camelField,
                                    '<behaviors>'  => $behaviors ? '\'' . implode('\',\'', $behaviors) . '\'' : '',
                                    '<purpose>'    => $purpose,
                                )
                            );
                        }
                    }
                }
            }
        }

        // Populate changes
        $this->writeData();
    }

    // {{{ Replacements

    /**
     * Add code to replace
     *
     * @param \Doctrine\ORM\Mapping\ClassMetadata $data        Class metadata
     * @param string                              $template    Template to use
     * @param array                               $substitutes List of entries to substitude
     *
     * @return void
     */
    protected function addReplacement(\Doctrine\ORM\Mapping\ClassMetadata $data, $template, array $substitutes)
    {
        if (!empty($substitutes)) {
            $file = \Includes\Utils\Converter::getClassFile($data->reflClass->getName());

            if (!isset($this->replacements[$file])) {
                $this->replacements[$file] = '';
            }

            $this->replacements[$file] .= $this->substituteTemplate($template, $substitutes) . PHP_EOL . PHP_EOL;
        }
    }

    // }}}

    // {{{ Methods to write changes

    /**
     * Put prepared code into the files
     *
     * @return void
     */
    protected function writeData()
    {
        foreach ($this->replacements as $file => $code) {
            \Includes\Utils\FileManager::write(
                $file = LC_DIR_CACHE_CLASSES . $file,
                \Includes\Decorator\Utils\Tokenizer::addCodeToClassBody($file, $code)
            );
        }
    }

    /**
     * Substitute entries in code template
     *
     * @param string $template Template to prepare
     * @param array  $entries  List of <entry, value> pairs
     *
     * @return string
     */
    protected function substituteTemplate($template, array $entries)
    {
        return str_replace(array_keys($entries), $entries, $this->{'template' . ucfirst($template)});
    }

    // }}}

    // {{{ Auxiliary methods

    /**
     * Alias
     *
     * @param string $class Class name OPTIONAL
     *
     * @return array|\Doctrine\ORM\Mapping\ClassMetadata
     */
    protected function getMetadata($class = null)
    {
        return \Includes\Decorator\Plugin\Doctrine\Utils\EntityManager::getAllMetadata($class);
    }

    // }}}
}
