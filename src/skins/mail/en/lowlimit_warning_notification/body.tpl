{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * ____file_title____
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *}
<html>
<body>
<p>Warning message from {config.Company.company_name}

<p>
Product ID# {product.product_id}<br>
SKU: {product.sku:h}<br>
Product name: {product.name:h}<br>
<widget module="CDev\ProductOptions" template="modules/CDev/ProductOptions/selected_options.tpl" IF="product.productOptions"/>
<br>
{amount} item(s) in stock<br>
<br>

<p>{signature:h}
</body>
</html>
