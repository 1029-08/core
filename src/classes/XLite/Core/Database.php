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
 * @subpackage Model
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Core;

/**
 * Database
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Database extends \XLite\Base\Singleton
{
    /**
     * Schema generation modes 
     */
    const SCHEMA_CREATE = 'create';
    const SCHEMA_UPDATE = 'update';
    const SCHEMA_DELETE = 'delete';


    /**
     * DB schema file ident
     */
    const SCHEMA_FILE_IDENT = '  ';

    /**
     * Doctrine entity manager 
     * 
     * @var    \Doctrine\ORM\EntityManager
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected static $em = null;

    /**
     * Doctrine cache driver 
     * 
     * @var    \Doctrine\Common\Cache\AbtractCache
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected static $cacheDriver = null;

    /**
     * Cache drivers query 
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected static $cacheDriversQuery = array(
        'apc',
        'xcache',
        'memcache',
    );

    /**
     * Doctrine config object
     * 
     * @var    \Doctrine\ORM\Configuration
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $configuration;

    /**
     * Table prefix 
     * 
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $tablePrefix = '';

    /**
     * connected 
     * 
     * @var    bool
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $connected;

    /**
     * Doctrine unmanaged table names list
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $unmanagedTables = array();

    /**
     * Forbid truncate tables if will truncate store-based tables
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $forbidTruncateTablesStore = array(
        'profiles',
        'currencies',
        'payment_methods',
        'shipping_methods',
        'memberships',
    );

    /**
     * Fixtures loading prtocedure options 
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $fixturesLoadingOptions = array(
        'insert'   => false,
        'addModel' => null,
    );

    /**
     * Get entity manager 
     * 
     * @return \Doctrine\ORM\EntityManager
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getEM()
    {
        // FIXME
        if (!isset(static::$em)) {
            \XLite\Core\Database::getInstance();
        }

        return static::$em;
    }

    /**
     * Get repository (short method)
     *
     * @param string $repository Entity class name
     * 
     * @return \Doctrine\ORM\EntityRepository
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getRepo($repository)
    {
        return static::getEM()->getRepository(ltrim($repository, '\\'));
    }

    /**
     * Get cache driver by options list
     * 
     * @param mixed $options Options from config.ini
     *  
     * @return \Doctrine\Common\Cache\Cache
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getCacheDriverByOptions($options)
    {
        if (!isset($options) || !is_array($options) || !isset($options['type'])) {
            $options = array('type' => null);
        }

        // Auto-detection
        if ('auto' == $options['type']) {
            foreach (static::$cacheDriversQuery as $type) {
                $method = 'detectCacheDriver' . ucfirst($type);

                // $method assembled from 'detectCacheDriver' + $type
                if (static::$method()) {
                    $options['type'] = $type;
                    break;
                }
            }
        }

        if ('apc' == $options['type']) {

            // APC
            $cache = new \Doctrine\Common\Cache\ApcCache;

        } elseif ('memcache' == $options['type'] && isset($options['servers']) && class_exists('Memcache', false)) {

            // Memcache
            $servers = explode(';', $options['servers']);
            if ($servers) {
                $memcache = new \Memcache();
                foreach ($servers as $row) {
                    $row = trim($row);
                    $tmp = explode(':', $row, 2);
                    if ('unix' == $tmp[0]) {
                        $memcache->addServer($row, 0);

                    } elseif (isset($tmp[1])) {
                        $memcache->addServer($tmp[0], $tmp[1]);

                    } else {
                        $memcache->addServer($tmp[0]);
                    }
                }

                $cache = new \Doctrine\Common\Cache\MemcacheCache;
                $cache->setMemcache($memcache);
            }

        } elseif ('xcache' == $options['type']) {

            $cache = new \Doctrine\Common\Cache\XcacheCache;

        } else {

            // Default cache - file system cache
            $cache = new \XLite\Core\FileCache(LC_DATACACHE_DIR);

        }

        if (isset($options['namespace']) && $options['namespace']) {
            // TODO - namespace temporary is empty - bug into Doctrine\Common\Cache\AbstractCache::deleteByPrefix()
            //$cache->setNamespace($options['namespace']);
        }

        return $cache;
    }

    /**
     * Constructor
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function __construct()
    {
        if (!$this->connected) {
            $this->connect();
        }
    }

    /**
     * Connect and set-up Doctrine
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function connect()
    {
        $this->configuration = new \Doctrine\ORM\Configuration;

        // Setup cache
        $this->setDoctrineCache();

        // Set metadata driver
        $chain = new \Doctrine\ORM\Mapping\Driver\DriverChain();
        $chain->addDriver(
            $this->configuration->newDefaultAnnotationDriver(LC_MODEL_CACHE_DIR),
            'XLite\Model'
        );

        $iterator = new \RecursiveDirectoryIterator(
            LC_CLASSES_CACHE_DIR . 'XLite' . LC_DS . 'Module',
            \FilesystemIterator::SKIP_DOTS
        );

        foreach ($iterator as $dir) {

            if (
                \Includes\Utils\FileManager::isDir($dir->getPathName())
            ) {
                $iterator2 = new \RecursiveDirectoryIterator(
                    $dir->getPathName(),
                    \FilesystemIterator::SKIP_DOTS
                );
     
                foreach ($iterator2 as $dir2) {
                    if (
                        \Includes\Utils\FileManager::isDir($dir2->getPathName())
                        && \Includes\Utils\FileManager::isDir($dir2->getPathName() . LC_DS . 'Model')
                    ) {
                        $chain->addDriver(
                            $this->configuration->newDefaultAnnotationDriver($dir2->getPathName() . LC_DS . 'Model'),
                            'XLite\Module\\' . $dir->getBaseName() . '\\' . $dir2->getBaseName() . '\Model'
                        );
                    }
                }
            }
        }


        $this->configuration->setMetadataDriverImpl($chain);

        // Set proxy settings
        $this->configuration->setProxyDir(LC_PROXY_CACHE_DIR);
        $this->configuration->setProxyNamespace(LC_MODEL_PROXY_NS);
        $this->configuration->setAutoGenerateProxyClasses(false);

        $this->tablePrefix = \XLite::getInstance()->getOptions(array('database_details', 'table_prefix'));

        // Initialize DB connection and entity manager
        $this->startEntityManager();
    }

    /**
     * Start Doctrine entity manager 
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function startEntityManager()
    {
        // Initialize DB connection and entity manager
        self::$em = \Doctrine\ORM\EntityManager::create($this->getDSN(), $this->configuration);

        if (\XLite\Core\Profiler::getInstance()->enabled) {
            self::$em->getConnection()->getConfiguration()->setSQLLogger(\XLite\Core\Profiler::getInstance());
        }

        static::registerCustomTypes(self::$em);

        // Bind events
        $events = array(\Doctrine\ORM\Events::loadClassMetadata);
        if (self::$cacheDriver) {

            // Bind cache chekers
            $events[] = \Doctrine\ORM\Events::postPersist;
            $events[] = \Doctrine\ORM\Events::postUpdate;
            $events[] = \Doctrine\ORM\Events::postRemove;
        }

        self::$em->getEventManager()->addEventListener($events, $this);
    }

    /**
     * Register custom types 
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function registerCustomTypes(\Doctrine\ORM\EntityManager $em)
    {
        // Fixed string
        if (!\Doctrine\DBAL\Types\Type::hasType('fixedstring')) {
            \Doctrine\DBAL\Types\Type::addType('fixedstring', 'XLite\Core\ColumnType\FixedString');
        }
        $em->getConnection()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('char', 'fixedstring');

        // Unsigned integer
        if (!\Doctrine\DBAL\Types\Type::hasType('uinteger')) {
            \Doctrine\DBAL\Types\Type::addType('uinteger', 'XLite\Core\ColumnType\Uinteger');
        }
        $em->getConnection()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('int', 'uinteger');

    }

    /**
     * Export DB schema 
     * 
     * @param string $path Export directory path
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function exportDBSchema($path = null)
    {
        if (!$path) {
            $path = LC_VAR_DIR;
        }

        $metadata = $this->getAllMetadata();

        $tool = new \Doctrine\ORM\Tools\SchemaTool(self::$em);
        $tool->createSchema($metadata);
        file_put_contents($path . LC_DS . 'schema.sql');

        $cme = new \Doctrine\ORM\Tools\Export\ClassMetadataExporter();
        $exporter = $cme->getExporter('yml', $path);
        $exporter->setMetadata($metadata);
        $exporter->export();
    }

    /**
     * Check - DB is empty or not
     * 
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function isDBEmpty()
    {
        return 0 == count(self::$em->getConnection()->getSchemaManager()->listTableNames());
    }

    /**
     * Create / update DB schema 
     * 
     * @return integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function updateDBSchema()
    {
        $schema = $this->isDBEmpty()
            ? $this->getDBSchema(self::SCHEMA_CREATE)
            : $this->getDBSchema(self::SCHEMA_UPDATE);

        return $this->executeQueries($schema);
    }

    /**
     * Drop DB schema 
     * 
     * @return integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function dropDBSchema()
    {
        return $this->executeQueries($this->getDBSchema(self::SCHEMA_DELETE));
    }

    /**
     * Execute queries list
     * 
     * @param array $queries Queries list
     *  
     * @return integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function executeQueries(array $queries)
    {
        $i = 0;
        $connection = self::$em->getConnection();
        foreach ($queries as $sql) {
            $connection->executeQuery($sql);
            $i++;
        }

        return $i;
    }

    /**
     * Get DB schema as file
     *
     * @param string $mode Schema generation mode OPTIONAL
     *
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getDBSchemaAsFile($mode = self::SCHEMA_CREATE)
    {
        return implode(';' . PHP_EOL, $this->getDBSchema($mode)) . ';' . PHP_EOL;
    }

    /**
     * Get DB schema
     *
     * @param string $mode Schema generation mode OPTIONAL
     * 
     * @return array(string)
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getDBSchema($mode = self::SCHEMA_CREATE)
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool(self::$em);
        $schemas = array();

        $rawSchemas = null;
        $postprocessMethod = null;

        if (self::SCHEMA_CREATE == $mode) {
            $rawSchemas = $tool->getCreateSchemaSql($this->getAllMetadata());
            $postprocessMethod = 'postprocessCreateSchema';

        } elseif (self::SCHEMA_UPDATE == $mode) {
            $rawSchemas = $tool->getUpdateSchemaSql($this->getAllMetadata());
            $postprocessMethod = 'postprocessUpdateSchema';

        } elseif (self::SCHEMA_DELETE == $mode) {
            $rawSchemas = $tool->getDropSchemaSql($this->getAllMetadata());
            $postprocessMethod = 'postprocessDropSchema';
        }

        if ($rawSchemas) {
            foreach ($rawSchemas as $schema) {

                // $postprocessMethod detected by $mode
                $schema = $this->$postprocessMethod($schema);

                if (is_array($schema)) {
                    $schemas = array_merge($schemas, $schema);

                } elseif (isset($schema)) {
                    $schemas[] = $schema;
                }
            }

            foreach (self::$em->getMetadataFactory()->getAllMetadata() as $cmd) {
                if (!$cmd->isMappedSuperclass) {
                    $schemas = static::getRepo($cmd->name)->processSchema($schemas, $mode);
                }
            }
        }

        return $schemas;
    }

    /**
     * Postprocess creation schema 
     * 
     * @param string $schema Schema
     *  
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function postprocessCreateSchema($schema)
    {
        if (preg_match('/^CREATE TABLE /Ss', $schema)) {
            $schema = $this->postprocessCreateSchemaTable($schema);

        } elseif (preg_match('/^ALTER TABLE /Ss', $schema)) {
            $schema = $this->postprocessAlterSchemaTable($schema);
        }

        return $schema;
    }

    /**
     * Postprocess table creation schema line
     *
     * @param string $schema Schema
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function postprocessCreateSchemaTable($schema)
    {
        preg_match('/CREATE TABLE (\S+) /Ss', $schema, $m);
        $tableName = $m[1];

        $schema = preg_replace(
            '/CREATE TABLE (\S+) \((.+)(\) ENGINE = \w+)/Sse',
            '\'CREATE TABLE `$1` (\' . PHP_EOL'
            . ' . \'$2\' . PHP_EOL'
            . ' . \'$3 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci\'',
            $schema
        );

        $schema = explode(PHP_EOL, $schema);

        $str = $schema[1];

        $str = preg_replace('/numeric\((\d+), (\d+)\)/Ssi', 'NUMERIC($1,$2)', $str);
        $str = preg_replace('/ index (\S+) \(([\w_, ]+)\)/Ssie', '\' INDEX $1 (\' . str_replace(\', \', \',\', \'$2\') . \')\'', $str);

        $id = null;
        if (preg_match('/PRIMARY KEY *\(([^)]+)\)/Ssi', $str, $m)) {
            $id = trim($m[1]);
        }

        if ($id) {
            $str = preg_replace('/UNIQUE INDEX \S+ \(' . $id . '\),/Ssi', '', $str);
            $str = str_replace(PHP_EOL . PHP_EOL, PHP_EOL, $str);
        }

        $str = preg_replace('/PRIMARY KEY\(([^)]+)\)/Ssi', 'PRIMARY KEY ($1)', $str);

        $parts = array_map('trim', explode(', ', $str));
        foreach ($parts as $k => $v) {
            $v = preg_replace('/^((?:UNIQUE )?INDEX) (\S+) \(/Ss', '$1 `$2` (', $v);

            $v = preg_replace('/^(INDEX \S+ \()([^,)]+)/Ss', '$1`$2`', $v);
            $v = preg_replace('/^(INDEX \S+ \(.+,)([^`,)]+)/Ss', '$1`$2`', $v);

            $v = preg_replace('/^(PRIMARY KEY \()([^,\)]+)/Ss', '$1`$2`', $v);
            $v = preg_replace('/^(PRIMARY KEY \(.+,)([^`,\)]+)/Ss', '$1`$2`', $v);

            $v = self::SCHEMA_FILE_IDENT . preg_replace('/^([a-z][\w\d_]+) ([A-Z]+)/Ss', '`$1` $2', $v);

            $parts[$k] = $v;
        }

        $schema[1] = implode(',' . PHP_EOL, $parts);

        return array(
            'DROP TABLE IF EXISTS `' . $tableName . '`',
            implode(PHP_EOL, $schema),
        );
    }

    /**
     * Postprocess table altering schema line
     *
     * @param string $schema Schema
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function postprocessAlterSchemaTable($schema)
    {
        $schema = preg_replace('/ALTER TABLE (\S+) /USs', 'ALTER TABLE `$1` ', $schema);

        $schema = preg_replace('/( FOREIGN KEY \()([^,)]+)/Ss', '$1`$2`', $schema);
        $schema = preg_replace('/( FOREIGN KEY \(.+,)([^`,)]+)/Ss', '$1`$2`', $schema);

        $schema = preg_replace('/ REFERENCES (\S+) *\(([^,)]+)/Ss', ' REFERENCES `$1` (`$2`', $schema);
        $schema = preg_replace('/( REFERENCES \S+ \(.+,)([^`,)]+)/Ss', '$1`$2`', $schema);

        $schema = preg_replace('/ (DROP(?: FOREIGN KEY)?) ([a-z][^\s,`]+)(,)? /Ss', ' $1 `$2`$3 ', $schema);
        $schema = preg_replace('/ CHANGE ([a-z]\S+) (\S+) /Ss', ' CHANGE `$1` `$2` ', $schema);
        $schema = preg_replace('/ ADD ([a-z]\S+) /Ss', ' ADD `$1` ', $schema);

        return $schema;
    }

    /**
     * Postprocess update schema
     *
     * @param string $schema Schema
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function postprocessUpdateSchema($schema)
    {
        if (preg_match('/^CREATE TABLE /Ss', $schema)) {
            $schema = $this->postprocessCreateSchemaTable($schema);

        } elseif (preg_match('/^ALTER TABLE /Ss', $schema)) {
            $schema = $this->postprocessAlterSchemaTable($schema);

        } elseif (preg_match('/DROP TABLE /Ss', $schema)) {

            if (preg_match('/^DROP TABLE ' . $this->tablePrefix . '(?:' .implode('|', $this->unmanagedTables) . ')$/Ss', $schema)) {
                $schema = null;

            } else {
                $schema = preg_replace('/^DROP TABLE (\S+)/Ss', 'DROP TABLE IF EXISTS `$1`', $schema);
            }

        } else {

            $schema = preg_replace('/^(CREATE (?:UNIQUE )?INDEX) (\S+) ON ([^\s(]+)/Ss', '$1 `$2` ON `$3`', $schema);
            $schema = preg_replace('/^(CREATE (?:UNIQUE )?INDEX [^(]+ \()([^,)]+)/Ss', '$1`$2`', $schema);
            $schema = preg_replace('/^(CREATE (?:UNIQUE )?INDEX [^(]+ \(.+, *)([^`,)]+)/Ss', '$1`$2`', $schema);

            $schema = preg_replace('/^DROP INDEX (\S+) ON (\S+)/Ss', 'DROP INDEX `$1` ON `$2`', $schema);
        }

        return $schema;
    }

    /**
     * Postprocess drop schema
     *
     * @param string $schema Schema
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function postprocessDropSchema($schema)
    {
        $schema = preg_replace('/^ALTER TABLE (\S+) DROP FOREIGN KEY (\S+)/Ss', 'ALTER TABLE `$1` DROP FOREIGN KEY `$2`', $schema);
        $schema = preg_replace('/^DROP TABLE (\S+)/Ss', 'DROP TABLE IF EXISTS `$1`', $schema);

        return $schema;
    }

    /**
     * Get all metadata 
     * 
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getAllMetadata()
    {
        $entities = array();
        foreach (self::$em->getMetadataFactory()->getAllMetadata() as $md) {
            if (!$md->isMappedSuperclass) {
                $entities[] = $md;
            }
        }

        return $entities;
    }

    /**
     * Get fixtures loading procedure option 
     *
     * @param string $name  Option name
     * 
     * @return mixed
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getFixturesLoadingOption($name)
    {
        return isset($this->fixturesLoadingOptions[$name]) ? $this->fixturesLoadingOptions[$name] : null;
    }

    /**
     * Set fixtures loading procedure option
     *
     * @param string $name  Option name
     * @param mixed  $value Option value OPTIONAL
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function setFixturesLoadingOption($name, $value = null)
    {
        $this->fixturesLoadingOptions[$name] = $value;
    }

    /**
     * Load fixtures from YAML file 
     * 
     * @param string $path YAML file path
     *  
     * @return boolean|integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function loadFixturesFromYaml($path)
    {
        $data = \Symfony\Component\Yaml\Yaml::load($path);

        $result = false;

        if (is_array($data)) {
            $result = 0;
            foreach ($data as $entityName => $rows) {
                $repo = static::getRepo($entityName);

                if ($repo) {
                    $rows = $this->detectDirectives($rows);

                    $result += $repo->loadFixtures($rows);

                    static::$em->flush();
                    static::$em->clear();

                    $this->resetDirectives();
                }
            }
        }

        return $result;
    }

    /**
     * Unload fixtures from YAML file 
     * 
     * @param string $path YAML file path
     *  
     * @return boolean|integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function unloadFixturesFromYaml($path)
    {
        $data = \Symfony\Component\Yaml\Yaml::load($path);

        $result = false;

        if (is_array($data)) {
            $result = 0;
            foreach ($data as $entityName => $rows) {
                $repo = static::getRepo($entityName);

                if ($repo) {
                    $rows = $this->detectDirectives($rows);

                    $result += $repo->unloadFixtures($rows);

                    static::$em->flush();
                    static::$em->clear();

                    $this->resetDirectives();
                }
            }
        }

        return $result;
    }


    /**
     * Detect fixtures loading directives 
     * 
     * @param array $rows Entity fixtures
     *  
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function detectDirectives(array $rows)
    {
        if (isset($rows['directives'])) {
            $this->fixturesLoadingOptions['insert'] = isset($rows['directives']['insert'])
                && $rows['directives']['insert'];
            if (isset($rows['directives']['addModel'])) {
                $this->fixturesLoadingOptions['addModel'] = $rows['directives']['addModel'];
            }
            unset($rows['directives']);
        }

        return $rows;
    }

    /**
     * Reset fixtures loading directives 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function resetDirectives()
    {
        $this->fixturesLoadingOptions['insert'] = false;
        $this->fixturesLoadingOptions['addModel'] = null;
    }

    /**
     * Setup doctrine cache 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function setDoctrineCache()
    {
        self::$cacheDriver = self::getCacheDriverByOptions(\XLite::getInstance()->getOptions('cache'));

        $this->configuration->setMetadataCacheImpl(self::$cacheDriver);
        $this->configuration->setQueryCacheImpl(self::$cacheDriver);
        $this->configuration->setResultCacheImpl(self::$cacheDriver);
    }

    /**
     * Detect APC cache driver
     * 
     * @return boolean
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected static function detectCacheDriverApc()
    {
        return function_exists('apc_cache_info');
    }

    /**
     * Detect XCache cache driver
     * 
     * @return boolean
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected static function detectCacheDriverXcache()
    {
        return function_exists('xcache_get');
    }

    /**
     * Detect Memcache cache driver
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected static function detectCacheDriverMemcache()
    {
        return function_exists('memcache_connect');
    }

    /**
     * Get DSN in Doctrine style
     * 
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getDSN()
    {
        $options = \XLite::getInstance()->getOptions('database_details');

        $dsnFields = array(
            'host'        => 'hostspec',
            'port'        => 'port',
            'unix_socket' => 'socket',
            'dbname'      => 'database',
        );
        $dsnList = array(
            'driver'       => 'pdo_mysql',
            'wrapperClass' => '\XLite\Core\Connection',
        );
        $dsnString = array();

        foreach ($dsnFields as $pdoOption => $lcOption) {

            if (!empty($options[$lcOption])) {
                $dsnList[$pdoOption] = $options[$lcOption];
                $dsnString[] = $pdoOption . '=' . $options[$lcOption];
            }
        }

        $dsnList['path'] = 'mysql:' . implode(';', $dsnString);
        $dsnList['user'] = $options['username'];
        $dsnList['password'] = $options['password'];

        if ('pdo_mysql' == $dsnList['driver']) {
            $dsnList['driverClass'] = '\XLite\Core\PDOMySqlDriver';
        }

        return $dsnList;
    }

    /**
     * Get cache driver
     *
     * @return \Doctrine\Common\Cache\AbstractCache
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getCacheDriver()
    {
        return self::$cacheDriver;
    }

    /**
     * Get table prefix 
     * 
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * postPersist event handler
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $arg Event argument
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function postPersist(\Doctrine\ORM\Event\LifecycleEventArgs $arg)
    {
        $arg->getEntity()->checkCache();
    }

    /**
     * postUpdate event handler
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $arg Event argument
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function postUpdate(\Doctrine\ORM\Event\LifecycleEventArgs $arg)
    {
        $arg->getEntity()->checkCache();
    }
    /**
     * postRemove event handler
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $arg Event argument
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function postRemove(\Doctrine\ORM\Event\LifecycleEventArgs $arg)
    {
        $arg->getEntity()->checkCache();
    }

    /**
     * loadClassMetadata event handler
     * 
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs Event arguments
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();

        // Set table name prefix
        if ($this->tablePrefix && strpos($classMetadata->getTableName(), $this->tablePrefix) !== 0) {
            $classMetadata->setTableName($this->tablePrefix . $classMetadata->getTableName());
        }

        // Set repository
        if (!$classMetadata->customRepositoryClassName) {
            $classMetadata->setCustomRepositoryClass(
                $this->detectCustomRepositoryClassName($classMetadata->getReflectionClass()->getName())
            );
        }
    }

    /**
     * Get disabled structures
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getDisabledStructures()
    {
        $path = $this->getDisabledStructuresPath();
        $tables = array();
        $columns = array();

        if (file_exists($path)) {
            $data = file_get_contents($path);
            $data = substr($data, strlen($this->getServiceHeader()));
            foreach (\Symfony\Component\Yaml\Yaml::load($data) as $module => $list) {
                if (isset($list['tables']) && is_array($list['tables'])) {
                    $tables = array_merge($tables, $list['tables']);
                }
                if (isset($list['columns']) && is_array($list['columns'])) {
                    $columns = array_merge($columns, $list['columns']);
                }
            }
        }

        return array($tables, $columns);
    }

    /**
     * Set disabled tables list
     * 
     * @param string $module     Module unique name
     * @param array  $structures Disabled structures
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function setDisabledStructures($module, array $structures = array())
    {
        $path = $this->getDisabledStructuresPath();

        $data = array();

        if (file_exists($path)) {
            $data = file_get_contents($path);
            $data = substr($data, strlen($this->getServiceHeader()));
            $data = \Symfony\Component\Yaml\Yaml::load($data);
        }

        if (!$structures || (!$structures[0] && !$structures[1])) {
            unset($data[$module]);

        } else {
            $data[$module] = array(
                'tables'  => $structures[0],
                'columns' => $structures[1],
            );
        }

        if ($data) {
            file_put_contents(
                $path,
                $this->getServiceHeader() . \Symfony\Component\Yaml\Yaml::dump($data)
            );

        } elseif (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Get disabled tables list storage path 
     * 
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getDisabledStructuresPath()
    {
        return LC_VAR_DIR . '.disabled.structures.php';
    }

    /**
     * Get data storage service header 
     * 
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getServiceHeader()
    {
        return '<' . '?php die(); ?' . '>' . "\n";
    }

    /**
     * Detect custom repository class name by entity class name
     * 
     * @param string $entityClass Entity class name
     *  
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function detectCustomRepositoryClassName($entityClass)
    {
        $class = str_replace('\Model\\', '\Model\Repo\\', $entityClass);

        if (!\XLite\Core\Operator::isClassExists($class)) {
            if (preg_match('/\wTranslation$/Ss', $entityClass)) {
                $class = '\XLite\Model\Repo\Base\Translation';

            } else {
                $class = '\XLite\Model\Repo\Base\Common';
            }
        }

        return $class;
    }

    /**
     * Get last query length
     *
     * @return integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getLastQueryLength()
    {
        $length = static::getEM()
            ->getConnection()
            ->executeQuery('SELECT FOUND_ROWS()', array())
            ->fetchColumn();

        return intval($length);
    }

    /**
     * Prepare array for IN () DQL function
     * 
     * @param array  $data   Hash array
     * @param string $prefix Placeholder prefix OPTIONAL
     *  
     * @return array (keys for IN () function & parameters hash array)
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function prepareArray(array $data, $prefix = 'arr')
    {
        $keys = array();
        $parameters = array();

        foreach ($data as $k => $v) {
            $k = $prefix . $k;
            $keys[] = ':' . $k;
            $parameters[$k] = $v;
        }

        return array($keys, $parameters);
    }

    /**
     * Build IN () condition 
     * 
     * @param \Doctrine\ORM\QueryBuilder $qb     Query builder
     * @param array                      $data   Hash array
     * @param string                     $prefix Placeholder prefix OPTIONAL
     *  
     * @return array Keys for IN () function
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function buildInCondition(\Doctrine\ORM\QueryBuilder $qb, array $data, $prefix = 'arr')
    {
        list($keys, $data) = self::prepareArray($data, $prefix);

        foreach ($data as $k => $v) {
            $qb->setParameter($k, $v);
        }

        return $keys;
    }

    /**
     * Import SQL 
     * 
     * @param string $sql SQL
     *  
     * @return integer Lines count
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function importSQL($sql)
    {
        $lines = 0;

        $conn = static::$em->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        do {
            // Required due to "MySQL has gone away!" issue
            $stmt->fetch();
            $stmt->closeCursor();

            $lines++;
        } while ($stmt->nextRowset());

        return $lines;
    }

    /**
     * Import SQL from file 
     * 
     * @param string $path File path
     *  
     * @return integer Lines count
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function importSQLFromFile($path)
    {
        if (!file_exists($path)) {

            throw new \InvalidArgumentException(
                sprintf('SQL file \'%s\' does not exist.', $path)
            );

        } elseif (!is_readable($path)) {

            throw new \InvalidArgumentException(
                sprintf('SQL file \'%s\' does not have read permissions.', $path)
            );

        }

        return $this->importSQL(file_get_contents($path));
    }

    /**
     * Truncate data by repository type
     * 
     * @param string $type Repository type
     * 
     * @return integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function truncateByType($type)
    {
        return $this->truncate($this->getTruncateTableNames($type));
    }

    /**
     * Get table names by type for truncate
     * 
     * @param string $type Repository type
     *  
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getTruncateTableNames($type)
    {
        $list = $this->detectTruncateTables($this->getTruncateMetadatas($type));

        if (\XLite\Model\Repo\ARepo::TYPE_STORE == $type) {

            $forbid = array();
            foreach ($this->forbidTruncateTablesStore as $n) {
                $forbid[] = $this->tablePrefix . $n;
            }

            $list = array_diff($list, $forbid);
        }

        return $list;
    }

    /**
     * Get class metadata by type for truncate
     * 
     * @param string $type Repository type
     *  
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getTruncateMetadatas($type)
    {
        $list = array();

        foreach ($this->getAllMetadata() as $cmd) {
            if ($type == static::getRepo($cmd->name)->getRepoType()) {
                $list[] = $cmd;
            }
        }

        return $list;
    }

    /**
     * Truncate all data
     *
     * @param array $tableNames Table names OPTIONAL
     * 
     * @return integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function truncate(array $tableNames = array())
    {
        if (!$tableNames) {
            $tableNames = $this->detectTruncateTables($metadatas);
        }

        $sql = array();
        foreach ($tableNames as $tableName) {
            $sql[] = self::$em->getConnection()->getDatabasePlatform()->getTruncateTableSQL($tableName);
        }

        return $this->executeQueries($sql);
    }

    /**
     * Detect truncate table names by class metadatas
     * 
     * @param array $metadatas Class metadata list
     *  
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function detectTruncateTables(array $metadatas)
    {
        $calc = new \Doctrine\ORM\Internal\CommitOrderCalculator;

        foreach ($metadatas as $class) {
            $calc->addClass($class);

            foreach ($class->associationMappings as $assoc) {
                if ($assoc['isOwningSide']) {
                    $targetClass = static::$em->getClassMetadata($assoc['targetEntity']);

                    if (!$calc->hasClass($targetClass->name)) {
                        $calc->addClass($targetClass);
                    }

                    // add dependency ($targetClass before $class)
                    $calc->addDependency($targetClass, $class);
                }
            }
        }

        $commitOrder = $calc->getCommitOrder();

        $associationTables = array();

        foreach ($commitOrder as $class) {
            foreach ($class->associationMappings as $assoc) {
                if ($assoc['isOwningSide'] && $assoc['type'] == \Doctrine\ORM\Mapping\ClassMetadata::MANY_TO_MANY) {
                    $associationTables[] = $assoc['joinTable']['name'];
                }
            }
        }

        $orderedTables = array_unique($associationTables);

        // Truncate tables in reverse commit order
        foreach (array_reverse($commitOrder) as $class) {
            if (
                (!$class->isInheritanceTypeSingleTable() || $class->name == $class->rootEntityName)
                && !$class->isMappedSuperclass
                && !in_array($class->getTableName(), $orderedTables)
            ) {
                $orderedTables[] = $class->getTableName();
            }
        }

        return $orderedTables;
    }
}
