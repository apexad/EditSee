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
	<link rel="stylesheet" type="text/css" href="<?=$this->get_config('es_main_url')?>theme/ventixo/style.css" />
	<script src="<?=$this->get_config('es_main_url')?>includes/nicEdit/nicEdit.js" type="text/javascript"></script>
	<?php echo $this->header; ?>
	<?php $xajax->printJavascript('includes/xajax/'); ?>
	
</head>
<body>
<div id="page">
	<div id="banner">
		<img src="<?=$this->get_config('es_main_url')?>theme/ventixo/images/banner.png" alt="banner" onclick="location.href='<?=$this->get_config('es_main_url')?>'" />
	</div>
<div id="full">
<div id="main" class="clearfix">
<div id="posts">
