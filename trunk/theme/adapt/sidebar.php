		<section class="widget">
			<h4 class="widgettitle">About</h4>
			<?=$this->get_config('es_description')?>
		</section>
		
		<section class="widget">
			<h4 class="widgettitle">Recent Posts</h4>
			<?php echo '<ul><li>'.implode('</li> <li>',$this->get_post_titles('10',true)).'</li></ul>'; ?>
		</section>

		<section class="widget">
			<h4 class="widgettitle">Links</h4>
			<ul>
			<?php echo $this->get_links('<li>','links','</li>'); ?>
			</ul>
		</section>
		<!-- flicker widget
		<section class="widget clearfix">
			<h4 class="widgettitle">Flickr</h4>
			<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=8.&display=latest&size=s&layout=x&source=user&user=52839779@N02"></script> 
		</section>
		-->
