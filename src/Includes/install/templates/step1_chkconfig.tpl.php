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
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage View
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

/*
 * Output a configuration checking page body
 */

if (!defined('XLITE_INSTALL_MODE')) {
    die('Incorrect call of the script. Stopping.');
}

?>

<CENTER>
<SCRIPT language="javascript">showWaitingAlert(true, '');</SCRIPT>
<TABLE width="100%" border=0 cellspacing=0 cellpadding=2>

<TR valign=top>
 <TD width="50%" rowspan=2>

<TABLE width="100%" border=0 cellspacing=0 cellpadding=4>

<?php

    // Go through sections list...
    foreach ($sections as $sectionCode => $sectionTitle) {

    // Index for colouring table rows
    $clrNumber = 0;

?>

 <TR class="Clr<?php echo $clrNumber; $clrNumber = ($clrNumber == 2) ? 1 : 2; ?>">
 <TD colspan=4 align=left><B><?php echo $sectionTitle ; ?></B></TD>
 </TR>

<?php

        // Go through steps list...
        foreach ($steps as $stepData) {
        
            // Display only steps of current section
            if ($stepData['section'] != $sectionCode) {
                continue;
            }

?>

 <TR class="Clr<?php echo $clrNumber; $clrNumber = ($clrNumber == 2) ? 1 : 2; ?>">
  <TD align=center><B><?php echo $stepData['title']; ?></B></TD>
  <TD width="1%">&nbsp;</TD>
  <TD width="1%" align=center><B><?php echo xtr('Status'); ?></B></TD>
  <TD width="1%" align=center>&nbsp;</TD>
 </TR>

<?php

            // Go through requirements list of current step...
            foreach ($stepData['requirements'] as $reqName) {

                $reqData = $requirements[$reqName];
                $errorsFound = ($errorsFound || (!$reqData['status'] && $reqData['critical']));
                $warningsFound = ($warningsFound || (!$reqData['status'] && !$reqData['critical']));

?>

 <TR class="Clr<?php echo $clrNumber; $clrNumber = ($clrNumber == 2) ? 1 : 2; ?>">
 <TD nowrap><?php echo $reqData['title']; ?> ... <?php echo $reqData['value']; ?></TD>
  <TD width="1%">-</TD>
  <TD width="1%" align=center><?php echo isset($reqData['skipped']) ? status_skipped() : status($reqData['status'], $reqName); ?></TD>
  <TD width="1%" align=center>&nbsp;</TD>
 </TR>
	 
<?php
            } // foreach ($stepData['requirements']...
        } // foreach ($steps...

?>

 <TR>
 <td colspan="4">&nbsp;</td>
 </TR>

<?php

    } // foreach ($sections...

?>

</TABLE>
</TD>

<TD width=25 rowspan=2>&nbsp;</TD> 

<TD width="50%" valign=top>
<SCRIPT language="javascript">showWaitingAlert(false, '');</SCRIPT>
<TABLE id="status" border=0 cellpadding=0 cellspacing=0>
<TR>
    <TD valign=top>

    <div id="lc_loopback" style="display : none">
    <font class="ErrorTitle"><?php echo xtr('loopback_test_failed', array(':host' => $_SERVER["HTTP_HOST"])); ?>
    </div>

    <div id="lc_php_version" style="display : none">
    <font class="ErrorTitle"><?php echo xtr('php_version_failed', array(':phpver' => phpversion())); ?>
    </div>
<?php
        $info = get_info();
?>
    <div id="lc_php_safe_mode" style="display : none">
    <font class="ErrorTitle">Dependency failed: Safe Mode is ON</font>
    <br><br>
	Safe Mode must be turned off for correct operation of LiteCommerce application.

    <p>To disable Safe Mode: 
    
    <p><b>1. If you have access to php.ini file</b>
    <br><br>
    Locate the
    <br><br>
    <font style="background-color: #E3EAEF;"><?php print $info["php_ini_path"] ?></font>
    <br><br>
    file, find and edit the following line within the file:
    <br><br>
    <font style="background-color: #E3EAEF;">safe_mode = "On"</font>
    <br><br>
    change to:
    <br><br>
    <font style="background-color: #E3EAEF;">safe_mode = "Off"</font>
    <br><br>
    Save the file, then restart your web server application for the changes to take effect.

    <p><b>2. If you do not have access to php.ini file</b>
    <br><br>
    Please contact the support services of your hosting provider to adjust this parameter.
    <br><br>
    </div>

