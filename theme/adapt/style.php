<?php
$filename = 'theme/'.$this->get_config('es_theme').'/theme.xml';

$theme = simplexml_load_file($filename);
foreach ($theme->style as $style) {
	echo $style->element." {\n";
	foreach ($style->css as $css_line) {
		echo "\t".$css_line->item.': '.$css_line->value.";\n";
	}
	echo "}\n";
}
?>