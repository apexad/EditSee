<?php 
/* editsee header file
 * $this is available which has ->title and ->header variables available
 */
global $xajax;
?><!doctype html>
<html lang="en">
<head>
	<title><?php echo $this->title; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="<?=$this->get_config('es_main_url')?>theme/<?=$this->get_config('es_theme')?>/style.css" />
	<script src="<?=$this->get_config('es_main_url')?>includes/nicEdit/nicEdit.js" type="text/javascript"></script>
	<?php echo $this->header; ?>
	<?php $xajax->printJavascript('includes/xajax/'); ?>
	
</head>
<body>
<div id="page">
<div id="full">
<div id="page-menu" class="header-menu-wrapper clearfix">
<?php include('theme/'.$this->get_config('es_theme').'/page_menu.php'); ?>
</div>
<div id="header">
	<div id="logo_image">
		<h1>
			<a href="<?=$this->get_config('es_main_url')?>"><img src="<?=$this->get_config('es_main_url')?>theme/<?=$this->get_config('es_theme')?>/images/editsee.png" title="Monitor Models" alt="editsee logo" /></a>
		</h1>
	</div>
	<div id="header_meta"></div>
</div>
<div id="main" class="clearfix">
<div id="posts">
