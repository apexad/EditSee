<?php
global $xajax;
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- disable iPhone initial scale -->
<meta name="viewport" content="width=device-width; initial-scale=1.0">

<title><?php echo $this->title; ?></title>

<!-- main css -->
<link href="<?=$this->get_config('es_main_url')?>theme/<?=$this->get_config('es_theme')?>/style.css" rel="stylesheet" type="text/css">

<!-- media queries css -->
<link href="<?=$this->get_config('es_main_url')?>theme/<?=$this->get_config('es_theme')?>/media-queries.css" rel="stylesheet" type="text/css">

<!-- html5.js for IE less than 9 -->
<!--[if lt IE 9]>
	<script src="<?=$this->get_config('es_main_url')?>theme/<?=$this->get_config('es_theme')?>/html5.js"></script>
<![endif]-->

<!-- css3-mediaqueries.js for IE less than 9 -->
<!--[if lt IE 9]>
	<script src="<?=$this->get_config('es_main_url')?>theme/<?=$this->get_config('es_theme')?>/css3-mediaqueries.js"></script>
<![endif]-->

<!-- editsee stuff -->
<script src="<?=$this->get_config('es_main_url')?>includes/nicEdit/nicEdit.js" type="text/javascript"></script>
<script src="<?=$this->get_config('es_main_url')?>includes/jscolor/jscolor.js" type="text/javascript"></script>
<?php echo $this->header; ?>
<?php $xajax->printJavascript(); ?>

</head>

<body>

<div id="page">

	<header id="header">
		
<?php if (file_exists('images/logo.png')) { ?>
		<img src="<?=$this->get_config('es_main_url')?>images/logo.png" alt="<?=$this->get_config('es_title')?>" />
<?php } else { ?>
		<hgroup>
			<h1 id="site-logo"><a href="#"><?=$this->get_config('es_title')?></a></h1>
			<h2 id="site-description"><?=$this->get_config('es_description')?></h2>
		</hgroup>
<?php } ?>

		<nav>
			<?php include('theme/'.$this->get_config('es_theme').'/page_menu.php'); ?>
		</nav>

		<!--<form id="searchform">
			<input type="search" id="s" placeholder="Search">
		</form>-->

	</header>
	<!-- /#header -->
	
	<div id="posts">
		<?php echo $editsee_index; ?>
	</div>
	<!-- /#posts --> 
	
	
	<aside id="sidebar">
			<?php include('theme/'.$this->get_config('es_theme').'/sidebar.php'); ?>
	</aside>
	<!-- /#sidebar -->

	<footer id="footer">
		<?php echo $this->footer; ?>
		<p>Theme based on tutorial at <a href="http://webdesignerwall.com">Web Designer Wall</a>, ported to EditSee by <a href="http://apexad.net">apexad</a></p>
	</footer>
	<!-- /#footer --> 
	
</div>
<!-- /#page -->
<div id="topbar">
<?php echo $this->topbar; ?>
</div>
</body>
</html>
