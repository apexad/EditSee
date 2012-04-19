	<article  class="post" id="theme-config">
			<header>
				<h1 class="post-title"><a id="post-title">Adapt Theme Config</a></h1>
				<p class="post-meta"><time class="post-date" datetime="<?=date('Y-m-d')?>" pubdate><?=date('M jS, Y')?></time> <em>in</em> <a rel="tag">Theme Config</a></p>
			</header><!--
			<figure class="post-image"> 
				<img src="images/sample-image.jpg" alt=""/> 
			</figure>-->
			<div class="post-content">
	<form enctype="multipart/form-data" method="post" action="">
	<table>
		<tr>
			<td>Post Title Color:</td>
			<td>
				<input type="text" name="post_title_color" id="post_title_color"
				onchange="document.getElementById('post-title').style.color=this.value" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
				<!--<input type="submit" value="save">-->
			</td>
		</tr>
	</table>
	</form>
			</div>
		</article>