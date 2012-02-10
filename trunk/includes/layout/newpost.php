<?php 
/* editsee new post (and edit post) template
 * $post array is available to this file when loaded
 * $project7 variable is also available
 */
?>
<form id="new_post_form" onsubmit="return false;">
	<table class="new-post">
		<tr>
			<td>Title:<br/><textarea name="post_title" id="post_title" onblur="xajax_generatePostData('<?=$post['id']?>','urltag',this.value)"><?=$post['title']?></textarea></td>
		</tr>
		<tr>
			<td>Category:<br/><?php
			?><select id="post_category">
				<?=$project7->get_categories('option',true,'</option>',$post['simple_category'])?>
			</select></select><button onclick="xajax_openPopup('new_category')">new category</button></td>
		</tr>
		<tr>
			<td>
				URLtag <span title="access this post via <?=$project7->get_config('es_main_url')?>post/<urltag>">(?)</span>:
				<br/><input type="text" id="post_urltag" value="<?=$post['urltag']?>"/>
			</td>
		</tr>
		<?php if ($post_type == 'page') { ?>
		<tr>
			<td>In Navigation:<br/><select id="in_nav">
										<option value="1">Yes</option>
										<option value="0" <?php if ($post['in_nav'] == 0) echo 'selected="selected"'; ?>>No</option>
									</select>
			</td>
		</tr>
		<tr>
			<td>Page Order:<br/>
				<?php
				$query = $project7->db->_query("select title,page_order 
								from ".$project7->db->get_table_prefix()."post 
								where type='page' and deleted='0' order by title desc");
				if ($post['id'] != 'new') {
					$after_page = $project7->db->_query("select page_order from ".$project7->db->get_table_prefix()."post 
					where page_order < '".$post['page_order']."' and type='page' order by page_order desc limit 1");
					$begin = true;
					if ($after_page->_num_rows() == 1) {
						$after_page = $after_page->_result(0);
						$begin = false;
					}
				}
				?>
				<select id="page_order_position">
					<option value="end">At End</option>
					<option value="begin" <?php if ($begin) { echo ' selected="selected"'; } ?>>At Beginning</option>
					<option value="after" <?php if (!$begin) { echo ' selected="selected"'; } ?>>After</option>
				</select>
				<select id="page_order_after">
				<option value=""></option>
				<?php
				while($row = $query->_fetch_assoc()) {
					echo '<option value="'.$row['page_order'].'"';
					if ($row['page_order'] == $after_page) {
						echo ' selected="selected"';
					}
					echo '>'.$row['title'].'</option>';
				}
				?>
				</select>
			</td>
		</tr>
		<?php
		}
		?>
		<tr><td>Post Date (format- YYYY-MM-DD hh:mm:ss, 24 hour): <button onclick="xajax_generatePostData('<?=$post['id']?>','reset-date')">Reset To Now</button><br/>
			<input type="text" id="post_date" value="<?php
			if (!empty($post['date_entered'])) {
				echo $post['date_entered'];
				}
			else {
				echo date('Y-m-d H:i:s');
				}
			?>" /></td></tr>
			<tr><td><div id="post_draft_status"></div></td></tr>
		<tr>
			<td><textarea id="post_content" style="height:8em;"><?=$post['content']?></textarea></td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="post_id" id="post_id" value="<?=$post['id']?>" />
				<input type="hidden" name="post_type" id="post_type" value="<?=$post_type?>" />
<?php if ($project7->isPoster()) { ?>
				<button onclick="xajax_addPost(
											document.getElementById('post_id').value
											,document.getElementById('post_title').value
											,mynicEditornew.instanceById('post_content').getContent()
											,(document.getElementById('post_category').options[document.getElementById('post_category').selectedIndex]).value
											,document.getElementById('post_urltag').value
											,document.getElementById('post_type').value
											,document.getElementById('post_date').value
											<?php if ($post_type == 'page') { ?>
											,(document.getElementById('in_nav').options[document.getElementById('in_nav').selectedIndex]).value
											,(document.getElementById('page_order_position').options[document.getElementById('page_order_position').selectedIndex]).value
											,(document.getElementById('page_order_after').options[document.getElementById('page_order_after').selectedIndex]).value
											<?php } else { echo ',0,0,0'; } ?>,0
										); clearTimeout(draft)">save &amp; publish post</button>
<?php } ?>
				<button onclick="xajax_addPost(
											document.getElementById('post_id').value
											,document.getElementById('post_title').value
											,mynicEditornew.instanceById('post_content').getContent()
											,(document.getElementById('post_category').options[document.getElementById('post_category').selectedIndex]).value
											,document.getElementById('post_urltag').value
											,document.getElementById('post_type').value
											,document.getElementById('post_date').value
											<?php if ($post_type == 'page') { ?>
											,(document.getElementById('in_nav').options[document.getElementById('in_nav').selectedIndex]).value
											,(document.getElementById('page_order_position').options[document.getElementById('page_order_position').selectedIndex]).value
											,(document.getElementById('page_order_after').options[document.getElementById('page_order_after').selectedIndex]).value
											<?php } else echo ',0,0,0'; ?>,-1
										); clearTimeout(draft);">save as draft (unpublished)</button>
				<button onclick="xajax_cancel_addPost('<?=$post['id']?>','<?=$post_type?>')">cancel</button>
			</td>
		</tr>
	</table>
</form>
