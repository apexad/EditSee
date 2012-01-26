<div id="comments">
	<?=$this->get_comments($post_id)?>
</div>
<?php
session_start();
$rand1 = rand(1,9);
$rand2 = rand(1,9);
$_SESSION['rand1'] = $rand1;
$_SESSION['rand2'] = $rand2;
?>
<div id="new-comment-form">
<table>
<tr><td>name:</td><td><input type="text" id="comment_name" /></td></tr>
<tr><td>e-mail:</td><td><input type="text" id="comment_email" /></td></tr>
<tr><td>what is <?=$rand1?> + <?=$rand2?>:</td><td><input type="text" id="comment_humancheck"/></td></tr>
<tr><td>comment:</td><td><textarea id="comment_text">enter comment here</textarea></td></tr>
<tr><td colspan="2"><button onclick="xajax_addComment(	document.getElementById('comment_name').value
														,document.getElementById('comment_email').value
														,document.getElementById('comment_humancheck').value
														,document.getElementById('comment_text').value
														,'<?=$post_id?>')"
					>add comment</button></td></tr>
</table>
</div>