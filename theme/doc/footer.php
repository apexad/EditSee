</div> <!-- end posts -->
</div> <!-- end content -->
<div id="sidebar">
<?php include("theme/doc/sidebar.php");?>
</div>
<div id="ad-code-1">
<?=$this->get_ad_code('1')?>
</div>
<div id="footer" class="g33">
<?php echo $this->footer ?>
<a href="<?=$this->get_config('es_main_url')?>"><?=$this->get_config('es_title')?></a> is prowdly powered by <a href="http://editsee.com">editsee</a>. &middot; &copy; <?=date('Y')?> &middot; All rights reserved.
Doc theme by <a href="http://www.wp-content-themes.com/">Theme Museum</a> (ported to editsee by <a href="http://apexad.net">apexad</a>).
</div>
<div class="clear"></div>
</div> <!-- end container -->
<div id="topbar">
<?php echo $this->topbar; ?>
</div><?php /* ends <div id="topbar"> */ ?>
</body>
</html>