<?php
session_start();
if(isset($_SESSION['username'])) {
$filename = 'theme.xml';

$theme = simplexml_load_file($filename);

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
	$style_count++;
}
$theme->asXML('theme.xml');
?>
<html>
	<head>
		<title>adapt theme config</title>
		<script src="/includes/jscolor/jscolor.js" type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="stylesheet" type="text/css" href="/includes/layout/main.css" />
		<style type="text/css">
			<?php $config = true; include('style.php'); ?>
			body { background:white !important; }
			.input { border: 1px solid #006; }
		</style>
	</head>
<body>
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
		<input type="checkbox" name="post_title_bold" value="bold" <?php if ($post_title_bold == 'bold') { echo 'checked="checked"'; } ?> /> bold
	<br/><br/><input type="submit" value="submit" />
</form>
			</div>
		</article>
<?php
}
else { echo 'you are not logged in!'; }
?>
<script type="text/javascript">
	myPicker = new jscolor.color(document.getElementById('post_title_color'), {hash:true});
	myPicker.fromString('<?=$post_title_color?>')
</script>
</body>