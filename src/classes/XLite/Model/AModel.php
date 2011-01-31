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
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Model;

/**
* Delimiters definitions. Used for import / export store data.
*/
$GLOBALS['DATA_DELIMITERS'] = array(
    'semicolon' => ';',
    'comma'     => ',',
    'tab'       => "\t",
);
$GLOBALS['TEXT_QUALIFIERS'] = array(
    'double_quote' => '"',
    'single_quote' => '\'',
);

/**
 * Base class is an abstract class for all database-mapped objects
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class AModel extends \XLite\Base
{
    /**
     * Table alias 
     * 
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $alias;

    /**
     * Object properties (table filed => default value)
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $fields = array();

    /**
     * Object properties list
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $properties = array();

    /**
     * Primary keys names
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $primaryKey = array();

    /**
     * Auto-increment file name
     * 
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $autoIncrement = null;
    
    /**
     * Shows whether the object data have been read from DB or not
     * 
     * @var    boolean
     * @access public
     * @see    ____var_see____
     * @since  3.0.0
     */
    public $isRead = false;

    /**
     * Checks whether the object data exists in DB
     * 
     * @var    boolean
     * @access public
     * @see    ____var_see____
     * @since  3.0.0
     */
    public $isPersistent = false;
    
    /**
     * Default order file name
     * 
     * @var    string
     * @access public
     * @see    ____var_see____
     * @since  3.0.0
     */
    public $defaultOrder;

    /**
     * Default SQL filter (WHERE block) for findAll() method
     * 
     * @var    string
     * @access public
     * @see    ____var_see____
     * @since  3.0.0
     */
    public $_range;

    /**
     * If set to true, findAll will fetch only primary keys (isRead = false)
     * 
     * @var    boolean
     * @access public
     * @see    ____var_see____
     * @since  3.0.0
     */
    public $fetchKeysOnly = false;

    /**
     * If set to true, findAll will fetch only object' indexes
     * 
     * @var    boolean
     * @access public
     * @see    ____var_see____
     * @since  3.0.0
     */
    public $fetchObjIdxOnly = false;

    /**
     * Update or create model object (DB record)
     * 
     * @return boolean 
     * @access public
     * @since  3.0.0
     */
    public function modify()
    {
        return $this->isPersistent ? $this->update() : $this->create();
    }

    /**
     * Returns the SQL database table name for this object. Uses "alias"
     * property as a key. 
     * 
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getTable() 
    {
        return $this->db->getTableByAlias($this->alias);
    }

    function filter()
    {
        return true;
    }

    function isObjectDescriptor($descriptor)
    {
        return is_array($descriptor) && isset($descriptor['class']) && isset($descriptor['data']);
    }

    function descriptorToObject(array $descriptor)
    {
        $object = new $descriptor['class'];
        $object->isPersistent = true;
        $object->isRead = false;
        $object->properties = $descriptor['data'];

        return $object;
    }

    function iterate($where = null, $orderby = null, $groupby = null, $limit = null) 
    {
        // apply the default order
        if (empty($orderby)) {
            if (!empty($this->defaultOrder))
                $orderby = $this->defaultOrder;
        }
        $where = $this->_buildWhere($where);
        // build select query
        $this->sql = $this->_buildSelect($where, $orderby, $groupby, $limit);
        $result = \XLite\Model\Database::getInstance()->getAll($this->sql);
        if (!is_array($result)) {
            $this->_die ($this->sql.": ".$result->getMessage());
        }
        return $result;
    }

    function next(&$result)
    {
        do {
            $row = array_shift($result);
            if ($row === null) {
                return false;
            }
            if ($this->fetchKeysOnly) {
                $this->isPersistent = true;
                $this->isRead = false;
                $this->properties = $row;
            } else {
                $this->properties = array();
                $this->_updateProperties($row);
            }
        } while (!$this->filter());
        return true;
    }

    function _aggregate($field, $aggregate, $where = null) 
    {
        $sql = "SELECT $aggregate($field) FROM " . $this->getTable();
        if ($where) {
            $sql .= " WHERE $where";
        }
        return $this->db->getOne($sql);
    }

    function count($where = null, $field = "*") 
    {
        return $this->_aggregate($field, "COUNT", $where);
    }

    function min($field, $where = null) 
    {
        return $this->_aggregate($field, "MIN", $where);
    }

    function max($field, $where = null) 
    {
        return $this->_aggregate($field, "MAX", $where);
    }

    function avg($field, $where = null) 
    {
        return $this->_aggregate($field, "AVG", $where);
    }

    /**
    * Checks whether the database record exists for this object
    *
    * @access public
    * @return boolean True if record exists for object / false otherwise
    */
    function isExists() 
    {
        if (!$this->isRead) {
            $this->isRead = $this->isPersistent ? $this->read() : false;
            return $this->isRead;
        }
        return true;
    }

    /**
     * Update object
     * 
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function update()
    {
        $this->_beforeSave();

        // updated data for the persistent object
        if (!$this->isPersistent) {
            $this->doDie('Unable to update unspecified row for ' . $this->alias);
        }

        $this->sql = $this->_buildUpdate();
        $result = false;
        if ($this->sql !== false) {
            $result = $this->db->query($this->sql);
        }

        return $result;
    }

    /**
    * Creates the database record for this object.
    *
    * @access public
    */
    function create() 
    {
        $this->_beforeSave();
        // create record
        if (!$this->isPersistent || empty($this->autoIncrement)) {
            // build INSERT sql query
            $this->sql = $this->_buildInsert();
            $result = $this->db->query($this->sql);
            // get auto_increment field value
            if (!empty($this->autoIncrement)) {
                $this->setComplex($this->autoIncrement, mysql_insert_id($this->db->connection));
            }
            // fill unspecified fields with default values
            foreach ($this->fields as $field => $default) {
                if (!array_key_exists($field, $this->properties)) {
                    $this->properties[$field] = $default;
                }
            }
            $this->isPersistent = true;
            $this->isRead = true;
            return $result;
        }
        // die otherwise
        $this->doDie("Unable to insert duplicate row for " . $this->alias . " " . join(',', $this->primaryKey));
    }
    
    /**
    * Clones an existing record. Only available on 
    * auto-incremented primary keys.
    */
    function cloneObject() 
    {
        if ($this->autoIncrement) {
            $this->isRead = $this->read();
            $new = clone $this;
            $new->set($this->autoIncrement, null);
            $new->create();
            return $new;
        } else {
            $this->doDie("Can't clone non-autoincremented object");
        }
    }

    /**
    * A function called at the start of each create() and update()
    * This function is empty in this implementation and overridden
    * in descendant classes
    */
    function _beforeSave() {}

    /**
     * Deletes the database record for this object
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function delete() 
    {
        // delete if object is persistent
        if ($this->isPersistent) {
            $this->sql = $this->_buildDelete();
            $this->db->query($this->sql);
            $this->isPersistent = false;
            $this->isRead = false;

        } else {

            // die otherwise
            $this->doDie('Unable to delete unspecified row from ' . $this->alias);
        }
    }

    /**
     * Builds the SQL INSERT statement query for this object properties
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function _buildInsert()
    {
        $properties = $this->properties;
        if (!empty($this->autoIncrement)) {
            // remove auto increment field
            if (isset($properties[$this->autoIncrement])) {
                unset($properties[$this->autoIncrement]);
            }
        }
        $fields = implode(", ", array_keys($properties));
        $values = array_values($properties);
        for ($i=0; $i<count($values); $i++) {
            $values[$i] = "'".addslashes($values[$i])."'";
        }
        $values = implode(',', $values);
        $table = $this->getTable();
        return "INSERT INTO $table ($fields) VALUES ($values)";
    }
    
    /**
    * Builds the SQL DELETE statement to delete this object database record.
    *
    * @access private
    * @return string The SQL DELETE statement
    */
    function _buildDelete() 
    {
        $condition = array();
        foreach ($this->primaryKey as $field) {
            $condition[] = "$field='".addslashes($this->properties[$field])."'";
        }
        $condition = implode(' AND ', $condition);

        $table = $this->getTable();
        return "DELETE FROM $table WHERE $condition";
    }

    /**
     * Builds the SQL UPDATE statement for updating this object database record
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function _buildUpdate()
    {
        $properties = $this->properties;
        $condition = array();
        foreach ($this->primaryKey as $field) {
            $condition[] = $field . ' = \'' . (isset($properties[$field]) ? addslashes($properties[$field]) : '') . '\'';
            // remove primary keys
            unset($properties[$field]);
        }
        $condition = implode(' AND ', $condition);
        $values = array(); // compile 'set' clause
        if (is_array($properties)) {
            foreach ($properties as $field => $val) {
                if (is_scalar($val)) {
                    $values[] = "$field='".addslashes($val)."'";
                }
            }
        }
        if (!$values) {
            return false;
        }
        $values = implode(',', $values);
        $table = $this->getTable();
        return 'UPDATE ' . $table . ' SET ' . $values . ' WHERE ' . $condition;
    }
    
    /**
    * Enables the object - sets "enabled' property to 1.
    * @access public
    */
    function enable() 
    {
        $this->set('enabled', 1);
    }

    /**
    * Disables the object - sets "enabled' property to 0.
    * @access public
    */
    function disable() 
    {
        $this->set('enabled', 0);
    }

    /**
    * Compares the property name specified by $prop with $val and
    * returns true if equals, false otherwise. Useful in templates.
    *
    * @access public
    */
    function isSelected($property, $value = null, $prop = null) 
    {
        if (is_object($value)) {
            return $this->get($property) == $value->get($prop);
        }
        return $this->get($property) == $value;
    }

    /**
    * Calculates MD5 hash based on the object properties.
    *
    * @access public
    */
    function md5() 
    {
        return md5(implode('', $this->getProperties()));
    }

    /**
    * Prints HTML dump of object properties. Useful for debug.
    */
    function dump() 
    {
        echo "<p><pre>"; print_r($this->getProperties()); echo "</pre></p>";
    }

    function toXML() 
    {
        return $this->fieldsToXML();
    }

    function fieldsToXML() 
    {
        $xml = "";
        $values = $this->getProperties();
        foreach ($values as $name => $value) {
            if (!strlen(trim($value))) {
                continue;
            } elseif (is_numeric($value)) {
            } else {
                $value = "<![CDATA[$value]]>";
            }
            $xml .= "<$name>$value</$name>\n";
        }
        return $xml;
    }

    function toCSV() 
    {
    }

    function toString() 
    {
    }

    // IMPORT/EXPORT methods {{{

    /**
     * Process the import error
     * 
     * @param boolean $returnError Trigger for stopping script or returning false OPTIONAL
     *  
     * @return boolean 
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function processImportError($returnError = false)
    {
        if (!$returnError) {
            die($this->importError);
        }

        return false;
    }

    /**
     * import 
     * 
     * @param array $options ____param_comment____
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function import(array $options)
    {
        global $DATA_DELIMITERS, $TEXT_QUALIFIERS;

        $this->importError = '';

        is_array($options) or $this->importError = 'Invalid import options.';

        if ($this->importError) {
            return $this->processImportError(false);

        } else {

            $file = $options['file'];

            $handle = fopen($file, 'r') or $this->importError = "Failed to open import file $file.";

            if ($this->importError) {
                return $this->processImportError($options['return_error']);
            }

            if (!empty($options['delimiter'])) {
                $options['delimiter'] = $DATA_DELIMITERS[$options['delimiter']];
            }

            $qualifier = null;

            if (!empty($options['text_qualifier'])) {
                $qualifier = $TEXT_QUALIFIERS[$options['text_qualifier']];
            }

            $layout = $options['layout'];
            
            $this->lineNo = 1;
            $line_buffer = '';

            while ($line = fgets($handle, 4096)) {

                $error = '';

                if (strlen($line_buffer) > 0) {
                    $line = $line_buffer . $line;
                }

                $columns = func_parse_csv($line, $options['delimiter'], $qualifier, $error);

                if (is_null($columns) && "Unexpected end of line; $qualifier expected" == $error) {
                    $line = str_replace("\r\n", " ", $line);
                    $line = str_replace("\n", " ", $line);
                    $line_buffer = $line;
                    continue;

                } elseif (is_null($columns)) {
                    $this->importError = "CVS syntax error in line " . $this->lineNo . ": $error.";
                    return $this->processImportError($options['return_error']);

                } elseif (is_array($columns) && count($columns) == 1 && empty($columns[0])) {
                    continue;
                }

                $line_buffer = '';
                $properties = array();
                $layout_idx = 0;

                for ($i = 0; $i < count($layout); $i++) {

                    if ($layout[$i] != "NULL") {

                        array_key_exists($layout_idx, $columns) or $this->importError = 'Invalid CSV file: column count does not match.';

                        if ($this->importError) {
                            return $this->processImportError($options['return_error']);
                        }

                        $properties[$layout[$i]] = $columns[$layout_idx];
                        $layout_idx ++;
                    }
                }

                $options['properties'] = $properties;
                $this->_import($options);
                $this->lineNo ++;
            }
        }
    }

    /**
     * getImportFields 
     * 
     * @param array $layout The list of fields for importing OPTIONAL
     *  
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getImportFields($layout = null)
    {
        isset($this->importFields) or die('importFields property undefined');

        $fields = $this->importFields;
        $result = array();

        foreach ($fields as $field) {
            $result[] = $fields;
        }

        if (!is_null($layout)) {

            if (isset($this->config->ImportExport->$layout)) {

                $layout = explode(',', $this->config->ImportExport->$layout);

                foreach ($result as $id => $fields) {

                    if (isset($layout[$id])) {

                        $selected = $layout[$id];

                        if (array_key_exists($selected, $result[$id])) {
                            $result[$id][$selected] = true;
                        }
                    }
                }
            }
        }

        return $result;
    }

    function _import(array $options) 
    {
        $this->doDie("Base::_import() method should be overridden");
    }
    
    function _export($layout, $delimiter) 
    {
        $this->doDie("Base::_export() method should be overridden");
    }
    
    function export($layout, $delimiter, $where = null, $orderby = null, $groupby = null) 
    {
        $count = $this->count() or die("There is nothing to export (empty data)");
        $processed = 0;
        $limit = 10;
        do {
            $limit_sql = "$processed, $limit";
            $items = $this->findAll($where, $orderby, $groupby, $limit_sql);
            $items_number = count($items);
            for ($i=0; $i<$items_number; $i++) {
                print ($export_csv_string = func_construct_csv($items[$i]->_export($layout, $delimiter), $delimiter, '"'));
                if (strlen($export_csv_string) > 0) {
                    print "\n";
                }
            }
            $processed += $limit;
            $items = array();
        } while ($processed < $count);
        return true;
    }

    function _stripSpecials($value) 
    {
        return $value;
    }

    function _unslashProperties(&$properties, $qualifier = null) 
    {
        foreach ($properties as $name => $value) {
            if (!is_null($qualifier)) {
                // strip start/end quotes
                $value = preg_replace("/(^$qualifier)(.*)($qualifier$)/", "\\2", trim($value));
            }
            // remove double quotes
            $properties[$name] = str_replace("\"\"", "\"", $value);
        }
    }

    // END IMPORT/EXPORT methods }}}

    /**
     * Get formatted currency value
     * 
     * @param float $price Currency value
     *  
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function formatCurrency($price)
    {
        return sprintf('%.02f', round(doubleval($price), 2));
    }





    /**
     * Creates an associative array with index = object primary key 
     * 
     * @param array  $objects Array(objects)
     * @param string $field   Field to use as an index
     *  
     * @return array
     * @access protected
     * @since  3.0
     */
    protected function _assocArray(array $objects, $field)
    {
        $result = array();

        foreach ($objects as $object) {
            if (is_array($object) && isset($object['class']) && isset($object['data'])) {
                $properties = $object['data'];
                $object = new $object['class'];
                $object->isPersistent = true;
                $object->isRead = false;
                $object->properties = $properties;
                unset($properties);
            }

            $result[$object->get($field)] = $object;
        }

        return $result;
    }

    /**
     * Checks whether the all primary keys for this object are set or not 
     * 
     * @return boolean 
     * @access protected
     * @since  3.0
     */
    protected function _allKeysSet()
    {
        foreach ($this->primaryKey as $field) {
            if (!isset($this->properties[$field]) || '' === $this->properties[$field]) return false;
        }

        return true;
    }

    /**
     * Updates the object properties with specified array values. Sets persistent and read flags 
     * 
     * @param array $properties Property values array
     *  
     * @return void
     * @access protected
     * @since  3.0
     */
    protected function _updateProperties(array $properties = array())
    {
        $this->properties = array_merge(array_intersect_key($properties, $this->fields), $this->properties);
        $this->isPersistent = $this->isRead = true;
    }

    /**
     * Compose the "WHERE" condition for SQL queries
     * 
     * @param string $where Condition
     *  
     * @return string
     * @access protected
     * @since  3.0
     */
    protected function _buildWhere($where)
    {
        return empty($this->_range) ? $where : $this->_range . (empty($where) ? '' : ' AND ' . $where);
    }

    /**
     * Builds the SQL SELECT statement for this object 
     * 
     * @param mixed $where   "where" condition OPTIONAL
     * @param mixed $orderby "orderby" condition OPTIONAL
     * @param mixed $groupby "groupby" condition OPTIONAL
     * @param mixed $limit   "limit" condition OPTIONAL
     *  
     * @return string
     * @access protected
     * @since  3.0
     */
    protected function _buildSelect($where = null, $orderby = null, $groupby = null, $limit = null)
    {
        $sql = 'SELECT ' 
               . implode(',', $this->fetchKeysOnly ? $this->primaryKey : array_keys($this->fields)) 
               . ' FROM ' . $this->getTable();

        foreach (
            array(
                'WHERE'    => $where,
                'GROUP BY' => $groupby,
                'ORDER BY' => $orderby,
                'LIMIT'    => $limit,
            ) as $statement => $condition
        ) {
            empty($condition) || ($sql .= ' ' . $statement . ' ' . $condition);
        }

        return $sql;
    }

    /**
     * Builds SQL query for reading the database data for this object by primary key 
     * 
     * @return string
     * @access protected
     * @since  3.0
     */
    protected function _buildRead()
    {
        $condition = array();

        foreach ($this->primaryKey as $field) {
            $condition[] = $field . ' = \'' . addslashes($this->properties[$field]) . '\'';
        }

        $fko = $this->fetchKeysOnly;
        $this->fetchKeysOnly = false;
        $sql = $this->_buildSelect(implode(' AND ', $condition));
        $this->fetchKeysOnly = $fko;

        return $sql;
    }

    /**
     * Perform some action on the object properties
     * 
     * @param array  $properties Values list
     * @param string $method     Method to execute
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function modifyProperties(array $properties, $method)
    {
        foreach ($properties as $field => $value) {
            if (isset($this->fields[$field])) {
                $this->$method($field, $properties[$field]);
            }
        }
    }


    /**
     * Constructs a new database object. The options argument list is a primary key value.
     * If it is specified, the object is created as isPersistent, otherwise - !isPersistent 
     * 
     * @return void
     * @access public
     * @since  3.0
     */
    public function __construct()
    {
        // if auto-increment is specified, make it a primary key of this table
        if (isset($this->autoIncrement)) {
            $this->primaryKey = array($this->autoIncrement);
        }

        foreach (func_get_args() as $index => $arg) {
            if (!empty($arg)) {
                $this->set($this->primaryKey[$index], $arg);
            }
        }
    }

    /**
     * Returns the properties of this object. Reads the object from database if necessary 
     * 
     * @return array
     * @access public
     * @since  3.0
     */
    public function getProperties()
    {
        if (!$this->isRead && $this->isPersistent) {
            $this->isRead = $this->read();
        }

        return $this->properties;
    }

    /**
     * Sets the properties for this object from the specified array 
     * 
     * @param array $properties The associative array with properties
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function setProperties(array $properties)
    {
        $this->modifyProperties($properties, 'set');
    }

    /**
     * Unsets the properties for this object from the specified array
     *
     * @param array $properties The associative array with properties
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function unsetProperties(array $properties)
    {
        $this->modifyProperties($properties, 'unsetProperty');
    }

    /**
     * Returns the specified property of this object. Read the object data from dataase if necessary 
     * 
     * @param string $property Field name
     *  
     * @return mixed
     * @access public
     * @since  3.0
     */
    public function get($property)
    {
        // check whether the property exists
        if (isset($this->fields[$property])) {

            // read object data if necessary
            if ($this->isPersistent && !$this->isRead && $property != $this->autoIncrement) {
                $this->isRead = $this->read();
            }

            return isset($this->properties[$property]) ? $this->properties[$property] : $this->fields[$property];
        }
        
        return parent::get($property);
    }

    /**
     * Sets the specified property value 
     * 
     * @param string $property Field name
     * @param mixed  $value    Field value
     *  
     * @return void
     * @access public
     * @since  3.0
     */
    public function set($property, $value)
    {
        if (isset($this->fields[$property])) {

            // set isRead to FALSE if object has not been read yet
            if (in_array($property, $this->primaryKey)) {
                $this->isRead = false;
            }

            $this->properties[$property] = $value;
            $this->isPersistent = $this->_allKeysSet();

        } else {

            parent::set($property, $value);
        }
    }

    /**
     * Sets the specified property value
     *
     * @param string $property Field name
     * @param mixed  $value    Field value OPTIONAL
     *
     * @return void
     * @access public
     * @since  3.0
     */
    public function unsetProperty($property, $value = null)
    {
        unset($this->properties[$property]);
    }

    /**
     * Reads the database data for this object. Dies for non-persistens objects (object which are not exist in database) 
     * 
     * @return boolean True if data found / false otherwise
     * @access public
     * @since  3.0
     */
    public function read()
    {
        // read data for persisten object
        if ($this->isPersistent) {

            // build select query
            $this->sql = $this->_buildRead();
            $result = $this->db->getRow($this->sql);

            if (isset($result)) {

                $this->_updateProperties($result);

                if ($this->filter()) {
                    return true;
                }

                // default properties
                $this->properties = $this->fields;
            }

            return false;
        }

        // die otherwise
        $this->doDie('Unable to read unspecified row for ' . $this->alias);
    }

    /**
     * Wrapps findAll() method with the default arguments 
     * 
     * @return array
     * @access public
     * @since  3.0
     */
    public function readAll()
    {
        return $this->findAll();
    }

    /**
     * Attempts to find the database record for this object and fill the object properties with data found
     * 
     * @param mixed $where "where" condition
     * @param mixed $order "orderby" condition OPTIONAL
     *  
     * @return boolean 
     * @access public
     * @since  3.0
     */
    public function find($where, $order = null)
    {
        $this->sql = $this->_buildSelect($this->_buildWhere($where), $order);
        $result = $this->db->getRow($this->sql);

        if (!isset($result)) {
            $this->isRead = true;
            return false;
        }

        $this->_updateProperties($result);

        return $this->filter();
    }

    /**
     * Attempts to read All database records for this class 
     * 
     * @param mixed $where   "where" condition OPTIONAL
     * @param mixed $orderby "orderby" condition OPTIONAL
     * @param mixed $groupby "groupby" condition OPTIONAL
     * @param mixed $limit   "limit" condition OPTIONAL
     *  
     * @return array
     * @access public
     * @since  3.0
     */
    public function findAll($where = null, $orderby = null, $groupby = null, $limit = null)
    {
        // apply the default order
        if (empty($orderby) && !empty($this->defaultOrder)) {
            $orderby = $this->defaultOrder;
        }

        $where = $this->_buildWhere($where);

        // build select query
        $this->sql = $this->_buildSelect($where, $orderby, $groupby, $limit);
        $result = $this->db->getAll($this->sql);

        $class = get_class($this);
        $objects = array();

        // create class instance for every row found
        foreach ($result as $row) {

            $object = new $class();
            $object->isPersistent = true;

            if ($this->fetchKeysOnly) {
                $object->isPersistent = true;
                $object->isRead = false;
                $object->properties = $row;
                $object_key = array('class' => $class, 'data' => $row);
            } else {
                $object->_updateProperties($row);
            }

            if ($object->filter()) {
                $objects[] = ($this->fetchKeysOnly && $this->fetchObjIdxOnly) ? $object_key : $object;
            }

            unset($object);
        }

        return $objects;
    }
}

