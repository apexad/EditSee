<?php
session_start();

require_once("includes/xajax/xajax_core/xajax.inc.php");
require_once("includes/editsee_App.class.php");
require_once('includes/database/editsee_Database.class.php');
$xajax = new xajax();

$xajax->register(XAJAX_FUNCTION,"createConfigFile");
function createConfigFile($name,$type,$host,$user,$password,$database,$table_prefix,$admin_user,$admin_password,$admin_email,$site_title) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();

	if ((preg_match('/\w*config\w*\.php/',$name) == 1) && (!$project7->configFileExists()) ){
			$filename = $name;
			$content .= '<?php
$type		= '."'$type';".'
$host 		= '."'$host';".'
$user		= '."'$user';".'
$password	= '."'$password';".'
$database	= '."'$database';".'
$table_prefix   = '."'$table_prefix';".'
?>';
			$filehandle = fopen($filename, 'w') or die("can't open file");
			fwrite($filehandle, $content);
			fclose($filehandle);
			$project7->connectDatabase();
			//attempt to create the user table
			$table_created = $project7->db->_query("
						CREATE TABLE IF NOT EXISTS `".$table_prefix."user` (
						`user_id` int(11) NOT NULL AUTO_INCREMENT,
  						`username` varchar(255) NOT NULL,
  						`role` varchar(6) NOT NULL,
  						`email` varchar(255) NOT NULL,
  						`password` char(32) NOT NULL,
  						PRIMARY KEY (`user_id`),
						UNIQUE KEY `username` (`username`),
						UNIQUE KEY `e-mail` (`email`)
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

			if ($table_created) {
				$insert_query = $project7->db->_insert_user($admin_user,'admin',$admin_password,$admin_email);
				include('includes/database/editsee_Database.create.php');
				$script_uri = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				$objResponse->redirect($script_uri);
			}
			else {
				unlink($filename);
				$objResponse->alert('unable to insert into database!');
			}
	}
	else { 
		$objResponse->alert("Bad config file name!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"topbarActive");
function topbarActive() {
	$objResponse = new xajaxResponse();
	$objResponse->includeCSS(str_replace('index.php','',$_SERVER['SCRIPT_NAME']).'includes/layout/topbar.css');
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"Login");
function Login($username,$password) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	$username = stripslashes($username);
	$password = stripslashes($password);
	$username = $project7->db->_escape_string($username);
	$password = $project7->db->_escape_string($password);
	
	$login_check = $project7->db->_query("select user_id,email from ".$project7->db->get_table_prefix()."user 
										where username='".$username."' and password='".md5($password)."'");
	if ($login_check->_num_rows() == 1) {
		$info = $login_check->_fetch_assoc();
		$_SESSION['username'] = $username;
		$_SESSION['user_id'] = $info['user_id'];
		$_SESSION['email'] = $info['email'];
		
		/* include loggedin.css
		 * displays post edit & delete buttons
		 * displays link delete buttons
		 */
		$objResponse->includeCSS(str_replace('index.php','',$_SERVER['SCRIPT_NAME']).'includes/layout/loggedin.css');
		$objResponse->includeCSS(str_replace('index.php','',$_SERVER['SCRIPT_NAME']).'includes/layout/topbar.css');
		
		//reload the topbar
		ob_start();
		$project7->display('topbar-only');
		$topbar = ob_get_contents();
		ob_end_clean();
		$objResponse->assign('topbar','innerHTML',$topbar);

		//reload posts (to show scheduled posts)
		ob_start();
		$project7->display('posts-only');
		$reload_posts = ob_get_contents();
		ob_end_clean();
		$objResponse->assign('posts','innerHTML',$reload_posts);

	}
	else {
		$objResponse->alert("Invalid Username/Password");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"Logout");
function Logout() {
	$objResponse = new xajaxResponse();
	session_start();
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
    	$params = session_get_cookie_params();
    	setcookie(session_name(), '', time() - 42000,
        	$params["path"], $params["domain"],
        	$params["secure"], $params["httponly"]
    	);
	}
	session_destroy();
	$project7 = new editsee_App();
	$objResponse->redirect($project7->get_config('es_main_url'));
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"addComment");
function addComment($name,$email,$humancheck,$text,$post_id) {
	$objResponse = new xajaxResponse();
	if ($humancheck == ($_SESSION['rand1']+$_SESSION['rand2'])) {
		$project7 = new editsee_App();
		$project7->db->insert_comment($post_id,$name,$email,$text);
		
		if ($project7->get_config('es_email_comments') == '1') {
			mail($project7->get_user_email($project7->get_post_user($post_id)),
			'New Comment on Post '.$project7->get_post_title($post_id),
			$project7->get_config('es_main_url').'post/'.$post_id.'/#comments'."\n".$name.' said:'."\n".$text);
		}

		$objResponse->assign('comments','innerHTML',$project7->get_comments($post_id));
		$objResponse->script("document.getElementById('comment_name').value = '';
							  document.getElementById('comment_email').value = '';
							  document.getElementById('comment_humancheck').value = '';
							  document.getElementById('comment_text').value = ''");
	}
	else {
		$objResponse->alert("You did not answer the spam check question correctly.");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"addPost");
function addPost($id,$title,$content,$category,$urltag,$type,$date,$in_nav,$page_order_position,$page_order_after) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		$content = str_replace(array('&lt;','&gt;'),array('<','>'),$content);
		$content_check = str_replace('<br>','',$content);
		$urltag_check = str_replace(array('post','page','feed','category'),'',$urltag);
		if (!empty($title) && !empty($content_check) && !empty($category) && !empty($urltag_check)) {
			$insert_query = $project7->db->_insert_post($id,$title,$content,$category,$urltag,$type,$date,$in_nav,$page_order_position,$page_order_after);
			if ($id == 'new' && $type != 'page') {
				ob_start();
				$project7->display('posts-only');
				$output = ob_get_contents();
				ob_end_clean();
			
				$objResponse->assign('posts','innerHTML',$output);
			}
			else if ($id == 'new' && $type == 'page') {
				$objResponse->assign('new-post','innerHTML',$project7->get_single_post($insert_query,'innerHTML'));
			}
			else {
				$objResponse->assign('post-'.$id,'innerHTML',$project7->get_single_post($id,'innerHTML'));
			}
			ob_start();
			$project7->display('page-menu-only');
			$output = ob_get_contents();
			ob_end_clean();
			$objResponse->assign('page-menu','innerHTML',$output);
			$objResponse->assign('main-nav','innerHTML',$output);
			
			ob_start();
			$project7->display('sidebar-only');
			$output = ob_get_contents();
			ob_end_clean();
			$objResponse->assign('sidebar','innerHTML',$output);
			$_SESSION['in-quick'.$id] = 'no';
		}
		else {
			$objResponse->alert('Title, Category, URLtag and Post content are all required.'."\n".
								'URLTag cannot be post, page, category, or feed.');
		}
	}
	else {
		$objResponse->alert("Not Logged In!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"unDelete");
function unDelete($id,$type) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		$project7->db->_un_delete($id,$type);
		$objResponse->redirect($project7->get_config('es_main"url'));
	}
	else {
		$objResponse->alert("Not Logged in!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"undeleteList");
function undeleteList($type) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		$objResponse->assign('undelete_items','innerHTML',$project7->undeleteList($type));
	}
	else {
		$objResponse->alert("Not Logged in!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"quickEditPost");
function quickEditPost($post_id,$content,$title) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		$content = str_replace(array('&lt;','&gt;'),array('<','>'),$content);
		$query = $project7->db->_query("update ".$project7->db->get_table_prefix()."post set content='".$project7->db->_escape_string($content)."',title='".$title."' where id='".$post_id."'");
		$objResponse->assign('post-'.$post_id,'innerHTML',$project7->get_single_post($post_id,'innerHTML'));
		$_SESSION['in-quick'.$post_id] = 'no';
	}
	else {
		$objResponse->alert("Not Logged In!");
	}
	return $objResponse;
	
}
$xajax->register(XAJAX_FUNCTION,"cancel_addPost");
function cancel_addPost($post_id,$post_type='notpage') {
	$objResponse = new xajaxResponse();
	if ($post_id != 'new') {
		$project7 = new editsee_App();
		$objResponse->assign('post-'.$post_id,'innerHTML',$project7->get_single_post($post_id,'innerHTML'));
	}
	else {
		$objResponse->remove('new-post');
		if ($post_type='page') {
			$project7 = new editsee_App();
			$objResponse->redirect($project7->get_config('es_main"url'));
		}
	}
	$_SESSION['in-quick'.$post_id] = 'no';
	return $objResponse;
}

$xajax->register(XAJAX_FUNCTION,"updatePost");
function updatePost($post_id,$mode = 'full',$content = 'not needed') {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		if ($post_id == 'new') {
			$post_div = 'new-post';
			$post['id'] = 'new';
			$post['content'] = 'enter your new post content here';
			if ($mode == 'page')
				$objResponse->assign('posts','innerHTML','');
			$objResponse->prepend('posts','innerHTML','<div id="new-post" class="post"></div>'); 
		}
		else {
			$result = $project7->db->_query($project7->post_select(" and id='".$post_id."'"));
			$post = $result->_fetch_assoc();
			$post['title'] = stripslashes($post['title']);
			$post['content'] = stripslashes($post['content']);
			$post_div = 'post-'.$post['id'];
		}
		
		if ($mode == 'quick') {
			if ($_SESSION['in-quick'.$post_id] != 'yes') {
				$_SESSION['in-quick'.$post_id] = 'yes';
				ob_start();
				include('includes/layout/quickedit.php');
				$quick_edit = ob_get_contents();
				ob_end_clean();
				$objResponse->assign('post-'.$post['id'],'innerHTML',$quick_edit);
				$objResponse->script("mynicEditor".$post['id']." = new nicEditor({iconsPath : '".str_replace('index.php','',$_SERVER['SCRIPT_NAME'])."includes/nicEdit/nicEditorIcons.gif',buttonList : [],uploadURI : 'http://".$_SERVER['HTTP_HOST']."/nicUpload.php'}).panelInstance('post_content-".$post['id']."')");
			}
		}
		else {
			if ($mode == 'inquick') {
				$post['content'] = stripslashes($content);
			}
			if ($project7->is_page($post_id))
				$mode = 'page';
			if ($mode == 'page') { $post_type = 'page'; }
			else { $post_type = 'post'; }
			$_SESSION['in-quick'.$post_id] = 'yes';
			ob_start();
			include('includes/layout/newpost.php');
			$newpost = ob_get_contents();
			ob_end_clean();
			$objResponse->assign($post_div,'innerHTML',$newpost);
			$objResponse->script("mynicEditor".$post['id']." = new nicEditor({iconsPath : 
'".str_replace('index.php','',$_SERVER['SCRIPT_NAME'])."includes/nicEdit/nicEditorIcons.gif',fullPanel: 
true,uploadURI : 'http://".$_SERVER['HTTP_HOST']."/nicUpload.php'}).panelInstance('post_content-".$post['id']."')");
		}
	}
	else {
		$objResponse->alert("Not Logged In!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"deletePost");
function deletePost($post_id) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {	
		//$result = $project7->db->_query("delete from ".$project7->db->get_table_prefix()."post where id='".$post_id."'");
		$is_page = $project7->is_page($post_id);
		$result = $project7->db->_delete_post($post_id);
		if ($is_page) { $objResponse->redirect($project7->get_config('es_main_url')); }
		else {
			$objResponse->remove('post-'.$post_id);
			//reload the sidebar
			ob_start();
			$project7->display('sidebar-only');
			$output = ob_get_contents();
			ob_end_clean();
			$objResponse->assign('sidebar','innerHTML',$output);
		}
	}
	else {
		$objResponse->alert("Not Logged In!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"deleteComment");
function deleteComment($comment_id) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		$query = $project7->db->_query("select linked_post_id from ".$project7->db->get_table_prefix()."comments where comment_id='".$comment_id."'");
		$post_id = $query->_result(0);
		$project7->db->_query("update ".$project7->db->get_table_prefix()."comments set date_deleted=now(),deleted='1' where comment_id='".$comment_id."'");
	}
	$objResponse->assign("comments","innerHTML",$project7->get_comments($post_id));
	return $objResponse;
}	
$xajax->register(XAJAX_FUNCTION,"changePassword");
function changePassword($existing,$new_password,$retype_password) {
	$project7 = new editsee_App();
	$objResponse = new xajaxResponse();
	if ($project7->loggedIn()) {
		if ($new_password == $retype_password) {
			if ($project7->change_password($existing,$new_password)) {
				$objResponse->alert("Password changed");
			}
			else {
				$objResponse->alert("Could not change password");
			}
			$objResponse->remove("popup");
			$objResponse->removeCSS('includes/layout/overlay.css');
		}
		else {
			$objResponse->alert("You did not type in the same password");
		}
	}
	else {
		$objResponse->alert("Not Logged In!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"newLink");
function newLink($link_url,$link_title,$link_nofollow,$link_target) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		if (!empty($link_url) && !empty($link_title)) {
			$project7->db->_insert_link($link_url,$link_title,$link_nofollow,$link_target);
			ob_start();
			$project7->display('sidebar-only');
			$new_sidebar = ob_get_contents();
			ob_end_clean();
			$objResponse->assign('sidebar','innerHTML',$new_sidebar);
			$objResponse->remove("popup");
			$objResponse->removeCSS('includes/layout/overlay.css');
		}
		else {
			$objResponse->alert("Link URL and Link title are required");
		}
	}
	else {
		$objResponse->alert("Not Logged In!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"deleteLink");
function deleteLink($link_id) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {	
		$result = $project7->db->_delete_link($link_id);
		
		//reload the sidebar
		ob_start();
		$project7->display('sidebar-only');
		$output = ob_get_contents();
		ob_end_clean();
		$objResponse->assign('sidebar','innerHTML',$output);
	}
	else {
		$objResponse->alert("Not Logged In!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"siteSettings");
function siteSettings($site_title,$site_url,$site_desc,$posts_per_page,$homepage,$postpage,$email_comments) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		$email_comments = ($email_comments) ? '1' : '0';
		$project7->db->_update_options($site_title,$site_url,$site_desc,$posts_per_page,$homepage,$postpage,$email_comments);
		$objResponse->redirect($project7->get_config('es_main_url'));
	}
	else {
		$objResponse->alert('Not Logged In!');
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"profileSettings");
function profileSettings($username,$email) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		$user_update = $project7->db->_update_user($username,$email);
		if ($user_update->_affected_rows() == 1) {
			$objResponse->alert('Profile settings update successful');
			$objResponse->remove("popup");
			$objResponse->removeCSS('includes/layout/overlay.css');
		}
		else {
			$objResponse->alert('Profile settings not updated');
		}
	}
	else {
		$objResponse->alert('Not Logged In!');
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"forgotPassword");
function forgotPassword($info) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	$check_users = $project7->db->_query("select email 
										from ".$project7->db->get_table_prefix()."user where username='".$project7->db->_escape_string($info)."'");
	if ($check_users->_num_rows() == 1) {
		$email_address = $check_users->_result(0);	
	}
	else {
		$check_email = $project7->db->_query("select email 
										from ".$project7->db->get_table_prefix()."user where email='".$project7->db->_escape_string($info)."'");
		if ($check_email->_num_rows() == 1) {
			$email_address = $check_email->_result(0);	
		}
	}
	
	if (!empty($email_address)) {
		$new_password = $project7->random_password();
		$subject = 'editsee password reset for site at '.$_SERVER['HTTP_HOST'];
		$message = 'Your password has been reset to '.$new_password;
		if (mail($email_address,$subject,$message)) {
			$project7->db->_query("update `".$project7->db->get_table_prefix()."user` set `password`=md5('".$new_password."') where email='".$email_address."'");
			$objResponse->alert("Your password has been reset. Check your e-mail.");
		}
		else {
			$objResponse->alert("Error resetting password.");
		}
	}
	else {
		$objResponse->alert("Sorry, no user was found.");
	}
	$objResponse->remove("popup");
	$objResponse->removeCSS('includes/layout/overlay.css');
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"newCategory");
function newCategory($category) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		$project7->db->_insert_category($category);
		$objResponse->assign('post_category','innerHTML',$project7->get_categories('option',true,'</option>',$post['simple_category']));
		$objResponse->remove("popup");
		$objResponse->removeCSS('includes/layout/overlay.css');
	}
	else {
		$objResponse->alert("Not Logged In!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"customSection");
function customSection($section,$label,$data) {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		$project7->db->_insert_custom_section($section,$label,$data);
		$objResponse->append('footer','innerHTML',$data);
		$objResponse->remove("popup");
		$objResponse->removeCSS('includes/layout/overlay.css');
	}
	else {
		$objResponse->alert("Not Logged In!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"setTheme");
function setTheme($new_theme) {
	$_SESSION['temp_theme'] = '';
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		$project7->db->_query("update `".$project7->db->get_table_prefix()."config` set data='".$project7->db->_escape_string($new_theme)."' where `option`='es_theme'");
		$objResponse->redirect($project7->get_config('es_main_url'));
	}
	else {
		$objResponse->alert("Not Logged In!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"previewTheme");
function previewTheme($preview_theme,$revert='no') {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		if ($revert == 'no') {
			$_SESSION['temp_theme'] = $project7->db->_escape_string($preview_theme);
		}
		else {
			$_SESSION['temp_theme'] = '';
		}
		$objResponse->redirect($project7->get_config('es_main_url'));
	}
	else {
		$objResponse->alert("Not Logged In!");
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"openPopup");
function openPopup($popup) {
	$objResponse = new xajaxResponse();
	$objResponse->includeCSS(str_replace('index.php','',$_SERVER['SCRIPT_NAME']).'includes/layout/overlay.css');
	switch ($popup) {
	case 'undelete':
	$popup_title = 'Restore Posts';
	$popup_contents = <<<UNDELETE
	<form id="editsee_undelte">
	<table>
	<tr><td><!-- Content Type:</td><td> <select id="content_type" onchange="xajax_undeleteList((this.options[this.selectedIndex]).value)">
		<option value=""></option>
		<option value="post">Posts</option>
		<option value="page">Pages</option>
		<option value="links">Links</option>
		<option value="comments">Comments</options>
		</select>--><input type="button" value="Load Posts" onclick="xajax_undeleteList('post'); return false;" /></td></tr>
		<tr><td colspan="2" id="undelete_items"></td></tr>
		<tr><td colspan="2" class="submit">
UNDELETE;
	break;
	case 'password':
	$popup_title = 'Change Password';
	$popup_contents = <<<PASSWORD
	<form id="editsee_password">
	<table>
	<tr><td>Existing Password:</td><td><input type="password" id="existing_password" /></td></tr>
	<tr><td>New Password:</td><td><input type="password" id="new_password" /></td></tr>
	<tr><td>Re-Type New Password:</td><td><input type="password" id="retype_password" /></td></tr>
	<tr><td colspan="2" class="submit">
	<input type="submit" value="change password" onclick="xajax_changePassword(
													document.getElementById('existing_password').value
													,document.getElementById('new_password').value
													,document.getElementById('retype_password').value
												); return false;" />
PASSWORD;
	break;
	case 'new_link':
	$popup_title = 'New Link';
	$popup_contents = <<<NEWLINK
	<form id="editsee_newlink">
	<table>
	<tr><td>Link URL:</td><td><input type="text" id="link_url" /></td></tr>
	<tr><td>Link Title:</td><td><input type="text" id="link_title" /></td></tr>
	<tr><td>No-Follow <span title="no follow tells search engine to ignore this link">(?)</span>:</td><td><input type="checkbox" id="link_nofollow" class="checkbox" value="nofollow" /></td></tr>
	<tr><td>Link Target</td><td><select id="link_target">
									<option value="_self">Same Window</option>
									<option value="_blank">New Window</option>
								</select></td></tr>
	<tr><td colspan="2" class="submit">
	<input type="submit" value="add link" onclick="xajax_newLink(
													document.getElementById('link_url').value
													,document.getElementById('link_title').value
													,document.getElementById('link_nofollow').checked
													,(document.getElementById('link_target').options[document.getElementById('link_target').selectedIndex]).value
												); return false;" />
NEWLINK;
	break;
	case 'theme_selector':
	$_SESSION['temp_theme'] = '';
	$_SESSION['popup_close_redirect'] = 'yes';
	$project7 = new editsee_App();
	$themes = '';
	$theme_url = $project7->get_config('es_main_url').'theme/';
		$handler = opendir('./theme'); 
			while ((false !== ($file = readdir($handler)))) {
				if (substr($file,0,1) != '.') {
					$themes .= '<option value="'.$file.'"'; 
					if ($file == $project7->get_config('es_theme')) {
						$themes .= ' selected="selected"';
						$theme_image = $theme_url.$project7->get_config('es_theme').'/screenshot.png';
					}
					$themes .= '>'.ucfirst($file).'</option>';
				}
			}
	$popup_title = 'Change Theme';
	$popup_contents = <<<THEMESELECT
			<form id="editsee_theme_selector">
			<table>
			<tr><td>Theme:</td><td><select id="site_theme" onchange="document.getElementById('theme_screenshot').src='$theme_url'+(this.options[this.selectedIndex]).value+'/screenshot.png'">	
			$themes</select></td></tr>
			<tr><td colspan="2"><img id="theme_screenshot" src="$theme_image"></div></td></tr>
			<tr><td colspan="2" class="submit"><input type="submit" value="Preview Theme" onclick="xajax_previewTheme((document.getElementById('site_theme').options[document.getElementById('site_theme').selectedIndex]).value); return false;" /></td></tr>
			<tr><td colspan="2" class="submit"><input type="submit" value="change theme" onclick="xajax_setTheme((document.getElementById('site_theme').options[document.getElementById('site_theme').selectedIndex]).value); return false;" />
THEMESELECT;
	break;
	case 'site_settings':
		$project7 = new editsee_App();
		$title = $project7->get_config('es_title');
		$main_url = $project7->get_config('es_main_url');
		$description = $project7->get_config('es_description');
		$posts_per_page = $project7->get_config('es_posts_per_page');
		$email_comments = ($project7->get_config('es_email_comments') == '1') ? ' checked="checked"' : '';
		$popup_title = 'Site Settings';
		$popup_contents = <<<SITESETTINGS
		<form id="editsee_sitesettings">
		<table>
		<tr><td>Site Title:</td><td><input type="text" id="site_title" value="$title" /></td></tr>
		<tr><td>Site URL:</td><td><input type="text" id="site_url" value="$main_url" /></td></tr>
		<tr><td>Site Description:</td><td><textarea id="site_desc" rows="5">$description</textarea></td>
		<tr><td>E-mail Comments:</td><td><input type="checkbox" id="email_comments" class="checkbox" value="yes" $email_comments></td></tr>
		<tr><td>Post Per Page:</td><td><input type="text" id="posts_per_page" value="$posts_per_page" /></td></tr>
		<tr><td>Custom Homepage:</td><td>
							<select id="homepage">
							<option value="!posts!">-- posts --</option>
SITESETTINGS;
				$query = $project7->db->_query("select title,urltag from ".$project7->db->get_table_prefix()."post 
													where type='page' and deleted='0'
													order by title desc");
		while($row = $query->_fetch_assoc()) {
					$popup_contents .= '<option value="'.$row['urltag'].'"';
					if ($project7->get_config('es_homepage') == $row['urltag']) {
						$popup_contents .= ' selected="selected"';
					}
					$popup_contents .= '>'.$row['title'].'</option>'."\n";
				}
$post_page = $project7->get_config('es_postpage');
$popup_contents .= '
							</select></td></tr>
		<tr><td>Posts Page:</td><td><input type="text" id="postpage" value="'.$post_page.'" /></td></tr>';
$popup_contents .= <<<SITESETTINGS
		<tr><td colspan="2" class="submit">
		<input type="submit" value="save settings" onclick="xajax_siteSettings(
													document.getElementById('site_title').value
													,document.getElementById('site_url').value
													,document.getElementById('site_desc').value
													,document.getElementById('posts_per_page').value
													,document.getElementById('homepage').value
													,document.getElementById('postpage').value
													,document.getElementById('email_comments').checked
													); return false;" />
SITESETTINGS;
	break;
	case 'profile_settings':
		$project7 = new editsee_App();
		$username = $_SESSION['username'];
		$query = $project7->db->_query("select email from ".$project7->db->get_table_prefix()."user where user_id='".$_SESSION['user_id']."'");
		$email = $query->_result(0);
		$popup_title = 'Profile Settings';
		$popup_contents = <<<PROFILESETTINGS
	<form id="editsee_profile_settings">
	<table>
	<tr><td>Username:</td><td><input type="text" id="username" value="$username" /></td></tr>
	<tr><td>E-mail Address</td><td><input type="text" id="email" value="$email" /></td></tr>
	<tr><td colspan="2" class="submit">
	<input type="submit" value="save profile" onclick="xajax_profileSettings(
													document.getElementById('username').value
													,document.getElementById('email').value
												); return false;" />
PROFILESETTINGS;
	break;
	case 'forgot_password':
		$project7 = new editsee_App();
		$popup_title = 'Forgot Password';
		$popup_contents = <<<FORGOTPASSWORD
	<form id="editsee_forgot_password">
	<table>
	<tr><td>Username/E-mail Address:</td><td><input type="text" id="information" /></td></tr>
	<tr><td colspan="2" class="submit">
	<input type="submit" value="reset password" onclick="xajax_forgotPassword(
													document.getElementById('information').value
												); return false;" />
FORGOTPASSWORD;
	break;
	case 'new_category':
		$popup_title = 'New Category';
		$popup_contents = <<<NEWCAT
		<form id="editsee_new_category">
		<table>
		<tr><td>Category:</td><td><input type="text" id="category" /></td></tr>
		<tr><td colspan="2" class="submit">
		<input type="submit" value="add category" onclick="xajax_newCategory(document.getElementById('category').value); return false;" />		
NEWCAT;
	break;
	case 'custom_footer':
	$project7 = new editsee_App();
	$query = $project7->db->_query("select data from ".$project7->db->get_table_prefix()."custom where section='footer' and label='Custom Footer'");
	if ($query->_num_rows() == 1) {
		$custom_footer = stripslashes($query->_result(0));
	}
	$popup_title = 'Custom Footer';
	$popup_contents = <<<CUSTOMFOOTER
	<form id="custom_footer">
	<table>
	<tr><td>
			<input type="hidden" id="custom_section" value="footer" />
			<input type="hidden" id="custom_label" value="Custom Footer" />
			<textarea id="custom_footer_code" style="width:300px !important;" rows="20">$custom_footer</textarea>
	</tr</td>
	<tr><td colspan="2" class="submit"><input type="submit" value="save footer" onclick="xajax_customSection(document.getElementById('custom_section').value
																			,document.getElementById('custom_label').value
																			,document.getElementById('custom_footer_code').value); return false;" />
CUSTOMFOOTER;
	break;
	}
	$popup_contents .= '<input type="submit" value="cancel" onclick="xajax_closePopup(); return false;" /></td></tr>
		</table>
		</form>';
	$popup_contents = '<div>'.$popup_title.'<button onclick="xajax_closePopup(); return false;">X</button></div>'.$popup_contents;
	$objResponse->insert("page","div","popup");
	$objResponse->assign("popup",'innerHTML',$popup_contents);
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"closePopup");
function closePopup() {
	$objResponse = new xajaxResponse();
	$objResponse->remove("popup");
	$objResponse->removeCSS('includes/layout/overlay.css');
	if ($_SESSION['popup_close_redirect'] == 'yes') {
		$project7 = new editsee_App();
		$_SESSION['popup_close_redirect'] = '';
		$_SESSION['temp_theme'] = '';
		$objResponse->redirect($project7->get_config('es_main_url'));
	}
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"moveLink");
function moveLink($link_id,$direction) {
	$objResponse = new xajaxResponse();
	$direction = ($direction == 'up') ? '<' : '>';
	$orderby = ($direction == '<') ? 'desc' : 'asc';
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		//get the stuff
		$query = $project7->db->_query("select link_order from ".$project7->db->get_table_prefix()."links where link_id='".$link_id."'");
		$link_order_orginal = $query->_result(0);
		$query2 = $project7->db->_query("select link_id,link_order from ".$project7->db->get_table_prefix()."links 
										where link_order ".$direction." '".$link_order_orginal."' and deleted=0
										order by link_order ".$orderby." limit 1");
		$link_order_cutoff = $query2->_fetch_assoc();
		
		//fix the stuff
		$project7->db->_query("update ".$project7->db->get_table_prefix()."links set link_order='".$link_order_cutoff['link_order']."' where link_id='".$link_id."'");
		$project7->db->_query("update ".$project7->db->get_table_prefix()."links set link_order='".$link_order_orginal."' where link_id='".$link_order_cutoff['link_id']."'");
		
		ob_start();
		$project7->display('sidebar-only');
		$new_sidebar = ob_get_contents();
		ob_end_clean();
		$objResponse->assign('sidebar','innerHTML',$new_sidebar);
	}
	else {
		$objResponse->alert("Not Logged In!");
	}
	return $objResponse;
}

$xajax->register(XAJAX_FUNCTION,"generatePostData");
function generatePostData($post_id,$type,$data='') {
	$objResponse = new xajaxResponse();
	if ($type == 'urltag') {
		$urltag = str_replace(' ','-',strtolower($data));
		$urltag = ereg_replace("[^A-Za-z0-9-]", "", $urltag);
		$project7 = new editsee_App();
		$urltag_ok = false;
		$urltag_num = '';
		while ($urltag_ok !== true) {
			$urltag_check = $project7->db->_query("select 'post-exists' from ".$project7->db->get_table_prefix()."post where urltag = '".$urltag.$urltag_num."' and id !='".$post_id."'");
			if ($urltag_num == '') { $urltag_num = 0; }
			if ($urltag_check->_num_rows() >= 1) {
				$urltag_num += 1;
			}
			else {
				$urltag_ok = true;
			}
		}
		if ($urltag_num > 0) {
			$urltag .= $urltag_num;
		}
		$objResponse->assign('post_urltag','value',$urltag);
	}
	if ($type == 'reset-date') {
		$objResponse->assign('post_date',value,date('Y-m-d H:i:s'));
	}
	
	return $objResponse;
}
$xajax->register(XAJAX_FUNCTION,"loadThemeConfig");
function loadThemeConfig() {
	$objResponse = new xajaxResponse();
	$project7 = new editsee_App();
	if ($project7->loggedIn()) {
		ob_start();
		include('theme/'.$project7->get_config('es_theme').'/config.php');
		$config = ob_get_contents();
		ob_end_clean();
		$objResponse->assign('posts','innerHTML',$config);
		
		ob_start();
		include('theme/'.$project7->get_config('es_theme').'/config.js');
		$script = ob_get_contents();
		ob_end_clean();
		$objResponse->script($script);
	}
	return $objResponse;
}
$xajax->processRequest();
$xajax->configure('javascript URI',str_replace('index.php','',$_SERVER['SCRIPT_NAME']).'includes/xajax/');
?>
