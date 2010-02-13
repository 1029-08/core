<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2009 Creative Development <info@creativedevelopment.biz>  |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URLs:                                                       |
|                                                                              |
| FOR LITECOMMERCE                                                             |
| http://www.litecommerce.com/software_license_agreement.html                  |
|                                                                              |
| FOR LITECOMMERCE ASP EDITION                                                 |
| http://www.litecommerce.com/software_license_agreement_asp.html              |
|                                                                              |
| THIS  AGREEMENT EXPRESSES THE TERMS AND CONDITIONS ON WHICH YOU MAY USE THIS |
| SOFTWARE PROGRAM AND ASSOCIATED DOCUMENTATION THAT CREATIVE DEVELOPMENT, LLC |
| REGISTERED IN ULYANOVSK, RUSSIAN FEDERATION (hereinafter referred to as "THE |
| AUTHOR")  IS  FURNISHING  OR MAKING AVAILABLE TO  YOU  WITH  THIS  AGREEMENT |
| (COLLECTIVELY,  THE "SOFTWARE"). PLEASE REVIEW THE TERMS AND  CONDITIONS  OF |
| THIS LICENSE AGREEMENT CAREFULLY BEFORE INSTALLING OR USING THE SOFTWARE. BY |
| INSTALLING,  COPYING OR OTHERWISE USING THE SOFTWARE, YOU AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE ACCEPTING AND AGREEING  TO  THE  TERMS  OF  THIS |
| LICENSE AGREEMENT. IF YOU ARE NOT WILLING TO BE BOUND BY THIS AGREEMENT,  DO |
| NOT  INSTALL  OR USE THE SOFTWARE. VARIOUS COPYRIGHTS AND OTHER INTELLECTUAL |
| PROPERTY  RIGHTS PROTECT THE SOFTWARE. THIS AGREEMENT IS A LICENSE AGREEMENT |
| THAT  GIVES YOU LIMITED RIGHTS TO USE THE SOFTWARE AND NOT AN AGREEMENT  FOR |
| SALE  OR  FOR TRANSFER OF TITLE. THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY |
| GRANTED  BY  THIS AGREEMENT.                                                 |
|                                                                              |
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 foldmethod=marker shiftwidth=4: */

class XLite_Model_Wysiwyg_ImportParser extends XLite_Core_FlexyCompiler
{	
    public $imagesDir = null;	
    public $template = null;	
    public $templateName = null;

    function postprocess() // {{{
    {
        // page ::= .* edit_area_start edit_area edit_area_end .*
        // edit_area ::= .* (widget_start .* param_edit_area_start param_value  param_edit_area_end .* widget_end .*)*
        // param_edit_area_start ::=  <img .... name="param_name">

        // find edit_area_start
        $i=0;
        $this->error = '';
        while (!$this->isEditAreaStart($i,$this->templateName,$editAreaStartOffset)) {
            if ($i == count($this->tokens)) {
                return $this->error('No edit area starting mark is found');
            }
            $i++;
            $editAreaStartPos = $i;
        }
        $editAreaEndPos = $i;
        while (!$this->isEditAreaEnd($i, $end)) {
            if ($i >= count($this->tokens)) {
                return $this->error('No edit area ending mark is found');
            }
            // find and replace widget calls
            $start = $i;
            if ($this->isWidgetCall($i, $params, $endOffset)) {
                $code = $this->compileWidgetCall($params);
                $this->subst($this->tokens[$start]['start'], $endOffset, $code);
            } else if ($this->isWidgetStart($i, $params, $endOffset)) {
                // find all param edit areas
                $end = 0;
                while (!$this->isWidgetEnd($i, $end)) {
                    // inside widget marks
                    if ($i == count($this->tokens)) {
                        return $this->error('No widget ending mark is found');
                    }
                    $peaStartOffset = 0;
                    if ($this->isParamEditAreaStart($i, $name, $modifier, $peaStartOffset)) {
                        // find end of param area
                        $peaend = $i;
                        while (!$this->isParamEditAreaEnd($i, $end)) {
                            if ($i == count($this->tokens)) {
                                return $this->error('No edit area ending mark is found for parameter ' . $name);
                            }
                            $i++;
                            $peaend = $i;
                        }
                        $value = substr($this->source, $peaStartOffset, $this->tokens[$peaend]['start'] - $peaStartOffset);
                        $params[$name] = $this->reverseModifier($value, $modifier);
                    } else {
                        $i++;
                    }
                }
                $code = $this->compileWidgetCall($params);
                $this->subst($this->tokens[$start]['start'], $end, $code);
            } else {
                $i++;
            }
            $editAreaEndPos = $i;
        }
        // remove the reminder of the page
        $editAreaEndOffset = $this->tokens[$editAreaEndPos]['start'];
        $this->source = substr($this->source, 0, $editAreaEndOffset);
        $this->template = $this->translateTemplate(substr($this->substitute(), $editAreaStartOffset));
    } // }}}

    function translateTemplate($src)
    {
        $lay = XLite_Model_Layout::getInstance();
        return str_replace(array('<!--*', '*-->', 'style.css'), array('{*', '*}', 'skins/' . $lay->get("skin") . '/' . $lay->get("locale") . '/style.css'), $src);
    }

    function compileWidgetCall($params) // {{{
    {
        $call = "<widget";
        if (isset($params["open-close-tag"])) {
            $close = '/>';
            unset($params["open-close-tag"]);
        } else {
            $close = '>';
        }
        foreach ($params as $name => $value) {
            if ($value === true) {
                $call .= ' ' . $name;
            } else {
                $call .= ' ' . $name . '="' . str_replace('"', '&quot;', $value) . '"';
            }
        }
        return $call . $close;
    } // }}}

    function reverseModifier($str, $modifier) // {{{
    {
        switch ($modifier) {
        case '': $str = func_htmldecode($str); break;
        case ':u': $str = urldecode($str); break;
        }
        return $str;
    } // }}}

    function equalTokenArrays(&$i, $tokens, &$endOffset) // {{{
    {
        $old = $i;
        $endOffset = 0;
        for ($j = 0; $j<count($tokens); $j++) {
            if ($i>=count($this->tokens) ||
              !$this->equalTokens($this->tokens[$i], $tokens[$j])) {
                $i = $old;
                return false;
            }
            if ($this->tokens[$i]['end'] > $endOffset) {
                $endOffset = $this->tokens[$i]['end'];
            }
            $i++;
        }
        return true;
    } // }}}
    
    function equalTokens($token1, $token2) // {{{
    {
        foreach ($token2 as $name=>$value) {
            if ($name == 'value') {
                if (!strcasecmp(substr($this->source, $token1['start'], $token1['end']-$token1['start']), $value)) {
                    continue;
                } else {
                    return false;
                }
            }
            if (!isset($token1[$name]) || strcasecmp($token1[$name], $value)) {
                return false;
            }
        }
        return true;
    } // }}}
   
    function isEditAreaStart(&$i, &$template, &$endOffset) // {{{
    {
        $old = $i;
        if ($this->equalTokenArrays($i, array(
                array('type'=>'tag','name'=>'table'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'tag','name'=>'tr'),
                array('type'=>'tag','name'=>'td'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'attribute','name'=>'background'), array('type'=>'attribute-value', 'value'=>$this->imagesDir . '/' . EDIT_START_IMG),
                array('type'=>'attribute'), array('type'=>'attribute-value'), // template = ?, offset 19
                array('type'=>'close-tag','name'=>'td'),
                array('type'=>'close-tag','name'=>'tr'),
                array('type'=>'tag','name'=>'tr'),
                array('type'=>'tag','name'=>'td'),
            ), $endOffset)) {
            $template = $this->getTokenText($old+12);
            return true;
        }
        if ($this->equalTokenArrays($i, array(
                array('type'=>'tag','name'=>'tr'),
                array('type'=>'tag','name'=>'td'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'attribute','name'=>'background'), array('type'=>'attribute-value', 'value'=>$this->imagesDir . '/' . EDIT_START_IMG),
                array('type'=>'attribute'), array('type'=>'attribute-value'), // template = ?, offset 19
                array('type'=>'close-tag','name'=>'td'),
                array('type'=>'close-tag','name'=>'tr'),
                array('type'=>'tag','name'=>'tr'),
                array('type'=>'tag','name'=>'td'),
            ), $endOffset)) {
            $template = $this->getTokenText($old+9);
            return true;
        }
        return false;
    } // }}}

    function isEditAreaEnd(&$i, &$endOffset) // {{{
    {
        return $this->equalTokenArrays($i, array(
                array('type'=>'close-tag','name'=>'td'),
                array('type'=>'close-tag','name'=>'tr'),
                array('type'=>'tag','name'=>'tr'),
                array('type'=>'tag','name'=>'td'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'attribute','name'=>'background'), array('type'=>'attribute-value', 'value'=>$this->imagesDir . '/' .  EDIT_END_IMG),
                array('type'=>'close-tag','name'=>'td'),
                array('type'=>'close-tag','name'=>'tr'),
                array('type'=>'close-tag','name'=>'table'),
            ), $endOffset)  ||
            $this->equalTokenArrays($i, array(
                array('type'=>'tag','name'=>'tr'),
                array('type'=>'tag','name'=>'td'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'attribute','name'=>'background'), array('type'=>'attribute-value', 'value'=>$this->imagesDir . '/' .  EDIT_END_IMG),
                array('type'=>'close-tag','name'=>'td'),
                array('type'=>'close-tag','name'=>'tr'),
            ), $endOffset)  ||
            $this->equalTokenArrays($i, array(
                array('type'=>'close-tag','name'=>'td'),
                array('type'=>'close-tag','name'=>'tr'),
                array('type'=>'tag','name'=>'tr'),
                array('type'=>'tag','name'=>'td'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'attribute','name'=>'background'), array('type'=>'attribute-value', 'value'=>$this->imagesDir . '/' .  EDIT_END_IMG),
                array('type'=>'close-tag','name'=>'td'),
                array('type'=>'close-tag','name'=>'tr'),
            ), $endOffset) ||
            $this->equalTokenArrays($i, array(
                array('type'=>'tag','name'=>'img'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'attribute'), array('type'=>'attribute-value', 'value'=>$this->imagesDir . '/' . EDIT_END_SHORT_IMG),
            ), $endOffset);

    } // }}}

    function isParamEditAreaStart(&$i, &$name, &$modifier, &$endOffset) // {{{
    {
        $old = $i;
        $match = false; 
        if ($this->equalTokenArrays($i, array(
                array('type'=>'tag','name'=>'img'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'attribute'), array('type'=>'attribute-value'),
                array('type'=>'attribute'), array('type'=>'attribute-value', 'value'=>$this->imagesDir . '/' . EDIT_PARAM_START_IMG),
                array('type'=>'attribute', 'name'=>'name'), array('type'=>'attribute-value'),
                array('type'=>'attribute', 'name'=>'modifier'), array('type'=>'attribute-value')), $endOffset)) {
             $name = $this->getTokenText($old+8);
             $modifier = $this->getTokenText($old+10);
             $match = true;
         }
         return $match;
    } // }}}
    
    function isParamEditAreaEnd(&$i, &$endOffset)  // {{{
    {
        return $this->isEditAreaEnd($i, $endOffset);
    } // }}}
    
    function isWidgetStart(&$i, &$params, &$endOffset) // {{{
    {
        $old = $i;
        $match = false; 
        if ($this->equalTokenArrays($i, array(
             array('type'=>'tag','name'=>'table'),
             array('type'=>'attribute'), array('type'=>'attribute-value'),
             array('type'=>'attribute'), array('type'=>'attribute-value'),
             array('type'=>'tag','name'=>'tr'),
             array('type'=>'tag','name'=>'td'),
             array('type'=>'attribute'), array('type'=>'attribute-value'),
             array('type'=>'attribute', 'name'=>'background'), array('type'=>'attribute-value', 'value'=>$this->imagesDir . '/' . WIDGET_START_IMG),
             ), $endOffset)) {
            $i = $old+6;
            $tag = $this->parseTag($i, $endOffset);
            if ($this->equalTokenArrays($i, array(
                 array('type'=>'close-tag','name'=>'td'),
                 array('type'=>'close-tag','name'=>'tr'),
                 array('type'=>'tag','name'=>'tr'),
                 array('type'=>'tag','name'=>'td'),
                 ), $endOffset)) {
                $match = true; 
            }
        }
        if (!$match && $this->equalTokenArrays($i, array(
             array('type'=>'tag','name'=>'tr'),
             array('type'=>'tag','name'=>'td'),
             array('type'=>'attribute'), array('type'=>'attribute-value'),
             array('type'=>'attribute'), array('type'=>'attribute-value'),
             array('type'=>'attribute', 'name'=>'background'), array('type'=>'attribute-value', 'value'=>$this->imagesDir . '/' . WIDGET_START_IMG),
             ), $endOffset)) {
            $i = $old+1;
            $tag = $this->parseTag($i, $endOffset);
            if ($this->equalTokenArrays($i, array(
                 array('type'=>'close-tag','name'=>'td'),
                 array('type'=>'close-tag','name'=>'tr'),
                 ), $endOffset)) {
                $match = true; 
            }
        }
        if ($match) {
            $params = $tag['attributes'];
            if (isset($params['height'])) {
            	unset($params['height']);
            }
            if (isset($params['background'])) {
            	unset($params['background']);
            }
            if (isset($params['colspan'])) {
            	unset($params['colspan']);
            }
        }
        return $match;
    } // }}}

    function isWidgetEnd(&$i, &$endOffset) // {{{
    {
        return $this->equalTokenArrays($i, array(
            array('type'=>'close-tag', 'name'=>'td'),
            array('type'=>'close-tag', 'name'=>'tr'),
            array('type'=>'tag', 'name'=>'tr'),
            array('type'=>'tag', 'name'=>'td'),
            array('type'=>'attribute'), array('type'=>'attribute-value'),
            array('type'=>'attribute', 'name'=>'background'), array('type'=>'attribute-value', 'value'=>$this->imagesDir . '/' . WIDGET_END_IMG),
            array('type'=>'close-tag', 'name'=>'td'),
            array('type'=>'close-tag', 'name'=>'tr'),
            array('type'=>'close-tag', 'name'=>'table'),
            ), $endOffset) ||
            $this->equalTokenArrays($i, array(
            array('type'=>'close-tag', 'name'=>'td'),
            array('type'=>'close-tag', 'name'=>'tr'),
            array('type'=>'tag','name'=>'tr'),
            array('type'=>'tag','name'=>'td'),
            array('type'=>'attribute'), array('type'=>'attribute-value'), // colspan
            array('type'=>'attribute'), array('type'=>'attribute-value'), 
            array('type'=>'attribute', 'name'=>'background'), array('type'=>'attribute-value', 'value'=>$this->imagesDir . '/' .  WIDGET_END_IMG),
            array('type'=>'close-tag','name'=>'td'),
            array('type'=>'close-tag','name'=>'tr'),
             ), $endOffset);
    } // }}}

    function isWidgetCall(&$i, &$params, &$endOffset) // {{{
    {
        $old = $i;
        $match = false; 
        $parsedWidget = array(
             array('type'=>'tag','name'=>'a'),
             array('type'=>'attribute', 'name'=>'href'), array('type'=>'attribute-value'),
             array('type'=>'tag','name'=>'img'),
             array('type'=>'attribute'), array('type'=>'attribute-value'),
             array('type'=>'attribute'), array('type'=>'attribute-value'),
             array('type'=>'attribute', 'name'=>'src'), array('type'=>'attribute-value'), // widget image
             );
        if ($this->equalTokenArrays($i, $parsedWidget, $endOffset)) {
            $i = $old+3;
            $tag = $this->parseTag($i, $endOffset);
            if (func_starts_with($tag['attributes']['src'], $this->imagesDir . '/') && $this->equalTokenArrays($i, array(
                 array('type'=>'close-tag','name'=>'a'),
                 ), $endOffset)) {
                $match = true; 
            }
        }
        if (!$match && $this->equalTokenArrays($i, array_merge(array(
             array('type'=>'tag', 'name'=>'div')), $parsedWidget), $endOffset)) {
            $i = $old+4;
            $tag = $this->parseTag($i, $endOffset);
            if (func_starts_with($tag['attributes']['src'], $this->imagesDir . '/') && $this->equalTokenArrays($i, array(
                 array('type'=>'close-tag','name'=>'a'),
                 array('type'=>'close-tag','name'=>'div'),
                 ), $endOffset)) {
                $match = true; 
            }
        }
        if (!$match && $this->equalTokenArrays($i, array_merge(array(
             array('type'=>'tag', 'name'=>'tr'),array('type'=>'tag', 'name'=>'td')), $parsedWidget), $endOffset)) {
            $i = $old+5;
            $tag = $this->parseTag($i, $endOffset);
            if (func_starts_with($tag['attributes']['src'], $this->imagesDir . '/') && $this->equalTokenArrays($i, array(
                 array('type'=>'close-tag','name'=>'a'),
                 array('type'=>'close-tag','name'=>'td'),
                 array('type'=>'close-tag','name'=>'tr'),
                 ), $endOffset)) {
                $match = true; 
            }
        }

        if (!$match && $this->equalTokenArrays($i, array(
             array('type'=>'tag','name'=>'img'),
             array('type'=>'attribute'), array('type'=>'attribute-value'),
             array('type'=>'attribute'), array('type'=>'attribute-value'),
             array('type'=>'attribute', 'name'=>'src'), array('type'=>'attribute-value', 'value'=>$this->imagesDir . '/' . DUMMY_WIDGET_IMG), // widget image
              ), $endOffset)) {
            $i = $old;
            $tag = $this->parseTag($i, $endOffset);
            $match = true; 
        }
        if ($match) {
            $params = $tag['attributes'];
            if (isset($params['align'])) {
            	unset($params['align']);
            }
            if (isset($params['border'])) {
            	unset($params['border']);
            }
            if (isset($params['src'])) {
            	unset($params['src']);
            }
        }
        return $match;
    } // }}}

    function parseTag(&$i, &$endOffset) // {{{
    {
        if ($i >= count($this->tokens)) {
            return false;
        }
        if ($this->tokens[$i]['type'] == 'tag' || $this->tokens[$i]['type'] == 'open-close-tag') {
            $endOffset = $this->tokens[$i]['end'];
            $tag = $this->tokens[$i];
            $i++;
            $name = '';
            while ($i<count($this->tokens) && 
                ($this->tokens[$i]['type'] == 'attribute' || 
                 $this->tokens[$i]['type'] == 'attribute-value')) {
                if ($this->tokens[$i]['type'] == 'attribute') {
                    if ($name) {
                        $tag['attributes'][$name] = true;
                    }
                    $name = $this->tokens[$i]['name'];
                } else {
                    $tag['attributes'][$name] = $this->getTokenText($i);
                    $name = '';
                }
                $i++;
            }
            if ($name) {
                $tag['attributes'][$name] = true;
            }
            return $tag;
        }
        return false;
    } // }}}

    function error($msg) // {{{
    {
        $this->error = $msg;
        return false;
    } // }}}
}

