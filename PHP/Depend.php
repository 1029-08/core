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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Metrics/AnalyzerLoader.php';
require_once 'PHP/Depend/Metrics/Dependency/Analyzer.php';

require_once 'PHP/Reflection.php';
// TODO: Refactor and remove these dependencies
require_once 'PHP/Reflection/AST/Iterator/GlobalPackageFilter.php';
require_once 'PHP/Reflection/AST/Iterator/InternalPackageFilter.php';

/**
 * PHP_Depend analyzes php class files and generates metrics.
 * 
 * The PHP_Depend is a php port/adaption of the Java class file analyzer 
 * <a href="http://clarkware.com/software/JDepend.html">JDepend</a>.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend
{
    /**
     * List of source directories.
     *
     * @var array(string) $directories
     */
    protected $directories = array();
    
    /**
     * The used code node builder.
     *
     * @var PHP_Reflection_BuilderI $nodeBuilder
     */
    protected $nodeBuilder = null;
    
    /**
     * Generated {@link PHP_Reflection_AST_Package} objects.
     *
     * @var Iterator $packages
     */
    protected $packages = null;
    
    /**
     * List of all registered {@link PHP_Depend_Log_LoggerI} instances.
     *
     * @var array(PHP_Depend_Log_LoggerI) $loggers
     */
    protected $loggers = array();
    
    /**
     * List of accepted source file extensions.
     *
     * @var array(string) $_extensions
     */
    private $_extensions = array();
    
    /**
     * List of exclude directories and directory patterns.
     *
     * @var array(string) $_excludeDirectories
     */
    private $_excludeDirectories = array();
    
    /**
     * A composite filter for input files.
     *
     * @var PHP_Depend_Util_CompositeFilter $_fileFilter
     */
    private $_fileFilter = null;
    
    /**
     * A composite filter for source packages.
     *
     * @var PHP_Reflection_AST_Iterator_CompositeFilter $_codeFilter
     */
    private $_codeFilter = null;
    
    /**
     * Should the parse ignore doc comment annotations?
     *
     * @var boolean $_withoutAnnotations
     */
    private $_withoutAnnotations = false;
    
    /**
     * Should PHP_Depend treat <b>+unknown</b> as a regular project package?
     *
     * @var boolean $_supportBadDocumentation
     */
    private $_supportBadDocumentation = false;
    
    /**
     * List or registered listeners.
     *
     * @var array(PHP_Depend_ProcessListenerI) $_listeners
     */
    private $_listeners = array();
    
    /**
     * List of analyzer options.
     *
     * @var array(string=>mixed) $_options
     */
    private $_options = array();
    
    /**
     * Constructs a new php depend facade.
     */
    public function __construct()
    {
        $this->_codeFilter = new PHP_Reflection_AST_Iterator_CompositeFilter();
        $this->_fileFilter = new PHP_Reflection_Input_CompositeFilter();
    }

    /**
     * Adds the specified directory to the list of directories to be analyzed.
     *
     * @param string $directory The php source directory.
     * 
     * @return void
     */
    public function addDirectory($directory)
    {
        $dir = realpath($directory);
        
        if (!is_dir($dir)) {
            throw new RuntimeException("Invalid directory '{$directory}' added.");
        }
        
        $this->directories[] = $dir;
    }
    
    /**
     * Adds a valid extension for source files, for example <b>php</b>.
     *
     * @param string $extension The file extension.
     * 
     * @return void
     */
    public function addExtension($extension)
    {
        if (in_array($extension, $this->_extensions) === false) {
            $this->_extensions[] = $extension;
        }
    }
    
    /**
     * Adds a directory or a directory pattern to the list of exclude paths.
     *
     * @param string $excludeDirectory The exclude directory.
     * 
     * @return void
     */
    public function addExcludeDirectory($excludeDirectory)
    {
        if (in_array($excludeDirectory, $this->_excludeDirectories) === false) {
            $this->_excludeDirectories[] = $excludeDirectory;
        }
    }
    
    /**
     * Adds a logger to the output list.
     *
     * @param PHP_Depend_Log_LoggerI $logger The logger instance.
     * 
     * @return void
     */
    public function addLogger(PHP_Depend_Log_LoggerI $logger)
    {
        $this->loggers[] = $logger;
    }
    
    /**
     * Adds an additional code filter. These filters could be used to hide 
     * external libraries and global stuff from the PDepend output.  
     *
     * @param PHP_Reflection_AST_Iterator_FilterI $filter The code filter.
     * 
     * @return void
     */
    public function addCodeFilter(PHP_Reflection_AST_Iterator_FilterI $filter)
    {
        $this->_codeFilter->addFilter($filter);
    }
    
    /**
     * Sets analyzer options.
     *
     * @param array(string=>mixed) $options The analyzer options.
     * 
     * @return void
     */
    public function setOptions(array $options = array())
    {
        $this->_options = $options;
    }
    
    /**
     * Should the parse ignore doc comment annotations?
     *
     * @return void
     */
    public function setWithoutAnnotations()
    {
        $this->_withoutAnnotations = true;
    }
    
    /**
     * Should PHP_Depend support projects with a bad documentation. If this
     * option is set to <b>true</b>, PHP_Depend will treat the default package
     * <b>+unknown</b> as a regular project package.
     *
     * @return void
     */
    public function setSupportBadDocumentation()
    {
        $this->_supportBadDocumentation = true;
    }
    
    /**
     * Adds a process listener.
     *
     * @param PHP_Depend_ProcessListenerI $listener The listener instance.
     * 
     * @return void
     */
    public function addProcessListener(PHP_Depend_ProcessListenerI $listener)
    {
        if (in_array($listener, $this->_listeners, true) === false) {
            $this->_listeners[] = $listener;
        }
    }
    
    /**
     * Analyzes the registered directories and returns the collection of 
     * analyzed packages.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function analyze()
    {
        if (count($this->directories) === 0) {
            throw new RuntimeException('No source directory set.');
        }
        
        $list   = $this->_createAnalyzerList();
        $loader = new PHP_Depend_Metrics_AnalyzerLoader($list, $this->_options);
        
        $reflection = new PHP_Reflection();
        if ($this->_withoutAnnotations === true) {
            $reflection->setWithoutAnnotations();
        }

        foreach ($this->directories as $directory) {
            $reflection->addInputSource($directory);
        }
        
        // Set valid file extensions
        if (count($this->_extensions) > 0) {
            $reflection->setExtensions($this->_extensions);
        }
        // Set exclude paths
        if (count($this->_excludeDirectories) > 0) {
            $reflection->setExcludePaths($this->_excludeDirectories);
        }
        
        $packages = $reflection->parse();
        
        // Initialize defaul filters
        if ($this->_supportBadDocumentation === false) {
            $filter = new PHP_Reflection_AST_Iterator_GlobalPackageFilter();
            $this->_codeFilter->addFilter($filter);
        }
        
        $filter = new PHP_Reflection_AST_Iterator_InternalPackageFilter();
        $this->_codeFilter->addFilter($filter);
        
        // Get global filter collection
        $staticFilter = PHP_Reflection_AST_Iterator_StaticFilter::getInstance();
        $staticFilter->addFilter($this->_codeFilter);
        
        if ($packages->count() === 0) {
            $message = "The parser doesn't detect package informations "
                     . "within the analyzed project, please check the "
                     . "documentation blocks for @package-annotations or use "
                     . "the --bad-documentation option.";
                     
            throw new RuntimeException($message);
        }
        
        $staticFilter->removeFilter($this->_codeFilter);

        // Append all listeners
        foreach ($loader as $analyzer) {
            foreach ($this->_listeners as $listener) {
                $analyzer->addAnalyzeListener($listener);
                
                if ($analyzer instanceof PHP_Reflection_VisitorI) {
                    $analyzer->addVisitListener($listener);
                }
            }
        }
        
        $this->fireStartAnalyzeProcess();
        
        foreach ($loader as $analyzer) {
            // Add filters if this analyzer is filter aware 
            if ($analyzer instanceof PHP_Depend_Metrics_FilterAwareI) {
                $staticFilter->addFilter($this->_codeFilter);
            }
            
            $analyzer->analyze($packages);
            
            // Remove filters if this analyzer is filter aware
            if ($analyzer instanceof PHP_Depend_Metrics_FilterAwareI) {
                $staticFilter->removeFilter($this->_codeFilter);
            }
            
            foreach ($this->loggers as $logger) {
                $logger->log($analyzer);
            }
        }
        
        $this->fireEndAnalyzeProcess();
        
        // Set global filter for logging
        $staticFilter->addFilter($this->_codeFilter);

        $this->fireStartLogProcess();

        foreach ($this->loggers as $logger) {
            // Check for code aware loggers
            if ($logger instanceof PHP_Depend_Log_CodeAwareI) {
                $logger->setCode($packages);
            }
            $logger->close();
        }
        
        $this->fireEndLogProcess();
        
        // Remove global filter
        // $staticFilter->removeFilter($this->_codeFilter);

        $this->packages = $packages;
        
        return $this->getPackages();
    }
    
    /**
     * Returns the number of analyzed php classes and interfaces.
     *
     * @return integer
     */
    public function countClasses()
    {
        if ($this->packages === null) {
            $msg = 'countClasses() doesn\'t work before the source was analyzed.';
            throw new RuntimeException($msg);
        }
        
        $classes = 0;
        foreach ($this->packages as $package) {
            $classes += $package->getTypes()->count();
        }
        return $classes;
    }
    
    /**
     *  Returns the number of analyzed packages.
     *
     * @return integer
     */
    public function countPackages()
    {
        if ($this->packages === null) {
            $msg = 'countPackages() doesn\'t work before the source was analyzed.';
            throw new RuntimeException($msg);
        }
        // TODO: This is internal knownhow, it is an ArrayIterator
        //       Replace it with a custom iterator interface
        return $this->packages->count();
    }
    
    /**
     * Returns the analyzed package of the specified name.
     *
     * @param string $name The package name.
     * 
     * @return PHP_Reflection_AST_Package
     */
    public function getPackage($name)
    {
        if ($this->packages === null) {
            $msg = 'getPackage() doesn\'t work before the source was analyzed.';
            throw new RuntimeException($msg);
        }
        foreach ($this->packages as $package) {
            if ($package->getName() === $name) {
                return $package;
            }
        }
        throw new OutOfBoundsException(sprintf('Unknown package "%s".', $name));
    }
    
    /**
     * Returns an iterator of the analyzed packages.
     *
     * @return Iterator
     */
    public function getPackages()
    {
        if ($this->packages === null) {
            $msg = 'getPackages() doesn\'t work before the source was analyzed.';
            throw new RuntimeException($msg);
        }
        // Rewind internal pointer
        $this->packages->rewind();

        return $this->packages;
    }
    
    /**
     * Send the start parsing process event.
     *
     * @param PHP_Reflection_BuilderI $builder The used node builder instance.
     * 
     * @return void
     */
    protected function fireStartParseProcess(PHP_Reflection_BuilderI $builder)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startParseProcess($builder);
        }
    }
    
    /**
     * Send the end parsing process event.
     *
     * @param PHP_Reflection_BuilderI $builder The used node builder instance.
     * 
     * @return void
     */
    protected function fireEndParseProcess(PHP_Reflection_BuilderI $builder)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endParseProcess($builder);
        }
    }
    
    /**
     * Sends the start file parsing event.
     *
     * @param PHP_Reflection_TokenizerI $tokenizer The used tokenizer instance.
     * 
     * @return void
     */
    protected function fireStartFileParsing(PHP_Reflection_TokenizerI $tokenizer)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startFileParsing($tokenizer);
        }
    }
    
    /**
     * Sends the end file parsing event.
     *
     * @param PHP_Reflection_TokenizerI $tokenizer The used tokenizer instance.
     * 
     * @return void
     */
    protected function fireEndFileParsing(PHP_Reflection_TokenizerI $tokenizer)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endFileParsing($tokenizer);
        }
    }
    
    /**
     * Sends the start analyzing process event.
     * 
     * @return void
     */
    protected function fireStartAnalyzeProcess()
    {
        foreach ($this->_listeners as $listener) {
            $listener->startAnalyzeProcess();
        }
    }
    
    /**
     * Sends the end analyzing process event.
     * 
     * @return void
     */
    protected function fireEndAnalyzeProcess()
    {
        foreach ($this->_listeners as $listener) {
            $listener->endAnalyzeProcess();
        }
    }
    
    /**
     * Sends the start log process event.
     * 
     * @return void
     */
    protected function fireStartLogProcess()
    {
        foreach ($this->_listeners as $listener) {
            $listener->startLogProcess();
        }
    }
    
    /**
     * Sends the end log process event.
     * 
     * @return void
     */
    protected function fireEndLogProcess()
    {
        foreach ($this->_listeners as $listener) {
            $listener->endLogProcess();
        }
    }
    
    /**
     * Creates an <b>array</b> with all expected analyzers. 
     *
     * @return array(string)
     */
    private function _createAnalyzerList()
    {
        $resultSets = array();
        
        foreach ($this->loggers as $logger) {
            foreach ($logger->getAcceptedAnalyzers() as $type) {
                // Check for type existence
                if (!in_array($type, $resultSets)) {
                    $resultSets[] = $type;
                }
            }
        }
        return $resultSets;
    }
}