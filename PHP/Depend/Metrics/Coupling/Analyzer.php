<?php
/**
 * This file is part of PHP_Depend.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Metrics/AbstractAnalyzer.php';
require_once 'PHP/Depend/Metrics/AnalyzerI.php';
require_once 'PHP/Depend/Metrics/ProjectAwareI.php';

require_once 'PHP/Depend/Util/NodeSet.php';

/**
 * This analyzer collects coupling values for the hole project. It calculates
 * all function and method <b>calls</b> and the <b>fanout</b>, that means the
 * number of referenced types.
 *
 * The FANOUT calculation is based on the definition used by the apache maven
 * project.
 *
 * <ul>
 *   <li>field declarations (Uses doc comment annotations)</li>
 *   <li>formal parameters and return types (The return type uses doc comment
 *   annotations)</li>
 *   <li>throws declarations (Uses doc comment annotations)</li>
 *   <li>local variables</li>
 * </ul>
 *
 * http://www.jajakarta.org/turbine/en/turbine/maven/reference/metrics.html
 *
 * The implemented algorithm counts each type only once for a method and function.
 * Any type that is either a supertype or a subtype of the class is not counted.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_Coupling_Analyzer
       extends PHP_Depend_Metrics_AbstractAnalyzer
    implements PHP_Depend_Metrics_AnalyzerI,
               PHP_Depend_Metrics_ProjectAwareI
{
    /**
     * The number of method or function calls.
     *
     * @var integer $_calls
     */
    private $_calls = -1;

    /**
     * Number of fanouts.
     *
     * @var integer $_fanout
     */
    private $_fanout = -1;

    /**
     * Provides the project summary as an <b>array</b>.
     *
     * <code>
     * array(
     *     'calls'   =>  23,
     *     'fanout'  =>  42
     * )
     * </code>
     *
     * @return array(string=>mixed)
     */
    public function getProjectMetrics()
    {
        return array(
            'calls'   =>  $this->_calls,
            'fanout'  =>  $this->_fanout
        );
    }

    /**
     * Processes all {@link PHP_Reflection_AST_Package} code nodes.
     *
     * @param PHP_Reflection_AST_Iterator $packages All code packages.
     *
     * @return void
     */
    public function analyze(PHP_Reflection_AST_Iterator $packages)
    {
        // Check for previous run
        if ($this->_calls === -1) {

            $this->fireStartAnalyzer();

            // Init metrics
            $this->_calls  = 0;
            $this->_fanout = 0;

            // Process all packages
            foreach ($packages as $package) {
                $package->accept($this);
            }

            $this->fireEndAnalyzer();
        }
    }

    /**
     * Visits a function node.
     *
     * @param PHP_Reflection_AST_Function $function The current function node.
     *
     * @return void
     * @see PHP_Reflection_VisitorI::visitFunction()
     */
    public function visitFunction(PHP_Reflection_AST_FunctionI $function)
    {
        $this->fireStartFunction($function);

        $fanoutSet = new PHP_Depend_Util_NodeSet();

        if (($returnType = $function->getReturnType()) !== null) {
            $fanoutSet->add($returnType);
        }
        foreach ($function->getExceptionTypes() as $exceptionType) {
            $fanoutSet->add($exceptionType);
        }
        foreach ($function->getDependencies() as $dependency) {
            $fanoutSet->add($dependency);
        }

        $this->_fanout += $fanoutSet->size();
        $this->_countCalls($function);

        $this->fireEndFunction($function);
    }

    /**
     * Visits a method node.
     *
     * @param PHP_Reflection_AST_MethodI $method The method class node.
     *
     * @return void
     * @see PHP_Reflection_VisitorI::visitMethod()
     */
    public function visitMethod(PHP_Reflection_AST_MethodI $method)
    {
        $this->fireStartMethod($method);

        $parentNode = $method->getParent();
        $fanoutSet  = new PHP_Depend_Util_NodeSet();

        if (($type = $method->getReturnType()) !== null) {
            if (!$type->isSubtypeOf($parentNode) &&
                !$parentNode->isSubtypeOf($type)) {

                $fanoutSet->add($type);
            }
        }
        foreach ($method->getExceptionTypes() as $type) {
            if (!$type->isSubtypeOf($parentNode) &&
                !$parentNode->isSubtypeOf($type)) {

                $fanoutSet->add($type);
            }
        }
        foreach ($method->getDependencies() as $type) {
            if (!$type->isSubtypeOf($parentNode) &&
                !$parentNode->isSubtypeOf($type)) {

                $fanoutSet->add($type);
            }
        }

        $this->_fanout += $fanoutSet->size();
        $this->_countCalls($method);

        $this->fireEndMethod($method);
    }

    /**
     * Visits a property node.
     *
     * @param PHP_Reflection_AST_PropertyI $property The property class node.
     *
     * @return void
     * @see PHP_Reflection_VisitorI::visitProperty()
     */
    public function visitProperty(PHP_Reflection_AST_PropertyI $property)
    {
        $this->fireStartProperty($property);

        // Check for not null
        if (($type = $property->getType()) !== null) {
            $parent = $property->getParent();

            // Only increment if these types are not part of the same hierarchy
            if (!$type->isSubtypeOf($parent) && !$parent->isSubtypeOf($type)) {
                ++$this->_fanout;
            }
        }

        $this->fireEndProperty($property);
    }

    /**
     * Counts all calls within the given <b>$callable</b>
     *
     * @param PHP_Reflection_AST_CallableI $callable Context callable.
     *
     * @return void
     */
    private function _countCalls(PHP_Reflection_AST_CallableI $callable)
    {
        $callT  = array(
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_VARIABLE
        );
        $chainT = array(
            PHP_Reflection_TokenizerI::T_DOUBLE_COLON,
            PHP_Reflection_TokenizerI::T_OBJECT_OPERATOR,
        );

        $called = array();

        $tokens = $callable->getTokens();
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            // Skip non parenthesis tokens
            if ($tokens[$i][0] !== PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN) {
                continue;
            }
            // Skip first token
            if (!isset($tokens[$i - 1]) || !in_array($tokens[$i - 1][0], $callT)) {
                continue;
            }
            // Count if no other token exists
            if (!isset($tokens[$i - 2]) && !isset($called[$tokens[$i - 1][1]])) {
                $called[$tokens[$i - 1][1]] = true;
                ++$this->_calls;
                continue;
            } else if (in_array($tokens[$i - 2][0], $chainT)) {
                $identifier = $tokens[$i - 2][1] . $tokens[$i - 1][1];
                for ($j = $i - 3; $j >= 0; --$j) {
                    if (!in_array($tokens[$j][0], $callT)
                     && !in_array($tokens[$j][0], $chainT)) {

                        break;
                    }
                    $identifier = $tokens[$j][1] . $identifier;
                }

                if (!isset($called[$identifier])) {
                    $called[$identifier] = true;
                    ++$this->_calls;
                }
            } else if ($tokens[$i - 2][0] !== PHP_Reflection_TokenizerI::T_NEW) {
                $called[$tokens[$i - 1][1]] = true;
                ++$this->_calls;
            }
        }
    }
}