<div id="lc_php_open_basedir" style="display : none">
<font class="ErrorTitle"><?php echo xtr('Non-critical dependency failed'); ?></font>
<br><br>
For Curl, openSSL and other external applications to work correctly with LiteCommerce, the value of open_basedir restriction variable in php.ini file must be empty or contain a valid path to external applications. A good solution is to add a valid path to external applications to the system 'PATH' variable.
<p>To adjust this parameter:
<p><b>1. If you have access to php.ini file</b>
<br><br>
Locate the
<br><br>
<font style="background-color: #E3EAEF;"><?php print $info["php_ini_path"] ?></font>
<br><br>
file, find and edit the following line within the file:<br><br>
<font style="background-color: #E3EAEF;">open_basedir = "/usr/local/php"</font>&nbsp;(for example)
<br><br>
change to:
<br><br>
<font style="background-color: #E3EAEF;">open_basedir = ""</font>
<br><br>
Save the file, then restart your web server application for the changes to take effect.

<p><b>2. If you do not have access to php.ini file</b>
<br><br>
Please contact the support services of your hosting provider to adjust this parameter.
<br><br>
</div>

 <div id="lc_php_sql_safe_mode" style="display : none">
    <font class="ErrorTitle">Dependency failed: sql.safe_mode is ON</font>
    <br><br>
	sql.safe_mode option must be turned off for correct operation of LiteCommerce application.

    <p>To disable sql.safe_mode: 
    
    <p><b>1. If you have access to php.ini file</b>
    <br><br>
    Locate the
    <br><br>
    <font style="background-color: #E3EAEF;"><?php print $info["php_ini_path"] ?></font>
    <br><br>
    file, find and edit the following line within the file:
    <br><br>
    <font style="background-color: #E3EAEF;">sql.safe_mode = "On"</font>
    <br><br>
    change to:
    <br><br>
    <font style="background-color: #E3EAEF;">sql.safe_mode = "Off"</font>
    <br><br>
    Save the file, then restart your web server application for the changes to take effect.

    <p><b>2. If you do not have access to php.ini file</b>
    <br><br>
    Please contact the support services of your hosting provider to adjust this parameter.
    <br><br>
</div>


<div id="lc_php_magic_quotes_sybase" style="display : none">
    <font class="ErrorTitle">Dependency failed: magic_quotes_sybase is ON</font>
    <br><br>
	magic_quotes_sybase option must be turned off for correct operation of LiteCommerce application.

    <p>To disable magic_quotes_sybase: 
    
    <p><b>1. If you have access to php.ini file</b>
    <br><br>
    Locate the
    <br><br>
    <font style="background-color: #E3EAEF;"><?php print $info["php_ini_path"] ?></font>
    <br><br>
    file, find and edit the following line within the file:
    <br><br>
    <font style="background-color: #E3EAEF;">magic_quotes_sybase = "On"</font>
    <br><br>
    change to:
    <br><br>
    <font style="background-color: #E3EAEF;">magic_quotes_sybase = "Off"</font>
    <br><br>
    Save the file, then restart your web server application for the changes to take effect.

    <p><b>2. If you do not have access to php.ini file</b>
    <br><br>
    Please contact the support services of your hosting provider to adjust this parameter.
    <br><br>
</div>

<div id="lc_php_allow_url_fopen" style="display : none">
    <font class="ErrorTitle"><?php echo xtr('Non-critical dependency failed'); ?></font>
    <br><br>
	For LiteCommerce application to work correctly, the value of allow_url_fopen variable in php.ini file must be "On".

    <p>To adjust this parameter:

    <p><b>1. If you have access to php.ini file</b>
    <br><br>
    Locate the
    <br><br>
    <font style="background-color: #E3EAEF;"><?php print $info["php_ini_path"] ?></font>
    <br><br>
    file, find and edit the following line within the file:
	<br><br>
    <font style="background-color: #E3EAEF;">allow_url_fopen = "Off"</font>
    <br><br>
    change to:
    <br><br>
    <font style="background-color: #E3EAEF;">allow_url_fopen = "On"</font>
    <br><br>
    Save the file, then restart your web server application for the changes to take effect.

    <p><b>2. If you do not have access to php.ini file</b>
    <br><br>
    Please contact the support services of your hosting provider to adjust this parameter.
	<br><br>
</div>

<div id="lc_php_memory_limit" style="display : none">
<font class="ErrorTitle">Dependency failed: memory_limit</font>
    <br><br>
    For LiteCommerce application to work correctly, the value of memory_limit variable in php.ini file must be &gt;= <?php echo constant('LC_PHP_MEMORY_LIMIT_MIN'); ?>.

    <p>To adjust this parameter:

    <p><b>1. If you have access to php.ini file</b>
    <br><br>
    Locate the
    <br><br>
    <font style="background-color: #E3EAEF;"><?php print $info["php_ini_path"] ?></font>
    <br><br>
    file, find and edit the following line within the file:
    <br><br>
    <font style="background-color: #E3EAEF;">memory_limit = <?php echo $requirements['lc_php_memory_limit']['value']; ?></font>
    <br><br>
    change to:
    <br><br>
    <font style="background-color: #E3EAEF;">memory_limit = <?php echo constant('LC_PHP_MEMORY_LIMIT_MIN'); ?></font>
    <br><br>
    Save the file, then restart your web server application for the changes to take effect.

    <p><b>2. If you do not have access to php.ini file</b>
    <br><br>
    Please contact the support services of your hosting provider to adjust this parameter.
    <br><br>
