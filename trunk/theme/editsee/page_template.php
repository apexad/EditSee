<?php 
if ($part != 'innerHTML') {
?><div <?=$inside_post_div?>><?php } ?>
	<h1><a href="<?=$this->get_config('es_main_url')?><?=$post['urltag']?>"><?=$post['title']?></a>
	<span class="post-edit">
	<?=$post['edit']?>
	</span></h1>
	<div class="post-content"><?=$post['content']?></div>
	<div class="post-meta">
		<ul class="clearfix">
			<li class="post-category"><a href="<?=$this->get_config('es_main_url')?>category/<?=$post['simple_category']?>" rel="category tag"><?=$post['simple_category']?></a></li>
		</ul>
	</div>
<?php if ($part != 'innerHTML') {
?></div>
<?php } ?>