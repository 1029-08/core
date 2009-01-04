<?php
/**
 * This file is part of PHP_Reflection.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Input
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Reflection/Input/FileExtensionFilter.php';

/**
 * Test case for the file extension filter.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Input
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Input_FileExtensionFilterTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests the extension filter for simple *.inc.
     * 
     * @return void
     */
    public function testFileExtensionFilterWithIncExtension()
    {
        $it     = new DirectoryIterator(self::createResourceURI('input'));
        $filter = new PHP_Reflection_Input_FileExtensionFilter(array('inc'));
        
        $result = array();
        foreach ($it as $file) {
            if ($filter->accept($file)) {
                $result[] = $file->getFilename();
            }
        }
        
        sort($result);
        
        $expected = array(
            'file_g.inc',
            'file_h.inc',
            'file_i.inc',
        );
        
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Tests the extension filter with multiple allowed file extensions.
     * 
     * @return void
     */
    public function testFileExtensionFilterWithMultipleExtensions()
    {
        $it     = new DirectoryIterator(self::createResourceURI('input'));
        $filter = new PHP_Reflection_Input_FileExtensionFilter(array('phtml', 'inc'));
        
        $result = array();
        foreach ($it as $file) {
            if ($filter->accept($file)) {
                $result[] = $file->getFilename();
            }
        }
        
        sort($result);
        
        $expected = array(
            'file_d.phtml',
            'file_e.phtml',
            'file_f.phtml',
            'file_g.inc',
            'file_h.inc',
            'file_i.inc',
        );
        
        $this->assertEquals($expected, $result);
    }
}