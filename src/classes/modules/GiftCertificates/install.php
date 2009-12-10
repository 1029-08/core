<?php

/**
* @package Module_GiftCertificates
* @access public
* @version $Id: install.php,v 1.12 2008/05/22 08:33:03 vgv Exp $
*/

// module installation code

if (!function_exists("file_put_contents")) 
{
    function file_put_contents($file, $content) 
    {
        if (file_exists($file)) 
        {
            unlink($file);
        }
        $fp = fopen($file, "wb") or die("write failed for $file");
        fwrite($fp, $content);
        fclose($fp);
        @chmod($file, 0666);
    }
}

if (!function_exists("file_get_contents")) 
{
    function file_get_contents($f) 
    {
        ob_start();
        $retval = @readfile($f);
        if (false !== $retval) 
        { 
        	// no readfile error
            $retval = ob_get_contents();
        }
        ob_end_clean();
        return $retval;
    }
}

if (!function_exists("start_patching"))
{
	function start_patching($title)
	{
	?>
</PRE>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<TITLE><?php echo $title; ?> installation steps</TITLE>
<STYLE type="text/css">
BODY,P,DIV {FONT-FAMILY: Verdana, Arial, Helvetica, Sans-serif; COLOR: #000000; FONT-SIZE: 12px;}
TH,TD {FONT-FAMILY: Verdana, Arial, Helvetica, Sans-serif; COLOR: #000000; FONT-SIZE: 10px;}
PRE {FONT-FAMILY: Courier, "Courier New"; COLOR: #000000; FONT-SIZE: 12px;}
.Head {BACKGROUND-COLOR: #CDD9E1;}
.Center {BACKGROUND-COLOR: #FFFFFF;}
.Middle {BACKGROUND-COLOR: #EFEFEF;}
</STYLE>
</HEAD>
<BODY bgcolor=#FFFFFF link=#0000FF alink=#4040FF vlink=#800080>
<TABLE border=0 cellpadding=3 cellspacing=2>
<TR class="Head">
<TD nowrap><B>&nbsp;&nbsp;Modifying templates ...&nbsp;</TD>
<TD nowrap><B>&nbsp;&nbsp;Status&nbsp;</TD>
</TR>
	<?php
		global $patching_table_row;
		$patching_table_row = 0;
	}
}

if (!function_exists("end_patching"))
{
	function end_patching()
	{
	?>
</TABLE>
<P>
</BODY>
</HTML>
<PRE>
<?php
	}
}

if (!function_exists("is_template_patched"))
{
	function is_template_patched($location, $check_str)
	{
        $src = @file_get_contents($location);
        return (strpos($src, $check_str) === false) ? false : true;
	}
}

if (!function_exists("already_patched"))
{
	function already_patched($location)
	{
		global $patching_table_row;
	    echo "<TR class=\"" . (($patching_table_row) ? "Middle" : "Center") . "\"><TD nowrap>&nbsp;$location&nbsp;</TD><TD nowrap>&nbsp;";
		echo "<FONT COLOR=\"#0000FF\"><B>already patched</B></FONT>";
	    echo "&nbsp;</TD></TR>\n";
	    $patching_table_row = ($patching_table_row) ? 0 : 1;
	}
} 

if (!function_exists("patch_template"))
{
	function patch_template($location, $check_str=null, $find_str=null, $replace_str=null, $add_str=null)
	{
		global $patching_table_row;
	    echo "<TR class=\"" . (($patching_table_row) ? "Middle" : "Center") . "\"><TD nowrap>&nbsp;$location&nbsp;</TD></TD><TD nowrap>&nbsp;";

		$src = @file_get_contents($location);
		$src = preg_replace("/\r\n/m","\n", $src);
	    if (!isset($check_str) || strpos($src, $check_str) === false) 
	    {
	    	$replace_message = "";
	    	if (isset($find_str) && isset($replace_str))
    		{
    			$old_src = $src;
				$src = str_replace($find_str, $replace_str, $src);
				if (strcmp($old_src, $src) == 0)
				{
					$replace_message = "<FONT COLOR=red><B>&nbsp;(replace failed)&nbsp;</B></FONT>";
				}
    		}
		
       	 	if (isset($add_str))
       	 	{
       	 		$src .= $add_str;
       	 	}
    	
       	 	file_put_contents($location, $src);
       	 	echo "<FONT COLOR=green><B>success</B></FONT>$replace_message";
       	} 
       	else 
       	{
       		echo "<FONT COLOR=\"#0000FF\"><B>already patched</B></FONT>";
    	}
       	echo "&nbsp;</TD></TR>\n";
		$patching_table_row = ($patching_table_row) ? 0 : 1;
	}
}

if (!function_exists("copy_schema_template"))
{
	function copy_schema_template($template, $schema, $module, $zone = "default", $locale = "en")
	{
		global $patching_table_row;
		if (empty($schema) || in_array($schema, array("3-columns_classic", "3-columns_modern", "2-columns_classic", "2-columns_modern"))) $schema = "standard";

	    echo "<TR class=\"" . (($patching_table_row) ? "Middle" : "Center") . "\"><TD nowrap>&nbsp;Replacing&nbsp;$template&nbsp;for&nbsp;<b>$schema</b>&nbsp;skin</TD><TD nowrap>&nbsp;";
	    $patching_table_row = ($patching_table_row) ? 0 : 1;

		$from = "skins/$zone/$locale/modules/$module/schemas/templates/$schema/$zone/$locale/modules/$module/$template";
		$to = "skins/$zone/$locale/modules/$module/$template";

		if (file_exists($from)) {
			if (@copy($from, $to)) {
				echo "<FONT COLOR=\"green\"><B>success</B></FONT>";
			} else {
				echo "<FONT COLOR=\"red\"><B>failed</B></FONT>";
			}
		} else {
			echo "<FONT COLOR=\"blue\"><B>skipped</B></FONT>";
		}
	    echo "&nbsp;</TD></TR>\n";
	}
}

$MODULE_NAME = "GiftCertificates";
start_patching($MODULE_NAME);

if (is_object($this)) {
	$schema = (!empty($this->layout))?$this->layout:($this->xlite->get("config.Skin.skin"));
}

//////////////////////////////////////
//	COPY SKIN-DEPENDENT TEMPLATES
//////////////////////////////////////

copy_schema_template("menu/body.tpl", $schema, $MODULE_NAME);
copy_schema_template("check_gift_certificate.tpl", $schema, $MODULE_NAME);
copy_schema_template("add_gift_certificate.tpl", $schema, $MODULE_NAME);

//////////////////////////////////////
//  ADMIN ZONE
//////////////////////////////////////

// patching "skins/admin/en/main.tpl"
$location = "skins/admin/en/main.tpl";
$check_str = 'modules/GiftCertificates/select_ecard.tpl';
$find_str = <<<EOT
<widget module="GiftCertificates" target="gift_certificate_select_ecard" class="CECardSelect" body="modules/GiftCertificates/ecard_select.tpl" head="Select e-Card" name="ecardSelectForm">
EOT;
$replace_str = <<<EOT
<widget module="GiftCertificates" target="gift_certificate_select_ecard" template="common/dialog.tpl" body="modules/GiftCertificates/select_ecard.tpl" head="Select e-Card">
EOT;
patch_template($location, $check_str, $find_str, $replace_str);

//////////////////////////////////////
//	CUSTOMER ZONE
//////////////////////////////////////

// patching "skins/default/en/main.tpl"
$location = "skins/default/en/main.tpl";
$check_str = 'modules/GiftCertificates/select_ecard.tpl';
$find_str = <<<EOT
<widget module="GiftCertificates" target="gift_certificate_ecards" class="CECardSelect" head="Select e-Card" name="ecardSelectForm">
EOT;
$replace_str = <<<EOT
<widget module="GiftCertificates" target="gift_certificate_ecards" template="common/dialog.tpl" body="modules/GiftCertificates/select_ecard.tpl" head="Select e-Card">
EOT;
patch_template($location, $check_str, $find_str, $replace_str);

// patching "skins/default/en/main.tpl"
$location = "skins/default/en/main.tpl";
$check_str = 'modules/GiftCertificates/gift_certificate_info.tpl';
$find_str = <<<EOT
<widget module="GiftCertificates" target="check_gift_certificate" template="common/dialog.tpl" body="modules/GiftCertificates/check_gift_certificate.tpl" head="Verify gift certificate">
EOT;
$replace_str = <<<EOT
<widget module="GiftCertificates" target="check_gift_certificate" template="common/dialog.tpl" body="modules/GiftCertificates/check_gift_certificate.tpl" head="Verify gift certificate">
<widget module="GiftCertificates" target="gift_certificate_info" template="common/dialog.tpl" body="modules/GiftCertificates/gift_certificate_info.tpl" head="Gift certificate">
EOT;
patch_template($location, $check_str, $find_str, $replace_str);

// patching "cart.html"
$location = "cart.html";
$check_str = 'modules/GiftCertificates/gift_certificate_info.tpl';
$find_str = <<<EOT
<widget module="GiftCertificates" target="check_gift_certificate" template="common/dialog.tpl" body="modules/GiftCertificates/check_gift_certificate.tpl" head="Verify gift certificate">
EOT;
$replace_str = <<<EOT
<widget module="GiftCertificates" target="check_gift_certificate" template="common/dialog.tpl" body="modules/GiftCertificates/check_gift_certificate.tpl" head="Verify gift certificate">
<widget module="GiftCertificates" target="gift_certificate_info" template="common/dialog.tpl" body="modules/GiftCertificates/gift_certificate_info.tpl" head="Gift certificate">
EOT;
patch_template($location, $check_str, $find_str, $replace_str);

// patching "skins/default/en/profile.tpl"
$location = "skins/default/en/profile.tpl";
$check_str = 'modules/GiftCertificates/active_certificates.tpl';
$find_str = <<<EOT
<widget module="Newsletters" template="modules/Newsletters/subscription_form.tpl">
EOT;
$replace_str = <<<EOT
<widget module="Newsletters" template="modules/Newsletters/subscription_form.tpl">
<widget module="GiftCertificates" template="modules/GiftCertificates/active_certificates.tpl">
EOT;
patch_template($location, $check_str, $find_str, $replace_str);

end_patching();

?>
