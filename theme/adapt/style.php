<?php
if ($config === true) {
	$filename = 'theme.xml';
}
else {
	$filename = 'theme/'.$this->get_config('es_theme').'/theme.xml';
}

$theme = simplexml_load_file($filename);
foreach ($theme->style as $style) {
	echo $style->element." {\n";
	foreach ($style->css as $css_line) {
		if ($css_line->item == 'font-weight') {
			$current_bold = $css_line->value;
		}
		echo "\t".$css_line->item.': '.$css_line->value.";\n";
	}
	echo "}\n";
}
echo '.boldtoggle { font-weight:'.($current_bold == 'bold' ? 'normal' : 'bold').' !important; }'."\n";
?>