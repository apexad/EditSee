<?php
$filename = 'theme/adapt/theme.xml';
//else { $filename = 'theme.xml'; }

$theme = simplexml_load_file($filename);
//print_r($theme);
$style_count = 0;
foreach ($theme->style as $style) {
	if ($style->element == '.post-title a') {
		$css_count = 0;
		foreach ($style->css as $css_line) {
			if ($css_line->item == 'color') {
				if (array_key_exists('post_title_color', $_REQUEST)) {
					$theme->style[$style_count]->css[$css_count]->value = $_REQUEST['post_title_color'];
				}
				$post_title_color = $theme->style[$style_count]->css[$css_count]->value;
			}
			if ($css_line->item == 'font-weight') {
				if (array_key_exists('post_title_color', $_REQUEST)) {
					if (str_word_count($_REQUEST['post_title_bold']) == 1) {
						$theme->style[$style_count]->css[$css_count]->value = 'bold';
					}
					else {
						$theme->style[$style_count]->css[$css_count]->value = 'normal';
					}
				}
				$post_title_bold = $theme->style[$style_count]->css[$css_count]->value;
			}
			$css_count++;
		}
	}
	if ($style->element == '#posts') {
		$css_count = 0;
		foreach ($style->css as $css_line) {
			if ($css_line->item == 'opacity') {
				if (array_key_exists('post_opacity', $_REQUEST)) {
					$theme->style[$style_count]->css[$css_count]->value = $_REQUEST['post_opacity'];
					$theme->style[$style_count]->css[$css_count+1]->value = 'alpha(opacity='.(floatval($_REQUEST['post_opacity'])*100).')';
				}
				$post_opacity = $theme->style[$style_count]->css[$css_count]->value;
			}
			$css_count++;
		}
	}
	$style_count++;
}
$theme->asXML($filename);
if ($load_script == 'yes') {
	if (!array_key_exists('theme_config',$_REQUEST)) {
		echo '
		myPicker = new jscolor.color(document.getElementById("post_title_color"), {hash:true});
		myPicker.fromString("'.$post_title_color.'")';
	}
}
else {
?>
	<article  class="post" id="theme-config">
			<header>
				<h1 class="post-title"><a id="post-title">Adapt Theme Config</a></h1>
				<p class="post-meta"><time class="post-date" datetime="<?=date('Y-m-d')?>" pubdate><?=date('M jS, Y')?></time> <em>in</em> <a rel="tag">Theme Config</a></p>
			</header><!--
			<figure class="post-image"> 
				<img src="images/sample-image.jpg" alt=""/> 
			</figure>-->
			<div class="post-content">
<form name="test" action="" method="post">
	<label for="post_title_color">Post Title:</label>
		<input type="text" name="post_title_color" id="post_title_color" 
		onchange="document.getElementById('post-title').style.color=this.value" />
		<input type="checkbox" name="post_title_bold" value="bold"
		<?php if ($post_title_bold == 'bold') {
			 echo 'checked="checked"';
		} ?> 
		onclick="$('#post-title').toggleClass('boldtoggle');"/> bold
	<br/><label for="post_opacity">Posts Opacity:</label>
		<select name="post_opacity"
		onchange="document.getElementById('posts').style.opacity=this.value">
			<option value="0.5" <?php echo ($post_opacity=='0.5' ? 'selected="selected"' : '' ); ?>>50%</option>
			<option value="0.6" <?php echo ($post_opacity=='0.6' ? 'selected="selected"' : '' ); ?>>60%</option>
			<option value="0.7" <?php echo ($post_opacity=='0.7' ? 'selected="selected"' : '' ); ?>>70%</option>
			<option value="0.8" <?php echo ($post_opacity=='0.8' ? 'selected="selected"' : '' ); ?>>80%</option>
			<option value="0.9" <?php echo ($post_opacity=='0.9' ? 'selected="selected"' : '' ); ?>>90%</option>
			<option value="1"   <?php echo ($post_opacity=='1' ? 'selected="selected"' : '' ); ?>>Opaque/No Opacity</option>
		</select>
	<br/><br/><input name="theme_config" type="submit" value="submit" />
</form>
			</div>
		</article>
<?php
}
?>