</div>

<div id="lc_mem_allocation" style="display: none">
<font class="ErrorTitle"><?php echo xtr('Non-critical dependency failed'); ?></font>
<br><br>
The configuration of the server where LiteCommerce will be installed meets the Server requirements, however some server software issues have been identified which can impair LiteCommerce operation.
<br><br>
Please contact our support team for further investigation.
</div>

<div id="lc_recursion_test" style="display : none">
<font class="ErrorTitle"><?php echo xtr('Non-critical dependency failed'); ?></font>
<br><br>
The configuration of the server where LiteCommerce will be installed meets the Server requirements, however some server software issues have been identified which can impair LiteCommerce operation.
<br><br>
Please contact our support team for further investigation.
</div>

<div id="lc_php_disable_functions" style="display : none">
<font class="ErrorTitle">Dependency failed: disabled functions</font>
    <br><br>
    For LiteCommerce application to work correctly, the value of disable_functions variable in php.ini file must be empty.
    <p>To adjust this parameter:
    <p><b>1. If you have access to php.ini file</b>
    <br><br>
    Locate the
    <br><br>
    <font style="background-color: #E3EAEF;"><?php print $info["php_ini_path"] ?></font>
    <br><br>
    file, find and edit the following line within the file:
    <br><br>
    <font style="background-color: #E3EAEF;">disable_functions = <?php echo @ini_get("disable_functions"); ?></font>
    <br><br>
    change to:
    <br><br>
    <font style="background-color: #E3EAEF;">disable_functions = <?php echo getAllowedDisableFunctionsValue(); ?></font>
    <br><br>
    Save the file, then restart your web server application for the changes to take effect.
    <p><b>2. If you do not have access to php.ini file</b>
    <br><br>
    Please contact the support services of your hosting provider to adjust this parameter.
    <br><br>
</div>

<div id="lc_php_file_uploads" style="display : none">
<font class="ErrorTitle">Dependency failed: file uploads</font>
    <br><br>
    For LiteCommerce application to work correctly, the value of file_uploads variable in php.ini file must be 1.
    <p>To adjust this parameter:
    <p><b>1. If you have access to php.ini file</b>
    <br><br>
    Locate the
    <br><br>
    <font style="background-color: #E3EAEF;"><?php print $info["php_ini_path"] ?></font>
    <br><br>
    file and set:
    <br><br>
    <font style="background-color: #E3EAEF;">file_uploads = 1</font>
    <br><br>
    Save the file, then restart your web server application for the changes to take effect.
    <p><b>2. If you do not have access to php.ini file</b>
    <br><br>
    Please contact the support services of your hosting provider to adjust this parameter.
    <br><br>
</div>

<div id="lc_php_mysql_support" style="display : none">
<font class="ErrorTitle">Dependency failed: MySQL support</font>
    <br><br>
    For LiteCommerce application to work with a database, MySQL support must be enabled.
    <br><br>
    Please contact the support services of your hosting provider to adjust this parameter.
</div>

<div id="lc_php_upload_max_filesize" style="display : none">
<font class="ErrorTitle"><?php echo xtr('Non-critical dependency failed'); ?></font>
<br><br>
The configuration of the server where LiteCommerce will be installed meets the Server requirements, however some server software issues have been identified which can impair LiteCommerce operation.
    <br><br>
	For LiteCommerce application to work correctly, the value of upload_max_filesize variable in php.ini file should contain the maximum size of the files allowed to be uploaded.
    <p>To adjust this parameter:
    <p><b>1. If you have access to php.ini file</b>
    <br><br>
    Locate the
    <br><br>
    <font style="background-color: #E3EAEF;"><?php print $info["php_ini_path"] ?></font>
    <br><br>
    file and set, for example:
    <br><br>
    <font style="background-color: #E3EAEF;">upload_max_filesize = 2M</font>
    <br><br>
    Save the file, then restart your web server application for the changes to take effect.
    <p><b>2. If you do not have access to php.ini file</b>
    <br><br>
    Please contact the support services of your hosting provider to adjust this parameter.
    <br><br>
</div>

<div id="lc_test_http_post" style="display : none">
<font class="ErrorTitle"><?php echo xtr('Non-critical dependency failed'); ?></font>
<br><br>
The configuration of the server where LiteCommerce will be installed makes sending POST requests to external servers impossible. Please contact the support services of your hosting provider to adjust this parameter.
</div>

