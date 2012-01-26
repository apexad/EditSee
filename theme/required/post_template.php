<?php 
/* editsee post template
 * $inside_post_div available (put inside the post div)
 * $post array is available (includes id,title,content,date_entered)
 * $loggedin is availbe and is true/false
 * $this should be as well
 */
if ($part != 'innerHTML') {
?><div <?=$inside_post_div?>><?php } ?>
	<h1><a href="<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>"><?=$post['title']?></a></h1>
		<div class="post-info">
			<span><?=date('M jS, Y',strtotime($post['date_entered']))?></span>
			<div class="post-edit">
				<img src="<?=$this->get_config('es_main_url')?>includes/layout/images/post_edit.png" onclick="xajax_updatePost(<?=$post['id']?>)" title="Edit Post" alt="Edit Post" />
				<img src="<?=$this->get_config('es_main_url')?>includes/layout/images/post_delete.png" onclick="xajax_deletePost(<?=$post['id']?>)" title="Delete Post" alt="Delete Post" />
			</div>
		</div>
	<div class="post-content"><?=$post['content']?></div>
	<div class="post-meta">
			<span class="post-category"><a href="<?=$this->get_config('es_main_url')?>category/<?=$post['simple_category']?>" rel="category tag"><?=$post['simple_category']?></a></span>
	</div>
<?php if ($part != 'innerHTML') {
?></div>
<?php } ?>