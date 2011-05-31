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

namespace XLite\Module\CDev\DrupalConnector\Drupal;

/**
 * Block
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class Block extends \XLite\Module\CDev\DrupalConnector\Drupal\ADrupal
{
    /**
     * Get block content from LC (if needed)
     *
     * @param array     &$data An array of data, as returned from the hook_block_view()
     * @param \stdClass $block The block object, as loaded from the database
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function setBlockContent(array &$data, \stdClass $block)
    {
        // Check if current block is an LC one
        if ($blockInfo = \XLite\Module\CDev\DrupalConnector\Drupal\Model::getInstance()->getBlock($block->delta)) {

            // Trying to get widget from LC
            if ($widget = $this->getHandler()->getWidget($blockInfo['lc_class'], $blockInfo['options'])) {

                // Check if widget is visible and its content is not empty
                if ($widget->checkVisibility() && ($content = $widget->getContent())) {

                    // Set content recieved from LC
                    $data['content'] = $content;

                    // Register JS and/or CSS
                    if ($widget->getProtectedWidget()) {
                        $this->registerResources($widget->getProtectedWidget());
                    }

                } else {

                    // Block is not visible
                    $data['content'] = null;
                }
            }
        }

        return true;
    }
}
