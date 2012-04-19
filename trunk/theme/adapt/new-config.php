<?php
session_start();
?>
<html>
	<head>
		<title>adapt theme config</title>
		<script src="http://editsee.com/includes/jscolor/jscolor.js" type="text/javascript"></script>
			<link rel="stylesheet" type="text/css" href="config.css" />
	</head>
<body>
<?php
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
<form name="test" action="" method="post">
	<h1>adapt theme config</h1>
	<label for="post_title_color">Post Title:</label>
		<input type="text" name="post_title_color" id="post_title_color" />
		<input type="checkbox" name="post_title_bold" value="bold" <?php if ($post_title_bold == 'bold') { echo 'checked="checked"'; } ?> /> bold
	<input type="submit" value="submit" />
</form>
<?php
}
else { echo 'you are not logged in!'; }
?>
<script type="text/javascript">
	myPicker = new jscolor.color(document.getElementById('post_title_color'), {hash:true});
	myPicker.fromString('<?=$post_title_color?>')
</script>
</body>