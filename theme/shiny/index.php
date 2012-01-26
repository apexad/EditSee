<?php 
/* editsee required header
 * 
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
<div id="full">
<div id="header">
	<h1><img src="<?php echo $this->get_config('es_main_url').'/theme/shiny/images/logo.png'; ?>" ></h1>
</div>
<div id="page">
<div id="posts">
<?php echo $editsee_index; ?>
<?php 
/* editsee required footer
 * 
 * $this is available which has ->footer (which will contain footer stuff plugins use)
 */

?></div> <?php /* ends <div id="posts"> */ ?>
</div> <?php /* ends <div id="page"> */ ?>
<div id="sidebar">
<?php
/* editsee required sidebar
 * 
 * use $this->get_post_titles(limit)
 * returns an array of post titles
 * example below puts them into a list
 * 
 * use $this->get_links(prepend,class,append)
 * returns html code for links in the format: 
 * prepend.<a href="url" class="class">title</a>(delete button if logged in).append
 * 
 * this is the default sidebar below.
 * to use a new sidebar, sidebar.php must be created within the theme
 */
?>
<h3>Recent Posts</h3>
<?php echo '<ul><li>'.implode('</li> <li>',$this->get_post_titles('10',true)).'</li></ul>'; ?>
<h3>Blogroll/Links</h3>
<ul>
<?php echo $this->get_links('<li>','links','</li>'); ?>
</ul>
<h3>Pages</h3>
<?php
 /* editsee required page menu
  * 
  * this is the default page menu below
  * to use a new page menu, page_menu.php must be created within the theme
  */
?>
<ul id="page-menu" class="menu">
<li class="first_menu"><a href="<?=$this->get_config('es_main_url')?>">Home</a></li>
<?php echo $this->get_pages('<li>','page_item','</li>'); ?>
</ul>
</div>
</div> <?php /* ends <div id="full"> */ ?>
<?php echo $this->footer; ?>
<div id="topbar">
<?php echo $this->topbar; ?>
</div>
</html>
