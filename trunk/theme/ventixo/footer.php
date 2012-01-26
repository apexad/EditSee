<?php 
/* editsee footer file
 * $this is available which has ->footer (which will contain footer stuff plugins use)
 */

?></div> <?php /* ends <div id="posts"> */ ?>
<div id="sidebar">
<?php  require_once('theme/'.$this->get_config('es_theme').'/sidebar.php'); ?>
</div>
</div> <?php /* ends <div id="main"> */ ?>
</div> <?php /* ends <div id="full"> */ ?>
<div id="footer">

copyright &copy; <script type="text/javascript"> 
<!--
var currentTime = new Date();
document.write(currentTime.getFullYear());
//-->
</script> Zooksee - All Rights Reserved - Powered by <a href="http://www.zooksee.com" class="linkred"> editSee</a>
</div>
</div> <?php /* ends <div id="page"> */ ?>
<?php echo $this->footer; ?>
<div id="topbar">
<?php echo $this->topbar; ?>
</div><?php /* ends <div id="topbar"> */ ?>
</html>
