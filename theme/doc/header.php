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
<div class="container" id="page">
<div id="top" class="g33">
<div id="description" class="g16 alpha">
<h3><?php echo $this->get_config('es_description'); ?></h3>
</div>
<div id="feeds" class="g16 omega" style="text-align:right;">
<?php include('theme/'.$this->get_config('es_theme').'/page_menu.php'); ?>
</div>
</div>
<div class="clear"></div>
<div id="header" class="g33">
<h2><a href="<?=$this->get_config('es_main_url')?>"><?=$this->get_config('es_title'); ?></a></h2>
</div>
<div class="clear"></div>
<div id="content" class="g33">
<div id="posts" class="g25">
