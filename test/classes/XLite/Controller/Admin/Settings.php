<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2009 Creative Development <info@creativedevelopment.biz>  |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URLs:                                                       |
|                                                                              |
| FOR LITECOMMERCE                                                             |
| http://www.litecommerce.com/software_license_agreement.html                  |
|                                                                              |
| FOR LITECOMMERCE ASP EDITION                                                 |
| http://www.litecommerce.com/software_license_agreement_asp.html              |
|                                                                              |
| THIS  AGREEMENT EXPRESSES THE TERMS AND CONDITIONS ON WHICH YOU MAY USE THIS |
| SOFTWARE PROGRAM AND ASSOCIATED DOCUMENTATION THAT CREATIVE DEVELOPMENT, LLC |
| REGISTERED IN ULYANOVSK, RUSSIAN FEDERATION (hereinafter referred to as "THE |
| AUTHOR")  IS  FURNISHING  OR MAKING AVAILABLE TO  YOU  WITH  THIS  AGREEMENT |
| (COLLECTIVELY,  THE "SOFTWARE"). PLEASE REVIEW THE TERMS AND  CONDITIONS  OF |
| THIS LICENSE AGREEMENT CAREFULLY BEFORE INSTALLING OR USING THE SOFTWARE. BY |
| INSTALLING,  COPYING OR OTHERWISE USING THE SOFTWARE, YOU AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE ACCEPTING AND AGREEING  TO  THE  TERMS  OF  THIS |
| LICENSE AGREEMENT. IF YOU ARE NOT WILLING TO BE BOUND BY THIS AGREEMENT,  DO |
| NOT  INSTALL  OR USE THE SOFTWARE. VARIOUS COPYRIGHTS AND OTHER INTELLECTUAL |
| PROPERTY  RIGHTS PROTECT THE SOFTWARE. THIS AGREEMENT IS A LICENSE AGREEMENT |
| THAT  GIVES YOU LIMITED RIGHTS TO USE THE SOFTWARE AND NOT AN AGREEMENT  FOR |
| SALE  OR  FOR TRANSFER OF TITLE. THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY |
| GRANTED  BY  THIS AGREEMENT.                                                 |
|                                                                              |
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* General Settings dialog
*
* @package Dialog
* @access public
* @version $Id$
*
*/
class XLite_Controller_Admin_Settings extends XLite_Controller_Admin_Abstract
{
    var $params = array('target', 'page');
    var $page = "General";
    var $_waiting_list = null;

    function handleRequest()
    {
        if($this->get("page") == "Captcha" && ($this->get("xlite.config.Security.captcha_protection_system") != "Y" || !$this->isGDLibLoaded())){
            $this->redirect("admin.php?target=settings");
        }

        parent::handleRequest();
    }

    function getSettings()
    {
        return new XLite_Model_Config();
    }

    function getPages()
    {
        $categories = $this->get("settings.categories");
        $names = $this->get("settings.categoryNames");
        $pages = array();
        for ($i = 0; $i < count($categories); $i++) {
            if((!$this->isGDLibLoaded() || $this->get("xlite.config.Security.captcha_protection_system") != "Y") && $categories[$i] == "Captcha")
                continue;
            $pages[$categories[$i]] = $names[$i];
        }
        return $pages;
    }

    function getOptions()
    {
        $settings = $this->get("settings");
        return $settings->getByCategory($this->page);
    }
	
	function check_https($https_client)	
	{
		$https = new XLite_Model_HTTPS();
		switch ($https_client) {
			case 'libcurl' : return $https->LibCurl_detect(); break;
			case 'curl'	  : return $https->Curl_detect(); break;
			case 'openssl' : !LC_OS_IS_WIN && func_find_executable("openssl") ? 2 : 1; break;
			default: return $https->AutoDetect() !== false ? 2 : 1;
		}
	}

    function isOpenBasedirRestriction()
    {
        $res = (string) @ini_get("open_basedir");
        return ($res != "");
    }
	
	function get($name) 
	{
		switch($name) {
            case 'phpversion' 	: return phpversion(); break;
            case 'timezone_changable' : return func_is_timezone_changable(); break;
			case 'os_type'		: list($os_type, $tmp) = split(" ", php_uname());
        						  return $os_type;
								  break;
			case 'mysql_server'	: return mysql_get_server_info(); break;
			case 'mysql_client'	: return mysql_get_client_info(); break;
			case 'root_folder'	: return getcwd(); break;
			case 'web_server'	: if(isset($_SERVER["SERVER_SOFTWARE"])) return $_SERVER["SERVER_SOFTWARE"]; else  return ""; break;
			case 'xml_parser'	: 	ob_start();
    								phpinfo(INFO_MODULES);
    								$php_info = ob_get_contents();
    								ob_end_clean();
    								if( preg_match('/EXPAT.+>([\.\d]+)/mi', $php_info, $m) )
        								return $m[1];
    								return function_exists("xml_parser_create")?"found":"";
									break;
            case 'gdlib'        :   
    								if (!$this->is("GDLibLoaded")) {
    									return "";
    								} else {
                                        ob_start();
                                        phpinfo(INFO_MODULES);
                                        $php_info = ob_get_contents();
                                        ob_end_clean();
                                        if (preg_match('/GD.+>([\.\d]+)/mi', $php_info, $m)) {
        									$gdVersion = $m[1];
            							} else {
    										$gdVersion = @gd_info();
    										if (is_array($gdVersion) && isset($gdVersion["GD Version"])) {
    											$gdVersion = $gdVersion["GD Version"];
    										} else {
    											$gdVersion = "unknown";
    										}
    									}
    									return "found (" . $gdVersion . ")";
    								}
									break;
                                  
			case 'lite_version'	: return $this->config->Version->version; break;
			case 'libcurl'		: 
									$libcurlVersion = curl_version();
									if (is_array($libcurlVersion)) {
										$libcurlVersion = $libcurlVersion["version"];
									}
									return $libcurlVersion;
			case 'curl'			: return $this->ext_curl_version(); break;
            case 'openssl'		: return $this->openssl_version(); break;
            case 'check_files'  :
                                    $result = array();
                                    $files = array("cart.html", "LICENSE");
                                    foreach ($files as $file) {
                                        $mode = $this->getFilePermission($file);
                                        $modeStr = $this->getFilePermissionStr($file);
                                        $res = array("file" => $file, "error" => "");
										if (!is_file($file)) {
											$res["error"] = "does_not_exist";
											$result[] = $res;
											continue;
										}
                                        $perm = substr(sprintf('%o', @fileperms($file)), -4);
                                        if($perm != $modeStr){
                                            if(!@chmod($file, $mode)){
                                                $res["error"] = "cannot_chmod";
                                                $result[] = $res;
                                                continue;
                                            }
                                        } else {
                                            if($this->get("xlite.suMode") != 0) {
                                                if(!@chmod($file, $mode)){
                                                    $res["error"] = "wrong_owner";
                                                    $result[] = $res;
                                                    continue;
                                                }
                                            }
                                        }
                                        $result[] = $res;
                                    }
                                    return $result;
			case 'check_dirs'	:
									$result = array();
									$dirs = array("var/run", "var/log", "var/html", "var/backup", "var/tmp", "catalog", "images", "classes/modules", "skins/default/en/modules", "skins/admin/en/modules", "skins/default/en/images/modules", "skins/admin/en/images/modules", "skins/mail/en/modules", "skins/mail/en/images/modules");
									foreach ($dirs as $dir) {
                                        $mode = $this->getDirPermission($dir);
                                        $modeStr = $this->getDirPermissionStr($dir);
										$res = array("dir" => $dir, "error" => "", "subdirs" => array());

										if (!is_dir($dir)) {
											$full_path = "";
											$path = explode("/", $dir);
											foreach ($path as $sub) {
												$full_path .= $sub."/";
												if (!is_dir($full_path)) {
													if (@mkdir($full_path, $mode) !== true )
														break;
												}
											}
										}

										if (!is_dir($dir)) {
											$res["error"] = "cannot_create";
											$result[] = $res;
											continue;
										}

                                        $perm = substr(sprintf('%o', @fileperms($dir)), -4);
                                        if($perm != $modeStr){
                                            if(!@chmod($dir, $mode)){
                                                $res["error"] = "cannot_chmod";
                                                $result[] = $res;
                                                continue;
                                            }
                                        } else {
                                            if($this->get("xlite.suMode") != 0 || strpos($dir, "var") !== false) {
                                                if(!@chmod($dir, $mode)){
                                                    $res["error"] = "wrong_owner";
                                                    $result[] = $res;
                                                    continue;
                                                }
                                            }
                                        }

                                        $subdirs = array();
                                        if($dir != "catalog" && $dir != "images"){
                                            $this->checkSubdirs($dir, $subdirs);
                                        }

                                        if(!empty($subdirs)){
                                            $res["error"] = "cannot_chmod_subdirs";
                                            $res["subdirs"] = $subdirs;
                                            $result[] = $res;
                                            continue;
                                        }

										$result[] = $res;
									}
									return $result;
									break;
			default 			: return parent::get($name);
		}	
	}

    function getDirPermission($dir)
    {
        global $options;

        if($this->get("xlite.suMode") == 0){
            if(strpos($dir, "var") === false){
                $mode = 0777;
            } else {
                $mode = isset($options['filesystem_permissions']['nonprivileged_permission_dir']) ? base_convert($options['filesystem_permissions']['nonprivileged_permission_dir'], 8, 10) : 0755;
            }
        } else {
            $mode = isset($options['filesystem_permissions']['privileged_permission_dir']) ? base_convert($options['filesystem_permissions']['privileged_permission_dir'],8, 10) : 0711;
        }

        return $mode;
    }

    function getDirPermissionStr($dir = '')
    {
        $mode = (int) $this->getDirPermission($dir);
        return (string) "0" . base_convert($mode, 10, 8);
    }

    function getFilePermission($file)
    {
        global $options;

        if($this->get("xlite.suMode") == 0){
            $mode = isset($options['filesystem_permissions']['nonprivileged_permission_file']) ? base_convert($options['filesystem_permissions']['nonprivileged_permission_file'], 8, 10) : 0644;
        } else {
            $mode = isset($options['filesystem_permissions']['privileged_permission_file']) ? base_convert($options['filesystem_permissions']['privileged_permission_file'],8, 10) : 0600;
        }

        return $mode;
    }

    function getFilePermissionStr($file = '')
    {
        $mode = (int) $this->getFilePermission($file);
        return (string) "0" . base_convert($mode, 10, 8);
    }

    function checkSubdirs($path, &$subdir_errors)
    {
        if (!is_dir($path))
            return;

        $mode = $this->getDirPermission($path);
        $modeStr = $this->getDirPermissionStr($path);

        $dh = @opendir($path);
        while (($file = @readdir($dh)) !== false) {
            if($file == '.' || $file == '..')
                continue;
            $fullpath = $path . DIRECTORY_SEPARATOR . $file;
            if(@is_dir($fullpath)) {
                $perm = substr(sprintf('%o', @fileperms($fullpath)), -4);
                if($perm != $modeStr){
                    if(!@chmod($fullpath, $mode)){
                        $subdir_errors[] = $fullpath;
                        continue;
                    }
                } else {
                    if($this->get("xlite.suMode") != 0 || strpos($dir, "var") !== false) {
                        if(!@chmod($fullpath, $mode)){
                            $subdir_errors[] = $fullpath;
                            continue;
                        }
                    }
                }

                $this->checkSubdirs($fullpath, $subdir_errors);
            }
        }
    }

    function getCheckFiles()
    {
        $htaccess = new XLite_Model_Htaccess();
        return $htaccess->checkEnvironment();
    }

    function action_update_htaccess()
    {
        $ids = (array) $this->get("ind");
        foreach($ids as $id => $v){
            $htaccess = new XLite_Model_Htaccess($id);
            $htaccess->reImage();
        }
    }

    function action_restore_htaccess()
    {
        $ids = (array) $this->get("ind");
        foreach($ids as $id => $v){
            $htaccess = new XLite_Model_Htaccess($id);
            $htaccess->restoreFile();
        }
    }

	function ext_curl_version()
	{
		$curlBinary = @func_find_executable("curl");
		@exec("$curlBinary --version", $output);
        $version = @$output[0];
		if(preg_match('/curl ([^ $]+)/', $version, $ver))
				return $ver[1];
		else 
				return "";	
	}  
	
	function openssl_version()
	{
		$opensslBinary = @func_find_executable("openssl");
		return @exec("$opensslBinary version");
	}

    function httpRequest($url_request)
    {
    	@ini_get('allow_url_fopen') or @ini_set('allow_url_fopen', 1);
    	$handle = @fopen ($url_request, "r");

    	$response = "";
    	if ($handle) {
    		while (!feof($handle)) {
    			$response .= fread($handle, 8192);
    		}

    		@fclose($handle);
    	} else {
    		global $php_errormsg;

			// FIXME - to delete?
    		$includes .= "." . DIRECTORY_SEPARATOR . "lib" . PATH_SEPARATOR;
    		$includes .= "." . DIRECTORY_SEPARATOR . PATH_SEPARATOR;
    		@ini_set("include_path", $includes);

    		$php_errormsg = "";
    		$_this->error = "";

    		require_once LC_ROOT_DIR . 'lib' . LC_DS . 'PEAR.php';
    		require_once LC_ROOT_DIR . 'lib' . LC_DS . 'HTTP' . LC_DS . 'Request.php';

    		$http = new HTTP_Request($url_request);
    		$http->_timeout = 3;
    		$track_errors = @ini_get("track_errors");
    		@ini_set("track_errors", 1);

    		$result = @$http->sendRequest();
    		@ini_set("track_errors", $track_errors);

    		if (!($php_errormsg || PEAR::isError($result))) {
    			$response = $http->getResponseBody();
    		} else {
    			return false;
    		}
    	}

    	return $response;
    }

	function getAnsweredVersion()
	{
		if (isset($this->_answeredVersion)) {
			return $this->_answeredVersion;
		}

		$checkUrl = $this->xlite->shopUrl("admin.php?target=upgrade&action=version");
		$this->_answeredVersionError = false;
		$response = $this->httpRequest($checkUrl);
		if ($this->get("lite_version") != $response) {
			$this->_answeredVersionError = true;
		}
		$this->_answeredVersion = $response;

		return $this->_answeredVersion;
	}

	function getAnsweredVersionError()
	{
		return $this->_answeredVersionError;
	}

	function action_phpinfo()
	{
		die(phpinfo());	
	} 
	
	function action_update()
    {
        $options = $this->get("options");
        for ($i=0; $i<count($options); $i++) {
            $name = $options[$i]->get("name");
            $type = $options[$i]->get("type");
            if ($type=='checkbox') {
                if (empty($_REQUEST[$name])) {
                    $val = 'N';
                } else {
                    $val = 'Y';
                }
            } elseif ($type == "serialized" && is_array($_POST[$name])) {
                $val = serialize($_POST[$name]);
            } else {
                $val = trim($_REQUEST[$name]);
            }

            if($name == "captcha_length"){
                $val = (int) $val;
                if($val < 1 || $val > 10)
                    continue;
            }

            $options[$i]->set("value", $val);
        }

        // optional validation goes here

        // write changes on success
        for ($i=0; $i<count($options); $i++) {
            $options[$i]->update();
        }
    }

    function getCountriesStates()
    {
    	if (!isset($this->_profileDialog)) {
    		$this->_profileDialog = new XLite_Controller_Admin_Profile();
    	}
        return $this->_profileDialog->getCountriesStates();
    }

    function getWaitingList()
    {
        if(is_null($this->_waiting_list)){
            $waiting_ip = new XLite_Model_WaitingIP();
            $this->_waiting_list = (array) $waiting_ip->findAll("", "first_date");
        }

        return $this->_waiting_list;
    }

	function getCurrentIP()
	{
	    return $_SERVER['REMOTE_ADDR'];
	}

	function isCurrentIpValid()
	{
		return $this->auth->isValidAdminIP($this, true) == IP_VALID;
	}

    function action_approve_ip()
    {
        $ids = (array) $this->get("waiting_ips");
        foreach($ids as $id){
            $waiting_ip = new XLite_Model_WaitingIP($id);
            $waiting_ip->approveIP();
            $waiting_ip->delete();
        }
        
    }

    function action_delete_ip()
    {
        $ids = (array) $this->get("waiting_ips");
        foreach($ids as $id){
            $waiting_ip = new XLite_Model_WaitingIP($id);
            $waiting_ip->delete();
        }
    }

    function getAllowedList()
    {
        return $this->get("xlite.config.SecurityIP.allow_admin_ip");
    }

    function action_add_new_ip()
    {
        $ip = $this->get("byte_1") . "." . $this->get("byte_2") . "." . $this->get("byte_3") . "." . $this->get("byte_4");
        $comment = $this->get("comment");
        $valid_ips_object = new XLite_Model_Config();
        if(!$valid_ips_object->find("category = 'SecurityIP' AND name = 'allow_admin_ip'"))
            return;
        $list = unserialize($valid_ips_object->get("value"));

        if(!is_array($list) || count($list) < 1){
            $list = array();
        }
        
        foreach($list as $ip_array){
            if($ip_array['ip'] == $ip){
                $this->set("returnUrl", "admin.php?target=" . $this->get("target")
                            . "&page=" . $this->get("page") . "&ip_error=1");
                return;
            }
        }

        $list[] = array("ip" => $ip, "comment" => $comment);

        $valid_ips_object->set("value", serialize($list));
        $valid_ips_object->set("type", "serialized");
        $valid_ips_object->update();
    }

    function action_delete_allowed_ip()
    {
        $new_list = array();
        $ids = (array) $this->get("allowed_ips");
        foreach($this->getAllowedList() as $id => $ip){
            if(!in_array($id, $ids))
                $new_list[] = $ip;
        }

        if(count($new_list) < 1){
            $admin_ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";
            $new_list[] = array("ip" => $admin_ip, "comment" => "Default admin IP");
        }

        $valid_ips_object = new XLite_Model_Config();

        if(!$valid_ips_object->find("category = 'SecurityIP' AND name = 'allow_admin_ip'"))
            return;

        $valid_ips_object->set("value", serialize($new_list));
        $valid_ips_object->update();
    }

    function action_update_allowed_ip()
    {
        $comments = (array) $this->get("comment");
        $valid_ips_object = new XLite_Model_Config();
        if(!$valid_ips_object->find("category = 'SecurityIP' AND name = 'allow_admin_ip'"))
            return;
        $list = unserialize($valid_ips_object->get("value"));
        foreach($list as $id => $ip){
            $comment = $comments[$id];
            $list[$id]["comment"] = $comment;
        }

        $valid_ips_object->set("value", serialize($list));
        $valid_ips_object->update();
    }


    function isWin()
    {
        return (LC_OS_CODE === 'win');
    }

    function getTimeZonesList()
    {
        $list = func_get_timezones();
        if (is_array($list))
            return $list;
        else
            return array("Not supported");
    }

    function getCurrentTimeZone()
    {
        $tz = func_get_timezone();
        if ($tz)
            return $tz;
        else
            return "Not supported";
    }
}
// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
