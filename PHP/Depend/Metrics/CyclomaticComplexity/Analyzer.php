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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeVisitor/AbstractDefaultVisitor.php';
require_once 'PHP/Depend/Metrics/AnalyzerI.php';
require_once 'PHP/Depend/Metrics/FilterAwareI.php';
require_once 'PHP/Depend/Metrics/NodeAwareI.php';
require_once 'PHP/Depend/Metrics/ProjectAwareI.php';

/**
 * This class calculates the Cyclomatic Complexity Number(CCN) for the project,
 * classes, methods and functions.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
       extends PHP_Depend_Code_NodeVisitor_AbstractDefaultVisitor
    implements PHP_Depend_Metrics_AnalyzerI,
               PHP_Depend_Metrics_FilterAwareI,
               PHP_Depend_Metrics_NodeAwareI,
               PHP_Depend_Metrics_ProjectAwareI
{
    /**
     * Hash with all calculated node metrics.
     *
     * <code>
     * array(
     *     '0375e305-885a-4e91-8b5c-e25bda005438'  =>  array(
     *         'loc'    =>  42,
     *         'ncloc'  =>  17,
     *         'cc'     =>  12
     *     ),
     *     'e60c22f0-1a63-4c40-893e-ed3b35b84d0b'  =>  array(
     *         'loc'    =>  42,
     *         'ncloc'  =>  17,
     *         'cc'     =>  12
     *     )
     * )
     * </code>
     *
     * @type array<array>
     * @var array(string=>array) $_nodeMetrics
     */
    private $_nodeMetrics = null;
    
    /**
     * The project Cyclomatic Complexity Number.
     *
     * @type integer
     * @var integer $_ccn
     */
    private $_ccn = 0;
    
    /**
     * Extended Cyclomatic Complexity Number(CCN2) for the project.
     *
     * @type integer
     * @var integer $_ccn2
     */
    private $_ccn2 = 0;
    
    /**
     * Processes all {@link PHP_Depend_Code_Package} code nodes.
     *
     * @param PHP_Depend_Code_NodeIterator $packages All code packages.
     * 
     * @return void
     */
    public function analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        if ($this->_nodeMetrics === null) {
            // Init node metrics
            $this->_nodeMetrics = array();
            
            // Visit all packages
            foreach ($packages as $package) {
                $package->accept($this);
            }
        }
    }
    
    /**
     * This method will return an <b>array</b> with all generated metric values 
     * for the given <b>$node</b>. If there are no metrics for the requested 
     * node, this method will return an empty <b>array</b>.
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     * 
     * @return array(string=>mixed)
     */
    public function getNodeMetrics(PHP_Depend_Code_NodeI $node)
    {
        $metrics = array();
        if (isset($this->_nodeMetrics[$node->getUUID()])) {
            $metrics = $this->_nodeMetrics[$node->getUUID()];
        }
        return $metrics;
    }
    
    /**
     * Provides the project summary metrics as an <b>array</b>.
     *
     * @return array(string=>mixed)
     */
    public function getProjectMetrics()
    {
        return array(
            'ccn'   =>  $this->_ccn,
            'ccn2'  =>  $this->_ccn2
        );
    }
    
    /**
     * Visits a class node. 
     *
     * @param PHP_Depend_Code_Class $class The current class node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitor_AbstractDefaultVisitor::visitClass()
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $this->_nodeMetrics[$class->getUUID()] = array(
            'ccn'   =>  0,
            'ccn2'  =>  0
        );

        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }
    }
    
    /**
     * Visits a function node. 
     *
     * @param PHP_Depend_Code_Function $function The current function node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitFunction()
     */
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        // Get all method tokens
        $tokens = $function->getTokens();
        
        $ccn  = $this->_calculateCCN($tokens);
        $ccn2 = $this->_calculateCCN2($tokens);
        
        // The method metrics
        $this->_nodeMetrics[$function->getUUID()] = array(
            'ccn'   =>  $ccn,
            'ccn2'  =>  $ccn2
        );
        
        // Update project metrics
        $this->_ccn  += $ccn;
        $this->_ccn2 += $ccn2;
    }
    
    /**
     * Visits a code interface object.
     *
     * @param PHP_Depend_Code_Interface $interface The context code interface.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitInterface()
     */
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        // Empty visit method, we don't want interface metrics
    }
    
    /**
     * Visits a method node. 
     *
     * @param PHP_Depend_Code_Class $method The method class node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitMethod()
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        // Get all method tokens
        $tokens = $method->getTokens();
        
        $ccn  = $this->_calculateCCN($tokens);
        $ccn2 = $this->_calculateCCN2($tokens);
        
        // The method metrics
        $this->_nodeMetrics[$method->getUUID()] = array(
            'ccn'   =>  $ccn,
            'ccn2'  =>  $ccn2
        );
        
        // Update parent class metrics
        $uuid = $method->getParent()->getUUID();
        $this->_nodeMetrics[$uuid]['ccn']  += $ccn;
        $this->_nodeMetrics[$uuid]['ccn2'] += $ccn2;
        
        // Update project metrics
        $this->_ccn  += $ccn;
        $this->_ccn2 += $ccn2;
    }
    
    /**
     * Calculates the Cyclomatic Complexity Number (CCN). 
     *
     * @param array $tokens The input tokens.
     * 
     * @return integer
     */
    private function _calculateCCN(array $tokens)
    {
        // List of tokens
        $countingTokens = array(
            PHP_Depend_Code_TokenizerI::T_CASE,
            PHP_Depend_Code_TokenizerI::T_CATCH,
            PHP_Depend_Code_TokenizerI::T_ELSEIF,
            PHP_Depend_Code_TokenizerI::T_FOR,
            PHP_Depend_Code_TokenizerI::T_FOREACH,
            PHP_Depend_Code_TokenizerI::T_IF,
            PHP_Depend_Code_TokenizerI::T_QUESTION_MARK,
            PHP_Depend_Code_TokenizerI::T_WHILE
        );
        
        $ccn = 1;
        foreach ($tokens as $token) {
            if (in_array($token[0], $countingTokens) === true) {
                ++$ccn;
            }
        }
        return $ccn;
    }
    
    /**
     * Calculates the second version of the Cyclomatic Complexity Number (CCN2).
     * This version includes boolean operators like <b>&&</b>, <b>and</b>, 
     * <b>or</b> and <b>||</b>. 
     *
     * @param array $tokens The input tokens.
     * 
     * @return integer
     */
    private function _calculateCCN2(array $tokens)
    {
        // List of tokens
        $countingTokens = array(
            PHP_Depend_Code_TokenizerI::T_BOOLEAN_AND,
            PHP_Depend_Code_TokenizerI::T_BOOLEAN_OR,
            PHP_Depend_Code_TokenizerI::T_CASE,
            PHP_Depend_Code_TokenizerI::T_CATCH,
            PHP_Depend_Code_TokenizerI::T_ELSEIF,
            PHP_Depend_Code_TokenizerI::T_FOR,
            PHP_Depend_Code_TokenizerI::T_FOREACH,
            PHP_Depend_Code_TokenizerI::T_IF,
            PHP_Depend_Code_TokenizerI::T_LOGICAL_AND,
            PHP_Depend_Code_TokenizerI::T_LOGICAL_OR,
            PHP_Depend_Code_TokenizerI::T_QUESTION_MARK,
            PHP_Depend_Code_TokenizerI::T_WHILE
        );
        
        $ccn2 = 1;
        foreach ($tokens as $token) {
            if (in_array($token[0], $countingTokens) === true) {
                ++$ccn2;
            }
        }
        return $ccn2;
    }
}