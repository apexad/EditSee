<?php 
if ($part != 'innerHTML') {
?><div <?=$inside_post_div?>><?php } ?>
	<h1><a href="<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>"><?=$post['title']?></a></h1>
		<ul class="post-info">
			<li><?=date('M jS, Y',strtotime($post['date_entered']))?></li>
			<li class="write-comment"><a href="<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>#comments"><?=$post['comments']?></a></li>
			<li class="post-edit">
				<img src="<?=$this->get_config('es_main_url')?>includes/layout/images/post_edit.png" onclick="xajax_updatePost(<?=$post['id']?>)" title="Edit Post" alt="Edit Post" />
				<img src="<?=$this->get_config('es_main_url')?>includes/layout/images/post_delete.png" onclick="xajax_deletePost(<?=$post['id']?>)" title="Delete Post" alt="Delete Post" />
			</li>
		</ul>
	<div class="post-content"><?=$post['content']?></div>
	<div class="post-meta">
		<ul class="clearfix">
			<li class="post-category"><a href="<?=$this->get_config('es_main_url')?>category/<?=$post['simple_category']?>" rel="category tag"><?=$post['simple_category']?></a></li>
			<li class="post-tag"><a href="#" rel="tag"></a></li>
			<li class="post-comment"><a href="<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>#comments" title="Comment on <?=$post['title']?>"><?=$post['comments']?></a></li>
		</ul>
	</div>
<?php if ($part != 'innerHTML') {
?></div>
<?php } ?>
