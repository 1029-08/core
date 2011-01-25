{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Verify gift certificate
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<widget template="modules/CDev/GiftCertificates/verify.tpl" />

{if:gcid&isFound()}
  <table cellspacing="0" class="form-table gc-info">

    <tr>
      <td class="label">To:</td>
      <td class="strong">{foundgc.recipient:h}</td>
    </tr>

    <tr>
      <td class="label">From:</td>
      <td class="strong">{foundgc.purchaser:h}</td>
    </tr>

    <tr class="amount">
      <td class="label">Amount:</td>
      <td>
        <span class="strong">{price_format(foundgc,#debit#):h}</span>
        <span class="gc-comment">(initial amount {price_format(foundgc,#amount#):h})</span>
      </td>
    </tr>

    <tr>
      <td class="label">Number:</td>
      <td>{foundgc.gcid:h}</td>
    </tr>

    <tr>
      <td class="label">Status:</td>
      <td>{getStatus()}</td>
    </tr>

  </table>

  {if:canApply()}
    <widget class="\XLite\Module\CDev\GiftCertificates\View\Form\GiftCertificate\Apply" name="apply_form" className="apply-gc" />
      <widget class="\XLite\View\Button\Submit" label="Redeem certificate" />
    <widget name="apply_form" end />
  {end:}

{else:}

  <strong IF="gcid">Gift certificate is not found</strong>

{end:}
