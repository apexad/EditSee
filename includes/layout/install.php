<?php 
/* editsee install file
 */
if (stripos($_SERVER['PHP_SELF'],'install.php')) {
	header("Location: ../../");
}
global $xajax;

?><!doctype html>
<html lang="en">
<head>
	<title>EditSee Install</title>
	<?php $xajax->printJavascript('includes/xajax/'); ?>
	<style type="text/css">
		img { display:block; margin-bottom:1em; }
		table { border:2px solid black; border-radius:5px; -moz-border-radius:5px; padding:10px; }
		img,table{ margin-left:auto; margin-right:auto; }
		button { 
			padding:10px;
			border:2px solid #46B754;
			border-radius:5px;
			-moz-border-radius:5px;
			font-weight:bold; 
			font-family:monospace;
			font-size:24px;
			margin-top:1em;
		}
		button:active { border-left:2px solid #ccc; border-top:2px solid #ccc; }
	</style>
</head>
<body>
<img src="/includes/layout/images/editsee.png" alt="EditSee logo" />
<div id="content">
<form id="editsee_install" onsubmit="return false;">
<table>
	<tr>
		<td>Config Filename:</td>
		<td><input type="text" id="config_file" value="editsee-config.php" /></td>
		<td>* must have 'config' in the filename (and end with .php)</td>
	</tr>
	<tr>
		<td>Database Type:</td>
		<td colspan="2"><select id="db_type">
			<option value="mysql">MySQL</option>
			<option value="mssql">MS SQL</option>
		</select><td>
	</tr>
	<tr>
		<td>Database Host:</td>
		<td><input type="text" id="db_host"></td>
		<td>* localhost on some servers</td>
	</tr>
	<tr>
		<td>Database User:</td>
		<td colspan="2"><input type="text" id="db_user"></td>
	</tr>
	<tr>
		<td>Database Password:</td>
		<td colspan="2"><input type="password" id="db_password"></td>
	</tr>
	<tr>
		<td>Database Name:</td>
		<td colspan="2"><input type="text" id="db_name"></td>
	</tr>
	<tr>
		<td>Table Prefix:</td>
		<td><input type="text" id="db_tableprefix" value="es_"/></td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></td></tr>
	<tr>
		<td>Site Title:</td>
		<td><input type="text" id="site_title" /></td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></td></tr>
	<tr>
		<td>Admin Username:</td>
		<td><input type="text" id="admin_user"></td>
		<td></td>
	</tr>
	<tr>
		<td>Admin Password:</td>
		<td><input type="password" id="admin_password"></td>
		<td></td>
	</tr>
	<tr>
		<td>Admin E-Mail Address:</td>
		<td><input type="text" id="admin_email"></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="3" align="center">
			<button onclick="xajax_createConfigFile(
			document.getElementById('config_file').value
			,(document.getElementById('db_type').options[document.getElementById('db_type').selectedIndex]).value
			,document.getElementById('db_host').value
			,document.getElementById('db_user').value
			,document.getElementById('db_password').value
			,document.getElementById('db_name').value
			,document.getElementById('db_tableprefix').value
			,document.getElementById('admin_user').value
			,document.getElementById('admin_password').value
			,document.getElementById('admin_email').value
			,document.getElementById('site_title').value); return false;">Install EditSee</button>
		</td>
	</tr>
</table>
</form>
</div>
</body>
</html>
