{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * ____file_title____
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<p IF="status=#updated#" class="success-message">&gt;&gt; Mail templates have been updated successfully &lt;&lt;</p>

<p class="admin-head">{mailTemplates.body.comment}</p>

<form action="admin.php" method="POST">
<input type="hidden" name="target" value="template_editor">
<input type="hidden" name="action" value="update_mail">
<input type="hidden" name="editor" value="mail">
<input type="hidden" name="path" value="{path}">
<input type="hidden" name="node" value="{node}">
<input type="hidden" name="returnUrl" value="{url}&mode=edit&path={path}&node={node}&status=updated"/>

<table border="0" cellpadding=2 cellspacing=2 width="430">
<tr>
<td>
	<table border="0" cellpadding=0 cellspacing=0 width=100%>
    <tr>
    	<td width=50%><img src="images/letter.gif" border="0" align="top" alt="" />&nbsp;<b>Mail templates folder:</b><br /><i>{node}</i></td>
    	<td width=50% align=right><a href="{url}"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle" alt="" /> Go back</a></td>
	</tr>
	</table>
</td>
</tr>
<tr>
<td>
&nbsp;
</td>
</tr>
</table>

<b>Subject:</b><span class="error-message" IF="subjectWriteError">&nbsp;WARNING! File cannot be overwritten! Please check and correct file permissions.</span><br />
<input type="text" name="subject" value="{mailTemplates.subject.content}" size="81">
<br /><br />
<b>Body:</b><span class="error-message" IF="bodyWriteError">&nbsp;WARNING! File cannot be overwritten! Please check and correct file permissions.</span><br />
<textarea name="body" cols="81" rows="20">{mailTemplates.body.content}</textarea>
<br /><br />
<b>Signature:</b><span class="error-message" IF="signatureWriteError">&nbsp;WARNING! File cannot be overwritten! Please check and correct file permissions.</span><br />
<textarea name="signature" cols="81" rows="7">{mailTemplates.signature.content}</textarea>
<p>

<table border="0" cellpadding=2 cellspacing=2 width="430">
<tr>
<td align=left>
<widget class="\XLite\View\Button\Submit" label=" Save " style="main-button" />
</td>
<td align=right>
<widget class="\XLite\View\Button\Regular" name="cancel" label="Cancel" jsCode="javascript: document.location='{url}'" />
</td>
</tr>
</table>

</form>
