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
<div class="sidebar_header"><img src="<?=$this->get_config('es_main_url')?>theme/ventixo/images/bulletpoint.png" /> Recent Posts</div>
<div class="sidebar_content">
<?php echo '<ul><li>'.implode('</li> <li>',$this->get_post_titles('10',true)).'</li></ul>'; ?>
</div>
<div class="sidebar_header"><img src="<?=$this->get_config('es_main_url')?>theme/ventixo/images/bulletpoint.png" /> Blogroll</div>
<div class="sidebar_content">
	<ul>
		<?php echo $this->get_links('<li>','links','</li>'); ?>
	</ul>
</div>
<div class="sidebar_header"><img src="<?=$this->get_config('es_main_url')?>theme/ventixo/images/bulletpoint.png" /> Pages</div>
<div class="sidebar_content" id="page-menu">
<?php include('theme/'.$this->get_config('es_theme').'/page_menu.php'); ?>
</div>
<div class="sidebar_header"><img src="<?=$this->get_config('es_main_url')?>theme/ventixo/images/bulletpoint.png" /> Calendar</div>
<div class="sidebar_content1"><br />
<table class="calendar">
<tr><td colspan="7"><strong><?=date('M Y')?></strong></td></tr>
<tr><td>Sun</td><td>Mon</td><td>Tues</td><td>Wed</td><td>Thurs</td><td>Fri</td><td>Sat</td></tr>
<?php 
	$timestamp = mktime(0,0,0,date('n'),1,date('Y'));
	$maxday    = date("t",$timestamp);
	$thismonth = getdate ($timestamp);
	$startday  = $thismonth['wday'];
	for ($i=0; $i<($maxday+$startday); $i++) {
		if (($i % 7) == 0 ) echo '<tr>';
		if ($i < $startday) { echo '<td></td>'; }
		else {
			$day = ($i - $startday + 1);
			echo '<td>';
			if ($day == date('j')) echo '<span>'.$day.'</span>';
			else echo $day;
			echo '</td>';
		}
		if(($i % 7) == 6 ) echo '</tr>';
	}
?>
</table>
<div id="ad-code-1">
<?=$this->get_ad_code('1')?>
</div>
</div>
