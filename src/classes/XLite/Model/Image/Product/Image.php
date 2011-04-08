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
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\Model\Image\Product;

/**
 * Product image
 * 
 * @see   ____class_see____
 * @since 1.0.0
 *
 * @Entity 
 * @Table  (name="product_images",
 *          indexes={
 *              @Index (name="id", columns={"id"})
 *          }
 * )
 */
class Image extends \XLite\Model\Base\Image
{
    /**
     * Alternative image text
     * 
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @Column (type="string", length="255")
     */
    protected $alt = '';

    /**
     * Image position 
     * 
     * @var   integer
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @Column (type="integer")
     */
    protected $orderby = 0;

    /**
     * Relation to a product entity
     *
     * @var   \XLite\Model\Product
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="images")
     * @JoinColumn (name="id", referencedColumnName="product_id")
     */
    protected $product;
}
