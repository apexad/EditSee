<?php
if ($part != 'innerHTML') {
?><div <?=$inside_post_div?>><?php } ?>
<div class="pt"><h1><a href="<?=$this->get_config('es_main_url')?><?php if (!$this->is_page($post['id'])) echo 'post/'; ?><?=$post['urltag']?>" rel="bookmark" title="Permanent link to <?=$post['title']?>"><?=$post['title']?></a></h1></div>
<div class="text">
<?=$post['content']?>
</div>
<div class="meta">&sect;<?=$post['id']?>&middot; <?=date('F j, Y',strtotime($post['date_entered']))?> &middot; <a href="<?=$this->get_config('es_main_url')?>category/<?=$post['simple_category']?>" rel="category tag"><?=$post['simple_category']?></a> &middot; <!-- tags --> &middot; [<a href="javascript:window.print()">Print</a>] <span class="post-edit">
				[<a href="" onclick="xajax_updatePost(<?=$post['id']?>,'',''); return false;">Edit</a>] 
				[<a href="" onclick="xajax_deletePost(<?=$post['id']?>); return false;">Delete</a>] </span>
</div>
<div class="social">
<ul>
<li><a href="mailto:?subject=<?=$post['title']?>&amp;body=<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>">[E-mail this post]</a></li> 
<li><a href="http://del.icio.us/post?url=<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>&amp;title=<?=$post['title']?>">Save on Delicious</a></li> 
<li><a href="http://reddit.com/submit?url=<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>&amp;title=<?=$post['title']?>">Submit to Reddit</a></li> 
<li><a href="http://www.digg.com/submit?phase=2&amp;url=<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>&amp;title=<?=$post['title']?>">Digg it</a></li> 
<li><a href="http://furl.net/storeIt.jsp?t=<?=$post['title']?>&amp;u=<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>">Store on Furl</a></li> 
<li><a href="http://technorati.com/faves?add=<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>">Fave on Technorati</a></li> 
</div>
<hr />
<?php if ($part != 'innerHTML') {
?>
</div>
<?php } ?>

