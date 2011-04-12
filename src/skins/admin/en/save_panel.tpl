{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Save panel
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *}
<div class="save-panel" style="display: none;">
  <div class="box">
    <widget class="\XLite\View\Button\Regular" label="Save" style="main invert" jsCode="return true;" />
    <a href="javascript:void(0);"><span>Delete selected</span></a>
  </div>
</div>
<script type="text/javascript">
var savePanelFormName = '{getParam(#formName#)}';
var savePanelDeleteAction = 'delete_labels';
</script>

