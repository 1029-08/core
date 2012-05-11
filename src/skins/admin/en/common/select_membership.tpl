{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Membership selection template
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *}
<select {if:!nonFixed}class="fixed-select"{end:} name="{getParam(#field#)}">
  <option value="%" IF="{getParam(#allOption#)}" selected="{isSelected(#%#,value)}">{t(#All membership levels#)}</option>
  <option value="" selected="{isSelected(##,value)}">{t(#No membership#)}</option>
  <option value="pending_membership" IF="{getParam(#pendingOption#)}" selected="{isSelected(#pending_membership#,getParam(#value#))}">{t(#Pending membership#)}</option>
  <option FOREACH="getMemberships(),membership" value="{membership.getMembershipId()}" selected="{isSelected(membership.getMembershipId(),getParam(#value#))}">{membership.getName()}</option>
</select>
