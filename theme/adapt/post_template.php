<?php
if ($part != 'innerHTML') {
?>		<article  <?=$inside_post_div?>>
<?php } ?>
			<header>
				<h1 class="post-title"><a href="<?=$this->get_config('es_main_url')?>post/<?=$post['urltag']?>"><?=$post['title']?></a></h1>
				<p class="post-meta"><time class="post-date" datetime="<?=date('Y-m-d H:i:s',strtotime($post['date_entered']))?>" pubdate><?=date('M jS, Y (g:ia)',strtotime($post['date_entered']))?></time> <em>in</em> <a href="<?=$this->get_config('es_main_url')?>category/<?=$post['simple_category']?>" rel="tag"><?=$post['simple_category']?></a></p>
				<div class="post-edit">
					<?=$post['edit']?>
				</div>
			</header><!--
			<figure class="post-image"> 
				<img src="images/sample-image.jpg" alt=""/> 
			</figure>-->
			<div class="post-content"><?=$post['content']?></div>
<?php
if ($part != 'innerHTML') {
?>
		</article>
<?php } ?>
