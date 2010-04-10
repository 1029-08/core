<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/AbstractItem.php';
require_once 'PHP/Depend/Code/DependencyNodeI.php';

/**
 * Abstract base class for callable objects.
 * 
 * Callable objects is a generic parent for methods and functions.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
abstract class PHP_Depend_Code_AbstractCallable
    extends PHP_Depend_Code_AbstractItem 
    implements PHP_Depend_Code_DependencyNodeI
{
    /**
     * The tokens for this function.
     *
     * @type array<mixed>
     * @var array(mixed) $tokens
     */
    protected $tokens = array();
    
    /**
     * List of {@link PHP_Depend_Code_AbstractType} objects this function depends on.
     *
     * @type array<PHP_Depend_Code_AbstractType>
     * @var array(PHP_Depend_Code_AbstractType) $dependencies
     */
    protected $dependencies = array();
    
    /**
     * The return type for this callable. By default and for scalar types this
     * will be <b>null</b>.
     *
     * @type PHP_Depend_Code_AbstractType
     * @var PHP_Depend_Code_AbstractType $_returnType
     */
    private $_returnType = null;
    
    /**
     * A list of all thrown exception types.
     *
     * @type array<PHP_Depend_Code_AbstractType>
     * @var array(PHP_Depend_Code_AbstractType) $_exceptionTypes
     */
    private $_exceptionTypes = array();
    
    /**
     * Returns the tokens found in the function body.
     *
     * @return array(mixed)
     */
    public function getTokens()
    {
        return $this->tokens;
    }
    
    /**
     * Sets the tokens found in the function body.
     * 
     * @param array(mixed) $tokens The body tokens.
     * 
     * @return void
     */
    public function setTokens(array $tokens)
    {
        $this->tokens = $tokens;
    }
    
    /**
     * Returns all {@link PHP_Depend_Code_AbstractType} objects this function 
     * depends on.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getDependencies()
    {
        return new PHP_Depend_Code_NodeIterator($this->dependencies);
    }
    
    /**
     * Adds the given {@link PHP_Depend_Code_AbstractType} object as dependency.
     *
     * @param PHP_Depend_Code_AbstractType $type A type this function depends on.
     * 
     * @return void
     */
    public function addDependency(PHP_Depend_Code_AbstractType $type)
    {
        if (in_array($type, $this->dependencies, true) === false) {
            $this->dependencies[] = $type;
        }
    }
    
    /**
     * Removes the given {@link PHP_Depend_Code_AbstractType} object from the 
     * dependency list.
     *
     * @param PHP_Depend_Code_AbstractType $type A type to remove.
     * 
     * @return void
     */
    public function removeDependency(PHP_Depend_Code_AbstractType $type)
    {
        if (($i = array_search($type, $this->dependencies, true)) !== false) {
            // Remove from internal list
            unset($this->dependencies[$i]);
        }
    }
    
    /**
     * Returns the return type of this callable. By default and for scalar types
     * this will be <b>null</b>.
     *
     * @return PHP_Depend_Code_AbstractType
     */
    public function getReturnType()
    {
        return $this->_returnType;
    }
    
    /**
     * Sets the return type of this callable.
     *
     * @param PHP_Depend_Code_AbstractType $returnType The return type of this.
     * 
     * @return void
     */
    public function setReturnType(PHP_Depend_Code_AbstractType $returnType)
    {
        $this->_returnType = $returnType;
    }

    /**
     * Returns an iterator with all thrown exception types.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getExceptionTypes()
    {
        return new PHP_Depend_Code_NodeIterator($this->_exceptionTypes);
    }
    
    /**
     * Adds an exception to the list of thrown exception types.
     *
     * @param PHP_Depend_Code_AbstractType $exceptionType Thrown exception.
     * 
     * @return void
     */
    public function addExceptionType(PHP_Depend_Code_AbstractType $exceptionType)
    {
        if (in_array($exceptionType, $this->_exceptionTypes, true) === false) {
            $this->_exceptionTypes[] = $exceptionType;
        }
    }
    
    /**
     * Removes an exception from the list of thrown exception types.
     *
     * @param PHP_Depend_Code_AbstractType $exceptionType Thrown exception.
     * 
     * @return void
     */
    public function removeExceptionType(PHP_Depend_Code_AbstractType $exceptionType)
    {
        if (($i = array_search($exceptionType, $this->_exceptionTypes, true))) {
            unset($this->_exceptionTypes[$i]);
        }
    }
}