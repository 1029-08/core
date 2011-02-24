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

namespace XLite\Model;

/**
 * XML parser
 * 
 * @see   ____class_see____
 * @since 3.0.0
 */
class XML extends \XLite\Base
{
    /**
     * XML parser
     * 
     * @var   resource
     * @see   ____var_see____
     * @since 3.0.0
     */
    protected $xml_parser = null;

    /**
     * XML error message
     * 
     * @var   string
     * @see   ____var_see____
     * @since 3.0.0
     */
    protected $error = '';

    /**
     * Parser encode character set
     * 
     * @var   string
     * @see   ____var_see____
     * @since 3.0.0
     */
    protected $parserEncode = 'ISO-8859-1';

    /**
     * Postprocessed XML with errors placeholder 
     * 
     * @var   string
     * @see   ____var_see____
     * @since 3.0.0
     */
    protected $xml = '';

    /**
     * Constructor
     *
     * @return void
     * @since  3.0.0
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Parse XML errors 
     * 
     * @param string $xml Initial XML
     *  
     * @return void
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function parseXMLErrors($xml)
    {
        $errorCode = xml_get_error_code($this->xml_parser);
        $this->error = 'XML Parse Error #$errorCode:  ' .
            xml_error_string($errorCode);

        $pos = xml_get_current_byte_index($this->xml_parser);

        $xml = substr($xml, 0, $pos) . '$$$' . substr($xml, $pos);
        $xml = htmlspecialchars($xml);
        $xml = str_replace('$$$', '<font color="red"><strong> HERE </strong></font>', $xml);

        $this->xml = $xml;
    }

    /**
     * Parse 
     * 
     * @param string $xml XML
     *  
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function parse($xml)
    {
        if (!is_scalar($xml)) {
            $this->doDie('wrong xml');
        }

        $this->xml_parser = xml_parser_create($this->parserEncode);

        $xml = trim($xml);

        $result = true;
        $values = array();
        $index = array();
        
        if (!xml_parse_into_struct($this->xml_parser, $xml, $values, $index)) {
            $this->parseXMLErrors($xml);
            $result = false;
        }

        xml_parser_free($this->xml_parser);

        $i = 0;

        return $result ? $this->compileTree($values, $i) : array();
    }

    /**
     * Build XML tree
     * 
     * @param array   $values XML nodes
     * @param integer &$i     Pointer
     *  
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function compileTree(array $values, &$i)
    {
        $tree = array();

        while (
            count($values) > $i
            && 'close' != $values[$i]['type']
        ) {

            $type = $values[$i]['type'];

            if ('open' == $type || 'complete' == $type) {

                if ('open' == $type) {

                    $i++;
                    $value = $this->compileTree($values, $i);

                } else {

                    $value = isset($values[$i]['value']) ? $values[$i]['value'] : null;
                }

                $tag = $values[$i]['tag'];
                $attributes = isset($values[$i]['attributes']) ? $values[$i]['attributes'] : null;

                if (!is_null($attributes) && isset($attributes['ID'])) {

                    if (!isset($tree[$tag])) {
                        $tree[$tag] = array();
                    }

                    $tree[$tag][$attributes['ID']] = $value;

                } else {

                    // repeating tag
                    $postfix = '';
                    while (isset($tree[$tag . $postfix])) {
                        $postfix = '' == $postfix ? 1 : $postfix + 1;
                    }

                    $tree[$tag . $postfix] = $value;
                }

            }

            $i++;
        }

        return $tree;
    }

    /**
     * Get formatted XML block
     * 
     * @param string $xml XML
     *  
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getFormattedXML($xml)
    {
        $xml = preg_replace('/>[ ' . "\t\n\r" . ']+</', '><', trim($xml));

        $level = -1;
        $i = 0;
        $prev = 0;
        $path = array();
        while (preg_match('/<([\w\d_\?]+)(?: [^>]+)?' . '>/S', substr($xml, $i), $match)) {
            $tn = $match[1];
            $len = strlen($match[0]);
            $i = strpos($xml, $match[0], $i);
            $level++;

            // Detect close-tags
            if (0 < $i - $prev) {
                $ends = substr_count(substr($xml, $prev, $i - $prev), '</');
                if (0 < $ends) {
                    $level -= $ends;
                }
            }

            // Add indents
            if (0 < $level) {
                $xml = substr($xml, 0, $i) . str_repeat("\t", $level) . substr($xml, $i);
                $i += $level;
            }

            // Add EOL symbol
            $end = strpos(substr($xml, $i + $len), '</' . $tn . '>');
            if (
                (false !== $end && preg_match('/<[\w\d_\?]+(?: [^>]+)?' . '>/S', substr($xml, $i + $len, $end)))
                || '?' == substr($tn, 0, 1)
            ) {
                $xml = substr($xml, 0, $i + $len) . "\n" . substr($xml, $i + $len);
                $i++;

                // Add indent for close-tag
                if (0 < $level) {
                    $end += $i + $len;
                    $xml = substr($xml, 0, $end) . str_repeat("\t", $level) . substr($xml, $end);
                }
            }

            $i += $len;
            $prev = $i;
        }

        return preg_replace('/(<\/[\w\d_]+>)/', '\1' . "\n", $xml);
    }
}
