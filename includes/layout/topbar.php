<?php
/* editsee topbar
 * just a heads up... this file will eventually go away
 * it contains a bit of crucial php
 * it's just here in a nice file so it's easy to edit, for now.
 * we'll need to hide this within the editsee_App class eventually 
 * 
 * this is the reason this page is usually references as $this->topbar 
 * */
session_start();
if (empty($_SESSION['username'])) {
?>
<div id="admin" class="loggedout" onclick="xajax_topbarActive()">
<br />
<form id="editsee_login">
<input name="username" type="text" id="username" onclick="xajax_topbarActive()" />
<input name="password" type="password" id="password" onclick="xajax_topbarActive()" />
<input type="submit" value="login" onclick="xajax_Login(document.getElementById('username').value,document.getElementById('password').value); return false;" />
<input type="submit" value="forgot password" onclick="xajax_openPopup('forgot_password'); return false;" />
</form>
</div>
<?php
}
if (!empty($_SESSION['username'])) {
?>
<div id="admin" class="loggedin">
<img id="topbar_editsee_logo" src="<?php echo str_replace('index.php','',$_SERVER['SCRIPT_NAME']).'includes/layout/images/editsee.png'; ?>" alt="editSee" />
<?php
}
if (!empty($_SESSION['username']) && !empty($_SESSION['temp_theme'])) {
?>
	<ul id="menu">
		<li><a onclick="xajax_setTheme('<?=$_SESSION['temp_theme']?>')">Use Theme</a></li>
		<li><a onclick="xajax_openPopup('theme_selector'); return false;">Theme&nbsp;Selector</a></li>
		<li><a onclick="xajax_closePopup()">Cancel</a></li>
	</ul>
</div>
<?php }
if (!empty($_SESSION['username']) && empty($_SESSION['temp_theme'])) {
?>
<ul id="menu">
<?php 
	if ($this->isAdmin()) {
?>
	<li><a href="#" onclick="return false;">Content</a>
		<ul>
<?php
	} //ends if (isAdmin())
?>
			<li><a onclick="xajax_updatePost('new'); return false;">New Post</a></li>
			<li><a onclick="xajax_updatePost('new','page'); return false;">New Page</a></li>
<?php 
	if ($this->isAdmin()) {
?>
			<li><a onclick="xajax_openPopup('new_link')">New Link</a></li>
			<li><a onclick="xajax_openPopup('undelete')">Restore Posts</a></li>
		</ul>
	</li>
	<li><a href="#" onclick="return false;">Settings</a>
		<ul>
			<!--<li><a onclick="xajax_openPopup('restore_posts'); return false;">Restore Posts</a></li>-->
			<li><a onclick="xajax_openPopup('site_settings'); return false;">Site Settings</a></li>
			<li><a onclick="xajax_openPopup('custom_footer'); return false;">Custom Footer</a></li>
<?php
	if (file_exists('theme/'.$this->get_config('es_theme').'/config.php')) {
?>
			<li><a onclick="xajax_loadThemeConfig(); return false;">Theme Config</a></li>
<?php } ?>
			<li><a onclick="xajax_openPopup('theme_selector'); return false;" class="b">Change Theme</a></li>
			<li><a onclick="xajax_openPopup('manage_users'); return false;">Manage Users</a></li>
	</ul>
<?php
	} //ends if (isAdmin())
?>
	
	</li>
	<li class="login"><a href="#" onclick="return false;"><?=$_SESSION['username']?></a>
		<ul>
			<li><a onclick="xajax_openPopup('profile_settings'); return false;">Profile</a></li>
			<li><a onclick="xajax_openPopup('password'); return false">Password</a></li>
			<li><a onclick="xajax_Logout(); return false;" class="b">Logout</a></li>
		</ul>
	</li>
</ul>
</div>
<?php
}
?>

