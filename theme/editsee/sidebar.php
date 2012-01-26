<?php
/* editsee sidebar
 * use $this->get_post_titles(limit)
 * returns an array of post titles
 * example below puts them into a list
 * 
 * use $this->get_links(prepend,class,append)
 * returns html code for links in the format: 
 * prepend.<a href="url" class="class">title</a>(delete button if logged in).append
 */
?>
<h3 id="information-title" class="side-title"><?=$this->get_config('es_title')?></h3>
<div class="information-contents"><?=$this->get_config('es_description')?></div>
<div id="side_middle" class="clearfix">
	<div id="side-left">
		<div class="side-box-short">
			<h3>Recent Posts</h3>
<?php echo '<ul><li>'.implode('</li> <li>',$this->get_post_titles('10',true)).'</li></ul>'; ?>
		</div>
	</div>
	<div id="side-right">
		<div class="side-box-short">
			<h3>Blogroll/Links</h3>
			<ul>
				<?php echo $this->get_links('<li>','links','</li>'); ?>
			</ul>
		</div>
	</div>
</div>
<div id="side_bottom">
<div class="side-box">
<!-- tag cloud!? -->
</div>
</div>
<div class="side-box">
	<ul id="copyrights">
<li>
Copyright &copy;&nbsp; <?=date('Y')?> &nbsp;
<a href="<?=$this->get_config('es_main_url')?>"><?=$this->get_config('es_title')?></a>
</li>
<li>
Theme designed by
<a href="http://www.mono-lab.net/">mono-lab</a> (ported to editsee by <a href="http://apexad.net">apexad</a>)
</li>
<li id="wp">
Powered by
<a href="http://editsee.org/">editsee</a>
</li>
</ul>
</div>
<div id="ad-code-1">
<?=$this->get_ad_code('1')?>
</div>