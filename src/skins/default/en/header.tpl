{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Page head
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}

<head>
  <title>{if:getPageTitle()}{getPageTitle()}{end:}</title>
  <meta http-equiv="Content-Type" content="text/html; charset={charset}" />
  <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
  <meta IFF="!metaDescription" name="description" content="The powerful shopping cart software for web stores and e-commerce enabled stores is based on PHP5 with SQL database with highly configurable implementation based on templates." />
  <meta IFF="metaDescription" name="description" content="{metaDescription:r}" />
  <meta IFF="keywords" name="keywords" content="{keywords:r}" />

  <link href="{%\XLite\Model\Layout::getInstance()->getSkinURL('style.css')%}" rel="stylesheet" type="text/css" />
  <link FOREACH="getCSSResources(),file" href="{file}" rel="stylesheet" type="text/css" />

  <script FOREACH="getJSResources(),file" type="text/javascript" src="{file}"></script>
</head>
