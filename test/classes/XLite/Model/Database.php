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

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

define('BACKUP_LIMIT_COUNT', 10);

// define DB backup properties
define('SQL_DUMP_DIR', 'var/backup/');
define('SQL_DUMP_FILE', 'var/backup/sqldump.sql.php');
define('SQL_UPLOAD_DIR', 'var/tmp/');

/**
* Class Database implements the factory method for creating the concrete
* database classes. Also creates the database connection.
*
* @package Kernel
* @version $Id$
*/
class XLite_Model_Database extends XLite_Base implements XLite_Base_ISingleton
{
	const DBTABLE_PREFIX = 'xlite_';

    // properties {{{

    /**
    * Database options.
    * 
    * @var    options
    * @access private
    */	
    public $options = array(
            'phptype'  => 'mysql_xlite',
            'dbsyntax' => false,
            'username' => '',
            'password' => '',
            'protocol' => false,
            'hostspec' => '',
            'port'     => false,
            'socket'   => false,
            'database' => '',
			'persistent'	=> 0);

    /**
    * Database connection resource
    */	
    public $connection = null;	
    public $connected = false;	
    
    protected $cache = array();	
    protected $cacheEnabled = false;

	protected $profiler = null;
	protected $profilerEnabled = false;

	public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }

	public function __construct()
    {
        $this->profiler = XLite_Model_Profiler::getInstance();
		$this->profilerEnabled = $this->profiler->enabled;
    }

    public function connect()
    {
        // profile db connect
        $time = microtime(true);
        $options = XLite::getInstance()->getOptions('database_details');

		if (!empty($options['socket'])) {
			$options['hostspec'] .= ':' . $options['socket'];
		} elseif (!empty($options['port'])) {
			$options['hostspec'] .= ':' . $options['port'];
		}

		$function = 'mysql_' . ((isset($options['persistent']) && 'on' == strtolower($options['persistent'])) ? 'p' : '') . 'connect';

		($this->connection = @$function($options['hostspec'], $options['username'], $options['password'])) || $this->_die(mysql_error());
        @mysql_select_db($options['database'], $this->connection) || $this->_die(mysql_error()); 

        $this->connected = true;

        $this->profiler->dbConnectTime = microtime(true) - $time;
    }

    protected function getCachedResult($sql)
    {
		return isset($this->cache[$hash = md5($sql)]) ? $this->cache[$hash] : false;
    }
    
    protected function cacheResult($sql, $result)
    {
		$this->cache[md5($sql)] = $result;
    }
    
	public function getOne($sql)
	{
		if ($this->cacheEnabled && ($result = $this->getCachedResult($sql))) {
			return $result;
		}

		list($result) = @mysql_fetch_row($res = $this->query($sql));
        @mysql_free_result($res);

		if ($this->cacheEnabled) {
	        $this->cacheResult($sql, $result);
		}

		return $result;
	}
    
	public function getAll($sql)
	{
        if ($this->cacheEnabled && ($result = $this->getCachedResult($sql))) {
            return $result;
        }

		$result = array();
		$res = $this->query($sql);
		while ($row = mysql_fetch_assoc($res)) {
			$result[] = $row;
		}
        @mysql_free_result($res); 

		if ($this->cacheEnabled) {
            $this->cacheResult($sql, $result);
        }

		return $result;
	}

	public function getRow($sql)
	{
		if ($this->cacheEnabled && ($result = $this->getCachedResult($sql))) {
            return $result;
        }
        
        $result = null;
		$row = @mysql_fetch_assoc($res = $this->query($sql));
        @mysql_free_result($res);

		$result = false === $row ? null : $row;

		if ($this->cacheEnabled) {
            $this->cacheResult($sql, $result);
        }

        return $result;
	}

	public function getColumn($sql, $columnName)
	{
		if ($this->cacheEnabled && ($result = $this->getCachedResult($sql))) {
            return $result;
        }

        $result = array();
		$res = $this->query($sql);
        while ($row = mysql_fetch_assoc($res)) {
			if (isset($row[$columnName])) {
				$index = $row[$columnName];
				unset($row[$columnName]);
    	        $result[$index] = array_merge($row, isset($result[$index]) ? $result[$index] : array());
			}
        }
        @mysql_free_result($res);

		if ($this->cacheEnabled) {
            $this->cacheResult($sql, $result);
        }

        return $result;
	}

	function query($sql)
	{
		if ($this->profilerEnabled) {
	        $this->profiler->addQuery($sql);
    	    $start = microtime(true);
		}

		($res = @mysql_query($sql, $this->connection)) || $this->_die(mysql_errno() . ': ' . mysql_error() .  ' in ' . $sql);

		if ($this->profilerEnabled) {
	        $this->profiler->setQueryTime($sql, microtime(true) - $start);
		}

        return $res;
	}

    /**
    * Returns the SQL Database table name for specified alias.
    *
    * @param string $alias  The sql table alias.
    * @return mixed         The table name
    * @static
    */
    function getTableByAlias($alias = '') // {{{
    {
        return self::DBTABLE_PREFIX . $alias;
    } // }}}

    function isTableExists($table) // {{{
    {
        $tables = $this->getAll("SHOW TABLES");
        foreach ($tables as $key => $tab) {
            if (in_array($table, $tab)) {
                return true;
            }
        }
        return false;
    } // }}}
    
    function isIndexExists($index, $table) // {{{
    {
        $i = $this->getAll("SHOW INDEX FROM $table");
        foreach ($i as $row) {
            if ($index == $row["Key_name"]) {
                return true;
            }
        }
        return false;
    } // }}}

    function isFieldExists($table, $field) // {{{
    {
        $fields = $this->getAll("SHOW FIELDS FROM $table");
        foreach ($fields as $fieldDescription) {
            if ($field == $fieldDescription["Field"]) {
                return true;
            }
        }
        return false;
    } // }}}

    // CREATE / ALTER / DROP functions {{{

    function createTable($table, $sql, $v = true) // {{{
    {
        if ($v) echo "Creating table $table ... ";
        if ($this->isTableExists($table)) {
            echo "[TABLE ALREADY EXISTS]\n";
        } else {
            if (mysql_query($sql, $this->connection) === false) {
				if ($v) echo "[FAILURE:" . mysql_error($this->connection) . "]\n";
				return false;
			} else {
            	if ($v) echo "[OK]\n";
			}
        }
		return true;
    } // }}}

    function createIndex($index, $table, $sql, $v = true) // {{{
    {
        if ($v) echo "Creating index $index in $table ... ";
        if ($this->isIndexExists($index, $table)) {
            if ($v) echo "[INDEX ALREADY EXISTS]\n";
        } else {
            if (mysql_query($sql, $this->connection) === false) {
				if ($v) echo "[FAILURE:" . mysql_error($this->connection) . "]\n";
				return false;
			} else {
            	if ($v) echo "[OK]\n";
			}
        }
		return true;
    } // }}}

    function dropTable($table, $v = true) // {{{
    {
        mysql_query("DROP TABLE IF EXISTS $table", $this->connection);
        if ($v) echo "Delete table $table ... [OK]\n";
    } // }}}
    
    function alterTable($table, $sql, $v = true) // {{{
    {
        if ($v) echo "Modifying table $table ... ";
        if (preg_match("/ALTER +TABLE +([^ ]*) +ADD ([^ ]*)/i", $sql, $matches)) {
            if ($this->isFieldExists($matches[1], $matches[2])) {
                if ($v) echo "[THE FIELD $matches[2] ALREADY EXISTS]\n";
            } else {
                if (mysql_query($sql, $this->connection) === false) {;
                    if ($v) echo "[FAILURE:" . mysql_error($this->connection) . "]\n";
                    return false;
                } else {
                    if ($v) echo "[OK]\n";
                }
            }
        }
        return true;
    } // }}}

    // }}}

    // BACKUP / RESTORE functions {{{

    function backup($file, $verbose = false) // {{{
    {
        $handle = null;
        // open backup file if necessary
        if (!is_null($file)) {
            if (!$handle = fopen($file, 'w')) {
                $this->_die("Failed to open backup file $file for writing");
            }
        }    
        // do not cache backup queries
        $this->set("cacheEnabled", false);
        // write backup file heading comments
        $this->_write($handle, "-- WARNING: Do not change this line <?php die(); /*\n");
        foreach ($this->getTables() as $table) {
            // dump table chema
            if ($verbose) {
                echo "Backup table [$table] ... ";
                flush();
            }
            $this->_write($handle, $this->getTableSchema($table));
            // dump table content
            while ($result = $this->getTableContent($table)) {
                $this->_write($handle, $result);
            }
            $this->_write($handle, "\n");
            if ($verbose) {
                echo "[OK]<br>\n";
                flush();
            }
        }
        // write backup file ending comments
        $this->_write($handle, "-- WARNING: Do not change this line */ ?>\n");
        is_null($handle) or fclose($handle) && chmod($file, get_filesystem_permissions(0666));
    } // }}}

    function restore($file) // {{{
    {
        echo "Please wait...<br>\n";
        $error = query_upload($file, $this->db->connection, true, true);
        // cleanup compiled cache
        echo "<br>\n";
        func_cleanup_cache("classes", true);
        func_cleanup_cache("skins", true);
        return $error;
    } // }}}

    function getTableSchema($table) // {{{
    {
        // do not cache queries
        $cacheEnabled = $this->get("cacheEnabled");
        $this->set("cacheEnabled", false);

        $schema  = "DROP TABLE IF EXISTS $table;\n";
        $schema .= "CREATE TABLE $table (\n";
        $table_list = '(';
        // Add fields
        foreach ($this->getTableFields($table) as $field) {
            $schema .= '  ' . $field['Field'] . ' ' . $field['Type'];
            if ($field['Null'] != 'YES') {
                $schema .= ' NOT NULL';
            }
            if ($field['Default']) {
                $schema .= ' default \'' . $field['Default'] . '\'';
            }
            if (isset($field['Extra'])) {
                $schema .= ' ' . $field['Extra'];
            }
            $schema .= ",\n";
            $table_list .= $field['Field'] . ', ';
        }
        $schema = ereg_replace(",\n$", "", $schema);
        $table_list = ereg_replace(", $", "", $table_list) . ')';
        // Add keys
        $index = array();
        foreach ($this->getTableKeys($table) as $key) {
            $kname = $key['Key_name'];
            $comment  = (isset($key['Comment'])) ? $key['Comment'] : '';
            $index_type = (isset($key['Index_type'])) ? $key['Index_type'] : '';
            
            if (($kname != "PRIMARY") && ($key['Non_unique'] == 0)) {
                $kname = "UNIQUE|$kname";
            }
            if ($comment == "FULLTEXT" || $index_type == "FULLTEXT") {
                $kname = "FULLTEXT|$kname";
            }
            if(!isset($index[$kname])) {
                $index[$kname] = array();
            }
            $index[$kname][] = $key['Column_name'];
        }
        while (list($x, $columns) = @each($index)) {
            $schema .= ",\n";
            if ($x == "PRIMARY") {
                $schema .= "  PRIMARY KEY (";
            } elseif (substr($x, 0, 6) == "UNIQUE") {
                $schema .= "  UNIQUE ".substr($x,7)." (";
            } else if (substr($x, 0, 8) == 'FULLTEXT') {
                $schema .= "  FULLTEXT " . substr($x, 9) . " (";
            } else {
                $schema .= "  KEY $x (";
            }
            $schema .= implode($columns, ", ") . ")";
        }
        $schema .= "\n) TYPE=MyISAM;\n\n";

        $this->set("cacheEnabled", $cacheEnabled);
        return $schema;
    } // }}}

    function getTableKeys($table) // {{{
    {
        return $this->getAll("SHOW KEYS FROM $table");
    } // }}}
    
    function getTableFields($table) // {{{
    {
        return $this->getAll("SHOW FIELDS FROM $table");
    } // }}}

    function getTableContent($table) // {{{
    {
        static $stat;
        if (!isset($stat)) $stat = array();
        // get table info if necessary
        if (!isset($stat[$table])) {
            foreach ($this->getTableInfo($table) as $data) {
                $stat[$table]["count"] = $this->getOne("SELECT COUNT(*) FROM $table");
                $stat[$table]["number"][$data["name"]] = $this->_isNumber($data["type"]);
            }    
            $stat[$table]["from"] = 0;
        }
        $limit = $stat[$table]["from"] + BACKUP_LIMIT_COUNT;
        $sql = "SELECT * FROM $table LIMIT ". $stat[$table]["from"] .", ".BACKUP_LIMIT_COUNT;
        $stat[$table]["from"] = $limit;

        $search  = array("\x00", "\x0a", "\x0d", "\x1a", "*/", "/*");
        $replace = array('\0', '\n', '\r', '\Z', "\*\/", "\/\*");

        $content = "";
        $result = $this->getAll($sql);
        foreach ($result as $row) {
            $schema = "INSERT INTO $table VALUES (";
            $values = array();
            foreach ($row as $name => $value) {
                if (!isset($row[$name])) {
                    $values[] = 'NULL';
                } elseif ($row[$name] == '0' || $row[$name] != '') { 
                    if ($stat[$table]["number"][$name]) {
                        $values[] = $row[$name];
                    } else {
                        $values[] = "'" . str_replace($search, $replace, str_replace('\'', '\\\'', str_replace('\\', '\\\\', $row[$name]))) . "'";
                    }
                } else {
                    $values[] = "''";
                }
            }
            $content .= $schema . implode(', ', $values) . ')' . ";\n";
        }
        return empty($content) ? false : $content;
    } // }}}

    function getTableInfo($table) // {{{
    {
        $count = 0;
        $id    = 0;
        $res   = array();

        if (is_string($table)) {
            $id = mysql_list_fields($this->options['database'],
                    $table, $this->connection);
            if (empty($id)) {
                $this->_die('Cannot get information about the table ' . $table . " (database " . $this->options['database'] . ") ");
            }
        }

        $count = @mysql_num_fields($id);

        $res['num_fields']= $count;

        for ($i=0; $i<$count; $i++) {
            $res[$i]['table'] = @mysql_field_table ($id, $i);
            $res[$i]['name']  = @mysql_field_name  ($id, $i);
            $res[$i]['type']  = @mysql_field_type  ($id, $i);
            $res[$i]['len']   = @mysql_field_len   ($id, $i);
            $res[$i]['flags'] = @mysql_field_flags ($id, $i);
        }
        @mysql_free_result($id);
        return $res;
    } // }}}

    function getTables() // {{{
    {
        $tables = array();
        foreach ($this->getAll("SHOW TABLES") as $table) {
            $data = array_values($table);
            if (strncmp($data[0], self::DBTABLE_PREFIX, strlen(self::DBTABLE_PREFIX))==0) {
                $tables[] = $data[0];
            }    
        }
        return $tables;
    } // }}}

    function _write($handle, $content) // {{{
    {
        if (is_null($handle)) {
            echo $content;
        } else {
            fwrite($handle, $content, strlen($content)) or $this->_die('<font color="red">Backup file write failed</font>');
        }
    } // }}}

    function _isNumber($type) // {{{
    {
        return ($type == 'tinyint' || $type == 'smallint' || $type == 'mediumint' || $type == 'int' || $type == 'bigint'  ||$type == 'timestamp') ? true : false;
    } // }}}
    
    // }}}
}

