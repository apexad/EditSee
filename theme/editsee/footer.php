<?php 
/* editsee footer file
 * $this is available which has ->footer (which will contain footer stuff plugins use)
 */

?></div> <?php /* ends <div id="posts"> */ ?>
<div id="sidebar">
<?php  require_once('theme/'.$this->get_config('es_theme').'/sidebar.php'); ?>
</div>
</div> <?php /* ends <div id="main"> */ ?>
<div id="footer"></div>
</div> <?php /* ends <div id="full"> */ ?>
</div> <?php /* ends <div id="page"> */ ?>
<?php echo $this->footer; ?>
<div id="topbar">
<?php echo $this->topbar; ?>
</div><?php /* ends <div id="topbar"> */ ?>
</html>