<div id="lc_file_permissions" style="display : none">
<font class="ErrorTitle">Critical dependency failed</font>
<br><br>
<?php
echo $requirements['lc_file_permissions']['description'];
?>
</div>

<div id="detailsElement">

</div>

<div id="lc_php_gdlib" style="display : none">
<font class="ErrorTitle"><?php echo xtr('Non-critical dependency failed'); ?></font>
<br><br>
GDLib 2.0 or better required for automatic generation of product thumbnails form product images and for some other modules. GDLib must be compiled with libJpeg (ensure that PHP is configured with the option --with-jpeg-dir=DIR, where DIR is the directory where libJpeg is installed). Please contact the support services of your hosting provider to adjust this parameter.
</div>

<div id="lc_php_phar" style="display : none">
<font class="ErrorTitle"><?php echo xtr('Non-critical dependency failed'); ?></font>
<br><br>
Phar extension is required to install external LiteCommerce addons from marketplace. Please contact the support services of your hosting provider to adjust this parameter.
</div>

<div id="lc_https_bouncer" style="display : none">
<font class="ErrorTitle"><?php echo xtr('Non-critical dependency failed'); ?></font>
<br><br>
libCURL module with HTTPS protocol support and an SSL certificate required, if you want to process credit cards using Authorize.NET, PayPal or other payment gateways, or use real time shipping calculation services (these services require that your site accepts secure connections via HTTPS/SSL protocol). Please contact the support services of your hosting provider to adjust this parameter.
</div>

<div id="lc_xml_support" style="display : none">
<font class="ErrorTitle"><?php echo xtr('Non-critical dependency failed'); ?></font>
<br><br>
Xml/EXPAT and DOMDocument extensions for PHP are required for real-time shipping modules as well as for a payment modules. Please contact the support services of your hosting provider to adjust this parameter.
</div>


<div style="display: none; padding-top: 50px; padding-left: 50px;" id="test_passed_icon">
<img src="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/skins_original/admin/en/images/test_passed.gif" border="0" />
</div>

<script type="text/javascript">
    var first_code = '<?php echo ($first_error) ? $first_error : ''; ?>';
    showDetails(first_code);
</script>

    </TD>
</TR>
</TABLE>

</TD>

</TR>

<TR>
<TD valign=bottom>

<TABLE  id="status_report" border=0 width=100% valign=bottom style="display: none;" class="TableTop" cellpadding=2 cellspacing=2>
<TR>
<TD>
<TABLE width=100% class="Clr2" cellpadding=2 cellspacing=2>
<TR>
<TD>
<TABLE width=100% class="TableTop" cellpadding=2 cellspacing=2>
<TR>
    <TD valign=middle nowrap><IMG src="skins_original/default/en/images/code.gif"></TD>
    <TD valign=middle width=100%><?php echo xtr('requirements_failed_text'); ?></TD>
    <TD valign=middle nowrap><input type="button" value="<?php echo xtr('Send report'); ?>" onclick="javascript:window.open('install.php?target=install&action=send_report&ruid=<?php echo $report_uid; ?>','SEND_REPORT','toolbar=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no,direction=no');"></TD>
</TR>
</TABLE>
</TD>
</TR>
</TABLE>
</TD>
</TR>
</TABLE>

</TD>
</TR>

<?php
    
    if (!$requirements['lc_file_permissions']['status']) {

?>

<TR>
<TD colspan=3>
<BR><BR>
<P>
<?php $requirements['lc_file_permissions']['description'] ?>
</P>

</TD>
</TR>

<?php

    }

?>

</TABLE>

<?php

	// Save report to file if errors found
	if ($errorsFound) {

		$report = make_check_report($requirements);

		if (@file_exists($reportFName) && !@is_writeable($reportFName)) {
			@chmod($reportFName, 0755);
        }

		$report_saved = false;
		$handle = @fopen($reportFName, "wb");
		if ($handle) {
			@fwrite($handle, $report);
			@fclose($handle);
			$report_saved = true;
		}
?>
        
        <SCRIPT language="javascript">visibleBox("status_report", true);</SCRIPT>

<?php

	}

    if (!$errorsFound) {

        echo "<br />";
        message(xtr('Push the \'Next\' button below to continue'));

        if ($warningsFound) {

?>

<BR>
<P><?php echo xtr('requirement_warning_text'); ?></P>
<label><INPUT type="checkbox" onClick="javascript: setNextButtonDisabled(!this.checked);">&nbsp;<?php echo xtr('Yes, I want to continue the installation.'); ?></label>

<?php 
        } 
    }
?>

</CENTER>
 
<BR>


