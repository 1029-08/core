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
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage Core
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Core;

/**
 * Profiler
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Profiler extends \XLite\Base\Singleton implements \Doctrine\DBAL\Logging\SQLLogger
{
    const QUERY_LIMIT_TIMES = 2;
    const QUERY_LIMIT_DURATION = 0.05;

    const TRACE_BEGIN = 3;
    const TRACE_LENGTH = 8;

    const DEC_POINT     = '.';
    const THOUSANDS_SEP = ' ';


    /**
     * List of executed queries 
     * 
     * @var    array
     * @access protected
     * @since  3.0.0
     */
    protected static $queries = array();

    /**
     * List of memory measuring points 
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected static $memoryPoints = array();

    /**
     * Templates profiling enabled flag
     * 
     * @var    boolean
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected static $templatesProfilingEnabled = false;

    /**
     * Enabled flag
     * 
     * @var    boolean
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $enabled = false;

    /**
     * Start time
     * 
     * @var    float
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $start_time = null;

    /**
     * Stop time 
     * 
     * @var    float
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $stop_time = null;

    /**
     * Included files list
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $includedFiles = array();

    /**
     * Included files total size
     * 
     * @var    integer
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $includedFilesTotal = 0;

    /**
     * Included files count 
     * 
     * @var    integer
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $includedFilesCount = 0;

    /**
     * Last time 
     * 
     * @var    float
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $lastTime = 0;

    /**
     * Time points 
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $points = array();

    /**
     * Profiler should not start on these targets 
     * 
     * @var    array
     * @access protected
     * @since  3.0.0
     */
    protected $disallowedTargets = array(
        'image',
    );

    /**
     * Use xdebug stack trace 
     * 
     * @var    boolean
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected static $useXdebugStackTrace = false;

    /**
     * Current query 
     * 
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $currentQuery;

    /**
     * Check - templates profiling mode is enabled or not
     * 
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function isTemplatesProfilingEnabled()
    {
        return self::$templatesProfilingEnabled;
    }

    /**
     * There are some targets which are not require profiler
     * 
     * @return boolean 
     * @access protected
     * @since  3.0.0
     */
    protected function isTargetAllowed()
    {
        return !in_array(\XLite\Core\Request::getInstance()->target, $this->disallowedTargets);
    }

    /**
     * Getter
     * 
     * @param string $name Peroperty name
     *  
     * @return mixed
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function __get($name)
    {
        if ('enabled' == $name) {
            $result = $this->enabled;

        } elseif (isset($this->points[$name])) {

            $result = isset($this->points[$name]['end'])
                ? ($this->points[$name]['end'] - $this->points[$name]['start'])
                : 0;

        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * getStartupFlag 
     * 
     * @return boolean 
     * @access protected
     * @since  3.0.0
     */
    protected function getStartupFlag()
    {
        return \XLite::getInstance()->getOptions(array('profiler_details', 'enabled'))
            && $this->isTargetAllowed()
            && !\XLite\Core\Request::getInstance()->isPost()
            && !\XLite\Core\Request::getInstance()->isCLI()
            && !\XLite\Core\Request::getInstance()->isAJAX();
    }

    /**
     * Constructor
     * 
     * @return void
     * @access public
     * @since  3.0.0
     */
    public function __construct()
    {
        $this->start($this->getStartupFlag());
    }

    /**
     * Destructor
     *
     * @return void
     * @access public
     * @since  3.0.0
     */
    public function __destruct()
    {
        $this->stop();
    }

    /**
     * Log same time range
     * 
     * @param string  $timePoint  Time range name
     * @param boolean $additional Additional metric flag OPTIONAL
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function log($timePoint, $additional = false)
    {
        if (!isset($this->points[$timePoint])) {
            $this->points[$timePoint] = array(
                'start' => microtime(true),
                'open'  => true,
                'time'  => 0,
            );

            if (self::$useXdebugStackTrace) {
                xdebug_start_trace(
                    LC_VAR_DIR . 'log' . LC_DS . $timePoint . '.' . microtime(true),
                    XDEBUG_TRACE_COMPUTERIZED
                );
            }

        } elseif ($this->points[$timePoint]['open']) {

            $range = microtime(true) - $this->points[$timePoint]['start'];
            if ($additional) {
                $this->points[$timePoint]['time'] += $range;
            } else {
                $this->points[$timePoint]['time'] = $range;
            }
            $this->points[$timePoint]['open'] = false;

            if (self::$useXdebugStackTrace) {
                @xdebug_stop_trace();
            }

        } else {

            $this->points[$timePoint]['start'] = microtime(true);
            $this->points[$timePoint]['open'] = true;

            if (self::$useXdebugStackTrace) {
                xdebug_start_trace(
                    LC_VAR_DIR . 'log' . LC_DS . $timePoint . '.' . microtime(true),
                    XDEBUG_TRACE_COMPUTERIZED
                );
            }

        }
    }
    
    /**
     * Start profiler
     * 
     * @param boolean $start Enable flag
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function start($start)
    {
        $this->enabled = !empty($start);
        $this->start_time = $_SERVER['REQUEST_TIME'];
        self::$templatesProfilingEnabled = $this->enabled
            && \XLite::getInstance()->getOptions(array('profiler_details', 'process_widgets'));

        self::$useXdebugStackTrace = function_exists('xdebug_start_trace')
            && \XLite::getInstance()->getOptions(array('profiler_details', 'xdebug_log_trace'));
    }
    
    /**
     * Stop profiler
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function stop()
    {
        if ($this->enabled && !\XLite\Core\Request::getInstance()->isPopup) {

            $this->stop_time = microtime(true);

            $this->includedFiles = array();
            $this->includedFilesTotal = 0;

            foreach (get_included_files() as $file) {
                $size = intval(@filesize($file));
                $this->includedFiles[] = array(
                    'name' => $file,
                    'size' => $size
                );
                $this->includedFilesTotal += $size;
            }
            $this->includedFilesCount = count($this->includedFiles);

            usort($this->includedFiles, array($this, 'sortCallback'));

            $this->display();
        }
    }
    
    /**
     * Display profiler report
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function display()
    {
        $totalQueriesTime = 0;
        foreach (self::$queries as $q => $d) {
            $cnt = count($d['time']);
            $sum = array_sum($d['time']);
            self::$queries[$q] = array(
                'count' => $cnt,
                'max'   => empty($d['time']) ? 0 : max($d['time']),
                'min'   => empty($d['time']) ? 0 : min($d['time']),
                'avg'   => (0 < $cnt) ? $sum / $cnt : 0,
                'trace' => $d['trace'],
            );
            $totalQueriesTime += $sum;
        }

        $execTime = number_format($this->stop_time - $this->start_time, 4, self::DEC_POINT, self::THOUSANDS_SEP);
        $memoryPeak = round(memory_get_peak_usage() / 1024 / 1024, 3);
        $totalQueries = count(self::$queries);
        $totalQueriesTime = number_format($totalQueriesTime, 4, self::DEC_POINT, self::THOUSANDS_SEP);
        $dbConnectTime = number_format($this->dbConnectTime, 4, self::DEC_POINT, self::THOUSANDS_SEP);
        $unitOfWorkSize = \XLite\Core\Database::getEM()->getUnitOfWork()->size();

        $this->includedFilesTotal = round($this->includedFilesTotal / 1024, 3);

        $html = <<<HTML
<table cellspacing="0" cellpadding="3" style="width: auto;">
    <tr>
        <td><strong>Execution time</strong></td>
        <td>$execTime</td>
    </tr>
    <tr>
        <td><strong>Memory usage (peak)</strong></td>
        <td>$memoryPeak Mb</td>
    </tr>
    <tr>
        <td><strong>SQL queries count</strong></td>
        <td>$totalQueries</td>
    </tr>
    <tr>
        <td><strong>SQL queries duration</strong></td>
        <td>$totalQueriesTime sec.</td>
    </tr>

    <tr>
        <td><strong>Included files count</strong></td>
        <td>$this->includedFilesCount</td>
    </tr>

    <tr> 
        <td><strong>Included files total size</strong></td>
        <td>$this->includedFilesTotal Kb.</td>
    </tr>

    <tr>
        <td><strong>Database connect time</strong></td>
        <td>$dbConnectTime sec.</td>
    </tr>

    <tr>
        <td><strong>Doctrine UnitOfWork final size</strong></td>
        <td>$unitOfWorkSize models</td>
    </tr>

</table>

<table cellspacing="0" cellpadding="3" border="1" style="width: auto;">
    <caption style="font-weight: bold; text-align: left;">Queries log</caption>
    <tr>
        <th>Times</th>
        <th>Max. duration, sec.</th>
        <th>Query</th>
    </tr>
HTML;

        echo ($html);

        $warnStyle = ' background-color: red; font-weight: bold;';

        foreach (self::$queries as $query => $d) {
            $timesLimit = (self::QUERY_LIMIT_TIMES < $d['count'] ? $warnStyle : '');
            $durationLimit = (self::QUERY_LIMIT_DURATION < $d['max'] ? $warnStyle : '');

            echo (
                '<tr>' . "\n"
                . '<td style="vertical-align: top;' . $timesLimit . '">'
                . $d['count']
                . '</td>'
                . '<td style="vertical-align: top;' . $durationLimit . '">'
                . number_format($d['max'], 4, self::DEC_POINT, self::THOUSANDS_SEP)
                . '</td><td style="white-space: nowrap;">'
                . $query . '<br />'
                . implode(' << ', $d['trace'])
                . '</td></tr>' . "\n"
            );
        }

        echo ('</table>');

        if (self::$memoryPoints) {
            $html = <<<HTML
<table cellspacing="0" cellpadding="3" border="1" style="width: auto;">
    <caption style="font-weight: bold; text-align: left;">Memory points</caption>
    <tr>
        <th nowrap="nowrap">Memory, Mbytes</th>
        <th nowrap="nowrap">Changes, Mbytes</th>
        <th>Back trace</th>
    </tr>
HTML;
            echo ($html);

            $lastMem = 0;
            foreach (self::$memoryPoints as $d) {
                $diff = $d['memory'] - $lastMem;
                $m = number_format(round($d['memory'] / 1024 / 1024, 3), 3, self::DEC_POINT, self::THOUSANDS_SEP);
                $md = number_format(round($diff / 1024 / 1024, 3), 3, self::DEC_POINT, self::THOUSANDS_SEP);
                echo (
                    '<tr>'
                    . '<td>' . $m . '</td>'
                    . '<td>' . $md . '</td>'
                    . '<td>' . implode(' << ', $d['trace']) . '</td>'
                    . '</tr>'
                );
                $lastMem = $d['memory'];
            }

            echo ('</table>');
        }

        if ($this->points) {
            $html = <<<HTML
<table cellspacing="0" cellpadding="3" border="1" style="width: auto;">
    <caption style="font-weight: bold; text-align: left;">Log points</caption>
    <tr>
        <th>Duration, sec.</th>
        <th>Point name</th>
    </tr>
HTML;
            echo ($html);

            foreach ($this->points as $name => $d) {
                echo (
                    '<tr><td>'
                    . number_format($d['time'], 4, self::DEC_POINT, self::THOUSANDS_SEP)
                    . '</td><td>'
                    . $name
                    . '</td></tr>'
                );
            }

            echo ('</table>');
        }
    }

    /**
     * Included files statistics sorting callback
     * 
     * @param array $a File info 1
     * @param array $b File info 2
     *  
     * @return integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function sortCallback($a, $b)
    {
        $result = 0;

        if ($a['size'] != $b['size']) {
            $result = $a['size'] < $b['size'] ? 1 : -1;
        }

        return $result;
    }

    /**
     * Log SQL queries
     * 
     * @param string $sql    Query
     * @param array  $params Query arguments
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function logSQL($sql, array $params = null)
    {
        $this->addQuery($sql);
    }

    /**
     * Add query to log
     * 
     * @param string $query Query
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function addQuery($query)
    {
        $this->lastTime = microtime(true);

        // Uncomment if you want to truncate queries
        /* if (strlen($query)>300) {
            $query = substr($query, 0, 300) . ' ...';
            
        } */

        if (!isset(self::$queries[$query])) {
            self::$queries[$query] = array(
                'time' => array(),
                'trace' => $this->getBackTrace(),
            );
            $this->addMemoryPoint();
        }
    }

    /**
     * Set query time 
     * 
     * @param string $query Query
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function setQueryTime($query)
    {
        if (isset(self::$queries[$query])) {
            self::$queries[$query]['time'][] = microtime(true) - $this->lastTime;
        }
    }

    /**
     * Add memory measure point 
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function addMemoryPoint()
    {
        self::$memoryPoints[] = array(
            'memory' => memory_get_usage(),
            'trace' => $this->getBackTrace(),
        );
    }

    /**
     * Get back trace 
     * 
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getBackTrace()
    {
        $trace = array();

        foreach (debug_backtrace(false) as $l) {
            if (isset($l['file']) && isset($l['line'])) {
                $trace[] = str_replace(
                    array(LC_COMPILE_DIR, LC_ROOT_DIR),
                    array('', ''),
                    $l['file']
                ) . ':' . $l['line'];

            } elseif (isset($l['function']) && isset($l['line'])) {
                $trace[] = $l['function'] . '():' . $l['line'];
            }
        }

        return array_slice($trace, self::TRACE_BEGIN, self::TRACE_LENGTH);
    }

    /**
     * Logs a SQL statement somewhere
     * 
     * @param string $sql    The SQL to be executed
     * @param array  $params The SQL parameter
     * @param array  $types  The SQL parameter types
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->addQuery($sql);
        $this->currentQuery = $sql;
    }

    /**
     * Mark the last started query as stopped. This can be used for timing of queries 
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function stopQuery()
    {
        $this->setQueryTime($this->currentQuery);
    }
}
