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
 * PHP version 5.3.0
 *
 * @category  LiteCommerce
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\Model;

/**
 * Session
 *
 * @see   ____class_see____
 * @since 1.0.0
 *
 * @Entity (repositoryClass="\XLite\Model\Repo\SessionCell")
 * @Table  (name="session_cells",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="iname", columns={"id", "name"})
 *      },
 *      indexes={
 *          @Index (name="id", columns={"id"})
 *      }
 * )
 */
class SessionCell extends \XLite\Model\AEntity
{
    /**
     * Cell unique id
     *
     * @var   integer
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $cell_id;

    /**
     * Session id
     *
     * @var   integer
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @Column (type="integer")
     */
    protected $id;

    /**
     * Name
     *
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @Column (type="string", length=255)
     */
    protected $name;

    /**
     * Value
     *
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @Column (type="text")
     */
    protected $value = '';

    /**
     * Value type
     *
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @Column (type="string", length=16)
     */
    protected $type;

    /**
     * Automatically get variable type
     *
     * @param mixed $value Variable to check
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public static function getTypeByValue($value)
    {
        $type = gettype($value);

        return in_array($type, array('NULL', 'unknown type')) ? null : $type;
    }

    /**
     * Common getter
     *
     * NOTE: this function is designed as "static public" to use in repository
     * NOTE: customize this method instead of the "getValue()" one
     *
     * @param mixed  $value Value to prepare
     * @param string $type  Field type OPTIONAL
     *
     * @return mixed
     * @see    ____func_see____
     * @since  1.0.0
     */
    public static function prepareValueForGet($value, $type = null)
    {
        $type = $type ?: static::getTypeByValue($value);

        switch ($type) {
            case 'boolean':
                $value = (bool) $value;
                break;

            case 'integer':
                $value = intval($value);
                break;

            case 'double':
                $value = doubleval($value);
                break;

            case 'string':
                $value = $value;
                break;

            case 'array':
            case 'object':
                $value = unserialize($value);
                break;

            default:
                $value = null;
        }

        return $value;
    }

    /**
     * Common setter
     *
     * NOTE: this function is designed as "static public" to use in repository
     * NOTE: customize this method instead of the "getValue()" one
     *
     * @param mixed  $value Value to prepare
     * @param string $type  Field type OPTIONAL
     *
     * @return mixed
     * @see    ____func_see____
     * @since  1.0.0
     */
    public static function prepareValueForSet($value, $type = null)
    {
        $type = $type ?: static::getTypeByValue($value);

        switch ($type) {
            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
                break;

            case 'array':
            case 'object':
                $value = serialize($value);
                break;

            default:
                $value = null;
        }

        return $value;
    }


    /**
     * Get value
     *
     * @return mixed
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getValue()
    {
        return static::prepareValueForGet($this->value, $this->getType());
    }

    /**
     * Set value
     *
     * @param mixed $value Value
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function setValue($value)
    {
        $this->type  = static::getTypeByValue($value);
        $this->value = static::prepareValueForSet($value, $this->type);
    }
}
