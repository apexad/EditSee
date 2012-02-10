<?php 
if ($part != 'innerHTML') {
?><div <?=$inside_post_div?>><?php } ?>
	<h1><a href="<?=$this->get_config('es_main_url')?><?=$post['urltag']?>"><?=$post['title']?></a>
		<span class="post-edit"><?=$post['edit']?></span>
	</h1>
	<div class="post-content"><?=$post['content']?></div>
<br />
<div class="comment_bar">
<a href="<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>#comments" class="linkred"><img src="<?=$this->get_config('es_main_url')?>theme/ventixo/images/commentsicon.png" /> <?=$post['comments']?> &gt;&gt;</a>
</div>
<?php if ($part != 'innerHTML') {
?></div><?php } ?>
