<div id="notfooter">
<p>
<?php echo $this->title; ?> is proudly powered by <a href="http://editsee.com/">editsee</a> and the Theme <a href="http://schwarttzy.com/web-design/backpacking-wordpress-theme-1-0">Adventure by Eric Schwarz</a> (ported to editsee by apexad)
<br />
</p>
</div>

</div>

<div id="endspacer">
</div>
<?php echo $this->footer; ?>
<div id="bottombar">
<ul>
<?php include('theme/'.$this->get_config('es_theme').'/page_menu.php'); ?>
</ul>
<div id="title"><a href="<?=$this->get_config('es_main_url')?>"><?=$this->get_config('es_title')?></a></div>
    

    
<div id="slogan"><h2><?=$this->get_config('es_description')?></h2></div>   
    
</div>
<div id="topbar">
<?php echo $this->topbar; ?>
</div><?php /* ends <div id="topbar"> */ ?>
</body>