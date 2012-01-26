<h3>Recent Posts</h3>
<?php echo '<ul><li>'.implode('</li> <li>',$this->get_post_titles('10',true)).'</li></ul>'; ?>
<h3>Links</h3>
<ul>
<?php echo $this->get_links('<li>','links','</li>'); ?>
</ul>
<h3>Pages</h3>
<ul id="page-menu" class="menu">
<li class="first_menu"><a href="<?=$this->get_config('es_main_url')?>">Home</a></li>
<?php echo $this->get_pages('<li>','page_item','</li>'); ?>
</ul>