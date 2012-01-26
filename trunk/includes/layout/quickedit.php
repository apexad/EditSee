<div class="new-post">
Title:<br/><textarea id="post_title-<?=$post['id']?>"><?=$post['title']?></textarea>
<br/>Content:<br/>
<textarea id="post_content-<?=$post['id']?>"><?=$post['content']?></textarea>
<button onclick="xajax_quickEditPost('<?=$post['id']?>',mynicEditor<?=$post['id']?>.instanceById('post_content-<?=$post['id']?>').getContent(),document.getElementById('post_title-<?=$post['id']?>').value)">save post</button>
<button onclick="xajax_cancel_addPost('<?=$post['id']?>')">cancel</button>
<button onclick="xajax_updatePost('<?=$post['id']?>','inquick',mynicEditor<?=$post['id']?>.instanceById('post_content-<?=$post['id']?>').getContent())">full editor</button>
</div>