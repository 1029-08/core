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
 * Language label
 *
 * @see   ____class_see____
 * @since 1.0.0
 *
 * @Entity (repositoryClass="\XLite\Model\Repo\LanguageLabel")
 * @Table (name="language_labels",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="name", columns={"name"})
 *      }
 * )
 */
class LanguageLabel extends \XLite\Model\Base\I18n
{
    /**
     * Unique id
     *
     * @var   integer
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column (type="integer")
     */
    protected $label_id;

    /**
     * Label name
     *
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @Column (type="varbinary", length="255")
     */
    protected $name;

    /**
     * Get label translation 
     * 
     * @param string $code Language code OPTIONAL
     *  
     * @return \XLite\Model\LanguageLabelTranslation
     * @see    ____func_see____
     * @since  1.0.24
     */
    public function getLabelTranslation($code = null)
    {
        $result = null;

        $query = \XLite\Core\Translation::getLanguageQuery($code);
        foreach ($query as $code) {
            $result = $this->getTranslation($code, true);
            if (isset($result)) {
                break;
            }
        }

        return $result;
    }
}
