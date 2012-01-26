<?php 
if ($part != 'innerHTML') {
?><div <?=$inside_post_div?>><?php } ?>
	<h1><a href="<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>"><?=$post['title']?></a>
		<span class="post-edit">
		<img src="<?=$this->get_config('es_main_url')?>includes/layout/images/post_edit.png" onclick="xajax_updatePost(<?=$post['id']?>)" title="Edit Post" alt="Edit Post" />
		<img src="<?=$this->get_config('es_main_url')?>includes/layout/images/post_delete.png" onclick="xajax_deletePost(<?=$post['id']?>)" title="Delete Post" alt="Delete Post" />
		</span>
	</h1>
		<div class="date_bar">
			<img src="<?=$this->get_config('es_main_url')?>theme/ventixo/images/dateicon.png" /> <?=date('M jS, Y',strtotime($post['date_entered']))?>
			in <a href="<?=$this->get_config('es_main_url')?>category/<?=$post['simple_category']?>" rel="category tag"><?=$post['simple_category']?></a>
		</div>	
	<div class="post-content"><?=$post['content']?></div>
<br />
<div class="comment_bar">
<a href="<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>#comments" class="linkred"><img src="<?=$this->get_config('es_main_url')?>theme/ventixo/images/commentsicon.png" /> <?=$post['comments']?> &gt;&gt;</a>
</div>
<?php if ($part != 'innerHTML') {
?></div><?php } ?>
