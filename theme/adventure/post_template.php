
<?php 
if ($part != 'innerHTML') {
?><div <?=$inside_post_div?>><?php } ?>
<div class="imagez">
<div class="label"><a href="<?=$this->get_config('es_main_url')?><?php if (!$this->is_page($post['id'])) echo 'post/'; ?><?=$post['urltag']?>"><?=$post['title']?></a></div></div>
<?=$post['content']?>
<div class="clearz"><div class="tags">
<span class="post-edit">
<a href="" onclick="xajax_updatePost(<?=$post['id']?>,'',''); return false;">Edit</a> - 
<a href="" onclick="xajax_deletePost(<?=$post['id']?>); return false;">Delete</a> -
</span>

<a href="<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>#comments"><?=$post['comments']?></a>,
<?=date('F jS, Y',strtotime($post['date_entered']))?> , <a href="<?=$this->get_config('es_main_url')?>category/<?=$post['simple_category']?>" rel="category tag"><?=$post['simple_category']?></a> </div></div></div>
