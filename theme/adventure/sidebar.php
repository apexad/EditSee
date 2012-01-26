<ul>
	<li><h2>Pages</h2>
		<ul>
			<?php include('theme/'.$this->get_config('es_theme').'/page_menu.php'); ?>
		</ul></li>
	<li><h2>Recent Posts</h2>
		<ul>
			<li><?php echo implode('</li> <li>',$this->get_post_titles('10',true)); ?></li>
		</ul>
	</li>
	<li>
		<h2>Blogroll</h2>
		<ul>
			<?php echo $this->get_links('<li>','links','</li>'); ?>
		</ul>
	</li>
</ul>