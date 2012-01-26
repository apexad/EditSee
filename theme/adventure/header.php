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
	<?php $xajax->printJavascript(); ?>
	
</head>
<body>
<div id="page">
	<div id="sidebar"><?php  require_once('theme/'.$this->get_config('es_theme').'/sidebar.php'); ?></div>
<div id="posts">
