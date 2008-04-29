<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Code/DefaultBuilder.php';

/**
 * Test case implementation for the default node builder implementation.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_DefaultBuilderTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the node builder creates a class for the same name only once.
     *
     * @return void
     */
    public function testBuildClassUnique()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        $class1  = $builder->buildClass('clazz1', 'clazz1.php');
        $class2  = $builder->buildClass('clazz1', 'clazz1.php');
        
        $this->assertType('PHP_Depend_Code_Class', $class1);
        $this->assertType('PHP_Depend_Code_Class', $class2);
        
        $this->assertSame($class1, $class2);
    }
    
    /**
     * Tests that the node builder appends a default package to all new created
     * classes.
     *
     * @return void
     */
    public function testBuildClassDefaultPackage()
    {
        $defaultPackage = PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE; 
        
        $builder = new PHP_Depend_Code_DefaultBuilder();
        $class1  = $builder->buildClass('clazz1', 0, 'clazz1.php');
        $class2  = $builder->buildClass('clazz2', 0, 'clazz2.php');
        
        $this->assertNotNull($class1->getPackage());
        $this->assertNotNull($class2->getPackage());
        
        $this->assertSame($class1->getPackage(), $class2->getPackage());
        $this->assertEquals($defaultPackage, $class1->getPackage()->getName());
    }
    
    /**
     * Tests that the node build generates an unique interface instance for the
     * same identifier.
     *
     * @return void
     */
    public function testBuildInterfaceUnique()
    {
        $builder    = new PHP_Depend_Code_DefaultBuilder();
        $interface1 = $builder->buildInterface('interface1', 'interface1.php');
        $interface2 = $builder->buildInterface('interface1', 'interface1.php');
        
        $this->assertType('PHP_Depend_Code_Interface', $interface1);
        $this->assertType('PHP_Depend_Code_Interface', $interface2);
        
        $this->assertSame($interface1, $interface2);
    }
    
    /**
     * Tests the PHP_Depend_Code_Method build method.
     *
     * @return void
     */
    public function testBuildMethod()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        $method  = $builder->buildMethod('method', 0);
        
        $this->assertType('PHP_Depend_Code_Method', $method);
    }
    /**
     * Tests that the node builder creates a package for the same name only once.
     *
     * @return void
     */
    public function testBuildPackageUnique()
    {
        $builder  = new PHP_Depend_Code_DefaultBuilder();
        $package1 = $builder->buildPackage('package1');
        $package2 = $builder->buildPackage('package1');
        
        $this->assertType('PHP_Depend_Code_Package', $package1);
        $this->assertType('PHP_Depend_Code_Package', $package2);
        
        $this->assertSame($package1, $package2);
    }
    
    /**
     * Tests the implemented {@link IteratorAggregate}.
     *
     * @return void
     */
    public function testGetIteratorWithPackages()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        
        $packages = array(
            'package1'  =>  $builder->buildPackage('package1'),
            'package2'  =>  $builder->buildPackage('package2'),
            'package3'  =>  $builder->buildPackage('package3')
        );
        
        foreach ($builder as $name => $package) {
            $this->assertArrayHasKey($name, $packages);
            $this->assertEquals($name, $package->getName());
            $this->assertSame($packages[$name], $package);
        }
    }
    
    /**
     * Tests the {@link PHP_Depend_Code_DefaultBuilder::getPackages()} method.
     *
     * @return void
     */
    public function testGetPackages()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        
        $packages = array(
            'package1'  =>  $builder->buildPackage('package1'),
            'package2'  =>  $builder->buildPackage('package2'),
            'package3'  =>  $builder->buildPackage('package3')
        );
        
        foreach ($builder->getPackages() as $name => $package) {
            $this->assertArrayHasKey($name, $packages);
            $this->assertEquals($name, $package->getName());
            $this->assertSame($packages[$name], $package);
        }
    }
    
    /**
     * Tests that the node builder appends a default package to all new created
     * functions.
     *
     * @return void
     */
    public function testBuildFunctionDefaultPackage()
    {
        $defaultPackage = PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE;
        
        $builder   = new PHP_Depend_Code_DefaultBuilder();
        $function1 = $builder->buildFunction('func1', 0);
        $function2 = $builder->buildFunction('func2', 0);
        
        $this->assertNotNull($function1->getPackage());
        $this->assertNotNull($function2->getPackage());
        
        $this->assertSame($function1->getPackage(), $function2->getPackage());
        $this->assertEquals($defaultPackage, $function1->getPackage()->getName());
    }
}