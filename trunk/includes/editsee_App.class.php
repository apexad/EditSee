<?php
/* editsee_App.class.php - editsee application class 
 * author: Alex 'apex' Martin
 * date: may 2011
*/
require_once("includes/xajax.php");
require_once('includes/database/editsee_Database.class.php');
class editsee_App {
	
	const version = '0.1';
	public $db;
	public $title;
	public $header;
	public $footer;
	public $topbar;
	
	public $is_posts;
	public $is_post;
	public $is_page;
	public $is_category;
	public $is_404;
	public $is_feed;
	
	private $config_file;

	public function editsee_App() {
		//check if a confilg file exits
		if ($this->configFileExists() !== false) {
			$this->config_file = $this->configFileExists();
			//load the database, populate $this->page
			require_once($_SERVER['DOCUMENT_ROOT'].'/'.$this->config_file);
			$this->db = new editsee_Database($type, trim($host), trim($user), trim($password), trim($database),trim($table_prefix));
			$this->title = $this->get_config('es_title');
			$this->header = "\n".'<link rel="alternate" type="application/rss+xml" title="'.$this->get_config('es_title').' &raquo; Feed" href="'.$this->get_config('es_main_url').'feed/" />';
			$this->header .= "\n".'<link rel="stylesheet" type="text/css" href="'.$this->get_config('es_main_url').'includes/layout/main.css" />';
			if ($this->loggedIn()) {
				$this->header .= "\n".'<link rel="stylesheet" type="text/css" href="'.$this->get_config('es_main_url').'includes/layout/topbar.css" />';
				$this->header .= "\n".'<link rel="stylesheet" type="text/css" href="'.$this->get_config('es_main_url').'includes/layout/loggedin.css" />';
			}
			$this->header .= "\n".'<meta name="generator" content="editsee '.self::version.'" />';
			$this->footer = '<div id="overlay" style="display: none; position: absolute; top: 0pt; left: 0pt; z-index: 778; width: 100%; height: 2695px;"></div>';
			$custom_footer = $this->db->_query("select label,data from ".$this->db->get_table_prefix()."custom where section='footer'");
			while ($cf_row = $custom_footer->_fetch_assoc()) {
				$this->footer .= "\n".'<!-- start '.$cf_row['label'].' -->'."\n".stripslashes($cf_row['data'])."\n".'<!-- end '.$cf_row['label'].'-->'."\n";
			}

		}
		else {
			$this->title = 'editsee install';
		}
	}
	
	public static function configFileExists() {
		//check for a config file
		//echo $_SERVER['DOCUMENT_ROOT'];
		$handler = opendir($_SERVER['DOCUMENT_ROOT'].'/');

		while ((false !== ($file = readdir($handler)))) {
			if (preg_match('/\w*config\w*\.php/',$file) == 1)
				$config_file = $file;
		}
		if (isset($config_file)) {
			return $config_file;
		}
		else {
			return false;
		}
	}
	public function connectDatabase() {
		if ($this->configFileExists() !== false) {
			$this->config_file = $this->configFileExists();
			//load the database, populate $this->page
			require_once($this->config_file);
			$this->db = new editsee_Database($type, trim($host), trim($user), trim($password), trim($database),trim($table_prefix));
		}
	}

	public function loggedIn() {
		session_start();
	if(isset($_SESSION['username']))
		return true;
	else
		return false;
	}
	public function isAdmin() {
		//first ever auto-database update for EditSee, completed 02-01-2012
		$test_role = $this->db->_query("SHOW COLUMNS FROM  `".$this->db->get_table_prefix()."user` LIKE  'role'");
		if ($test_role->_num_rows() == 0) {
			//add role field
			$this->db->_query("ALTER TABLE  `".$this->db->get_table_prefix()."user` ADD  `role` VARCHAR( 6 ) NOT NULL AFTER  `username`");
			$this->db->_query("UPDATE `".$this->db->get_table_prefix()."user` set role='admin'"); //default all current users to admins
		}
		$admin_check = $this->db->_query("select role from `".$this->db->get_table_prefix()."user` where username='".$_SESSION['username']."'");
		if ($admin_check->_result(0) == 'admin') {
			return true;
		}
		else {
			return false;
		}
	}
	public function notLoggedIn($needsAdmin = false) {
		$message = 'Not logged in';
		if ($needsAdmin) {;
			$message .= ' as an Admin';
		}
		return $message.'!';
	}
	public function display($part = 'all') {
		switch ($part) {
			case 'page-menu-only':
				if (file_exists('theme/'.$this->get_config('es_theme').'/page_menu.php')) {
					$page_menu_file = 'theme/'.$this->get_config('es_theme').'/page_menu.php';
				}
				else {
					$page_menu_file = 'includes/layout/theme//page_menu.php';
				}
				ob_start();
				include($page_menu_file);
				$page_menu = ob_get_contents();
				ob_end_clean();
				echo $page_menu;
			break;
			case 'posts-only':
				echo '<div id="new-post"></div>';
				$this->get_posts();
			break;
			case 'topbar-only':
				ob_start();
				include('includes/layout/topbar.php');
				$this->topbar = ob_get_contents();
				ob_end_clean();
				echo $this->topbar;
			break;
			case 'sidebar-only':
				if (file_exists('theme/'.$this->get_config('es_theme').'/sidebar.php')) {
					$sidebar_file = 'theme/'.$this->get_config('es_theme').'/sidebar.php';
				}
				else {
					$sidebar_file = 'includes/layout/theme/sidebar.php';
				}
				ob_start();
				include($sidebar_file);
				$sidebar = ob_get_contents();
				ob_end_clean();
				echo $sidebar;
			break;
			case 'first-load':
				if ($this->configFileExists()) {
					ob_start();
					include('includes/layout/topbar.php');
					$this->topbar = ob_get_contents();
					ob_end_clean();
				
					$script_uri = 'http://'.str_replace('www.','',$_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI'];
					if (substr($script_uri,-1) != '/') { $script_uri .= '/'; }
					$editsee_request = str_replace(str_replace('www.','',$this->get_config('es_main_url')),'',$script_uri);
					
					if (substr($editsee_request,0,5) == 'feed/') {
						include("includes/RSSFeed.class.php");
						header("Content-type: text/xml; charset=UTF-8");
						$myfeed = new RSSFeed();
						$myfeed->SetChannel($this->get_config('es_main_url'),
											$this->get_config('es_title'),
											$this->get_config('es_description'),
											'en-us',
											date(Y).' '.$_SERVER['HTTP_HOST'],
											'webmaster@'.$_SERVER['HTTP_HOST'],
											$this->get_config('es_title'));
						$myfeed->SetImage('');

						$query = $this->db->_query("select id,title,urltag,content,date_entered from ".$this->db->get_table_prefix()."post 
													where type='post' and deleted='0' and (date_entered <= NOW())
													order by date_entered desc");
						while ($post = $query->_fetch_assoc()) {
							$post['content'] = strip_tags($post['content'],'<br/><br>');
							if (strpos($post['content'],'!--full-post--!')) {
								$post['content'] = substr($post['content'],0,strpos($post['content'],'!--full-post--!'));
								$add_dots = true;
							}
							else {
								if (strlen($post['content']) > 280) {
									$post['content'] = substr(substr($post['content'],0,280),0,strrpos(substr($post['content'],0,280),' '));
									$add_dots = true;
								}
							}
							$post['content'] = htmlentities(stripslashes($post['content']));
							if ($add_dots) { $post['content'] .= ' [...]'; }
							$myfeed->SetItem($this->get_config('es_main_url').'post/'.$post['id'],
							$this->get_config('es_main_url').'post/'.$post['urltag'],
					 		$post['title'],
					 		$post['date_entered'],
					 		$post['content']);
						}
						
						echo $myfeed->output();
						exit();
					}
					
					$editsee_index = '';
					
					if (($editsee_request == '') || ($editsee_request == ($this->get_config('es_postpage')).'/') || ($editsee_request == 'index.php/') ) {
						$post_start = 0;
						$page_number = 1;
						if (($this->get_config('es_homepage') == '!posts!') || ($editsee_request == ($this->get_config('es_postpage')).'/')) {
							$this->is_posts = true;
						}
						else {
							$editsee_request = ($this->get_config('es_homepage')).'/';
						}
					}
					if (substr($editsee_request,0,5) == 'page/') {
						$page_number = substr(substr($editsee_request,5),0,strpos(substr($editsee_request,5),'/'));
						$post_start = ($page_number-1)*$this->get_config('es_posts_per_page');
						$this->is_posts = true;
					}
					if ($this->is_posts) {
						ob_start();
						$this->get_posts($post_start);
						$editsee_index .= ob_get_contents();
						ob_end_clean();
					}
					else {
						$query = $this->db->_query("select id,title from ".$this->db->get_table_prefix()."post where urltag='".substr($editsee_request,0,-1)."'");
						if ($query->_num_rows() == 1) {
							$this->is_page = true;
							$page = $query->_fetch_assoc();
							$this->title .= ' - '.$page['title'];
							$editsee_index .=  $this->get_single_post($page['id'],'full','page');
						}
						else {
								if (substr($editsee_request,0,5) == 'post/') {
									if (!$this->loggedIn()) { $if_notloggedin = ' and (date_entered <= NOW())'; }
									$query = $this->db->_query("select id,title from ".$this->db->get_table_prefix()."post 
																where (urltag='".substr($editsee_request,5,-1)."' or id='".substr($editsee_request,5,-1)."') 
																and deleted=0".$if_notloggedin);
									if ($query->_num_rows() == 1) {
										$this->is_post = true;
										$post = $query->_fetch_assoc();
										$this->title .= ' - '.$post['title'];
										$editsee_index .= $this->get_single_post($post['id'],'full','post');
										$post_id = $post['id'];
										
										ob_start();
										include("includes/layout/comment_form.php");
										$comment_form = ob_get_contents();
										ob_end_clean();
										
										$editsee_index .= $comment_form;
									}
									else {
										$this->is_404 = true;
										$editsee_index .= '404 not found';
									}
								}
								else if (substr($editsee_request,0,9) == 'category/') {
										$this->is_category = true;
										$category = substr(substr($editsee_request,9),0,strpos(substr($editsee_request,9),'/'));
										ob_start();
										$this->get_posts(0,$category);
										$editsee_index = ob_get_contents();
										ob_end_clean();
								}
								else {
										$this->is_404 = true;
										$editsee_index .= '404 not found';
									}
							}
					}
					
					if (file_exists('theme/'.$this->get_config('es_theme').'/header.php')) {
						require_once('theme/'.$this->get_config('es_theme').'/header.php');
						echo $editsee_index;
					}
					
					if (file_exists('theme/'.$this->get_config('es_theme').'/index.php')) {
						require_once('theme/'.$this->get_config('es_theme').'/index.php');
					}

					if (file_exists('theme/'.$this->get_config('es_theme').'/footer.php')) {
						require_once('theme/'.$this->get_config('es_theme').'/footer.php');
					}
				}
				else {
					ob_start();
					include("includes/layout/install.php");
					$install_page = ob_get_contents();
					ob_end_clean();
					echo $install_page;
				}
		}
	}
	public function get_config($option) {
		$query = $this->db->_query("select data from ".$this->db->get_table_prefix()."config where `option`='".$this->db->_escape_string($option)."'");
		if ($query->_num_rows() == 1) {
			$data = $query->_result(0);
			if ($option == 'es_main_url') {
				if (substr($data,-1) != '/') { $data =  $data.'/'; }
			}
			if (($option == 'es_theme') && ($_SESSION['temp_theme'] != '')) {
				$data = $_SESSION['temp_theme'];
			}
		}
		else {
			$this->db->_query("insert into `".$this->db->get_table_prefix()."config`(`option`,`data`) values('".$this->db->_escape_string($option)."','')");
			$data = '';
		}
		return stripslashes($data);
	}
	public function post_select($extra = '') {
		return "select 
		id,title,content,urltag,in_nav,post.type,date_entered,date_entered
		,(date_entered <= NOW()) as `live`,tag as `simple_category`,page_order
		from ".$this->db->get_table_prefix()."post post 
		left join ".$this->db->get_table_prefix()."post_tags post_tags on (post.id=post_tags.post_id and post_tags.type='cat')
		left join ".$this->db->get_table_prefix()."tags tags on tags.tag_id=post_tags.tag_id
		where deleted=0 ".$extra; 
	}
	public function new_post_select($extra_where,$start) {
		if ($this->loggedIn()) {
			return $this->db->_limit_query($this->db->get_table_prefix()."post post
			left join ".$this->db->get_table_prefix()."post_tags post_tags on (post.id=post_tags.post_id and post_tags.type='cat')
			left join ".$this->db->get_table_prefix()."tags tags on tags.tag_id=post_tags.tag_id",'id','id,title,content,tag as `simple_category`,urltag,post.type,date_entered,(date_entered <= NOW()) as `live`',$start,$this->get_config('es_posts_per_page'),"deleted=0 and post.type='post'".$extra_where,'date_entered desc');
		}
		else {
			return $this->db->_limit_query($this->db->get_table_prefix()."post post
			left join ".$this->db->get_table_prefix()."post_tags post_tags on (post.id=post_tags.post_id and post_tags.type='cat')
			left join ".$this->db->get_table_prefix()."tags tags on tags.tag_id=post_tags.tag_id",'id','id,title,content,tag as `simple_category`,urltag,post.type,date_entered,(date_entered <= NOW()) as `live`',$start,$this->get_config('es_posts_per_page'),"deleted=0 and post.type='post' and date_entered <= NOW()".$extra_where,'date_entered desc');
		}
	}
	public function get_single_post($post_id,$part='full',$type='post') {
		if ($this->is_page($post_id))
			$type='page';
		$query = $this->db->_query($this->post_select("and id='".$this->db->_escape_string($post_id)."'",$type));
		$post = $query->_fetch_assoc();
		$loggedin = $this->loggedIn();
		$post['title'] = stripslashes($post['title']);
		if ($post['live'] == '0')
					$post['title'] .= ' (Not Live Until '.date('M jS, Y g:ia',strtotime($post['date_entered'])).')';
		$post['content'] = stripslashes($post['content']);
		if (($_SERVER['REQUEST_URI'] != '/post/'.$post['urltag']) && (strpos($post['content'],'!--full-post--!'))) {
			$post['content'] = substr($post['content'],0,strpos($post['content'],'!--full-post--!'));
			$post['content'] .= '<a href="'.$this->get_config('es_main_url').'post/'.$post['urltag'].'">full post...</a>';
		}
		else {
			$post['content'] = str_replace('!--full-post--!','',$post['content']);
		} 
		$post['comments'] = $this->get_post_comment_count($post_id);
		$inside_post_div = 'class="post" id="post-'.$post['id'].'" ondblclick="xajax_updatePost('.$post['id'].',\'quick\')"';
		ob_start();
		if (file_exists('theme/'.$this->get_config('es_theme').'/page_template.php') && $type == 'page') {
			include('theme/'.$this->get_config('es_theme').'/page_template.php');
		}
		else {
			include('theme/'.$this->get_config('es_theme').'/post_template.php');
		}
		$single_post = ob_get_contents();
		ob_end_clean();
		return $single_post;
	}
	public function get_post_comment_count($post_id) {
		$query = $this->db->_query("select count(*) from ".$this->db->get_table_prefix()."comments where linked_post_id='".$post_id."' and deleted=0");
		$result = $query->_result(0);
		if ($result == 0) {
			return 'No Comments';
		}
		else {
			return $result.' Comments';
		}
	}
	public function get_posts($start = 0,$category = 'none') {
		if ($category != 'none') {
			$extra_where = " and id in (select post_id from ".$this->db->get_table_prefix()."tags t inner join ".$this->db->get_table_prefix()."post_tags pt on (t.tag_id=pt.tag_id and pt.type='cat') where tag='".$category."' and t.type='cat')";
			//$extra_where = " and simple_category='".$category."'";
		}
		else { $extra_where=''; }
		$query = $this->new_post_select($extra_where,$start);
		$loggedin = $this->loggedIn();
		while ($post = $query->_fetch_assoc()) {
			if (($post['live'] == 1) || ($this->loggedIn())) {
				$post['title'] = stripslashes($post['title']);
				if ($post['live'] == '0')
					$post['title'] .= ' (Not Live Until '.date('M jS, Y g:ia',strtotime($post['date_entered'])).')';
				$post['content'] = stripslashes($post['content']);
				if (strpos($post['content'],'!--full-post--!')) {
					$post['content'] = substr($post['content'],0,strpos($post['content'],'!--full-post--!'));
					$post['content'] .= '<a href="'.$this->get_config('es_main_url').'post/'.$post['urltag'].'">full post...</a>';
				}
				$post['urltag'] = str_replace(array(' ',"'",'"','/','.',',','&'),'-',$post['urltag']);
				$inside_post_div = 'class="post" id="post-'.$post['id'].'" ondblclick="xajax_updatePost('.$post['id'].',\'quick\')"';
				$post['comments'] = $this->get_post_comment_count($post['id']);
				ob_start();
				include('theme/'.$this->get_config('es_theme').'/post_template.php');
				$post_html = ob_get_contents();
				ob_end_clean();
				echo $post_html;
			}
		}
		$query = $this->db->_query("select count(id) from ".$this->db->get_table_prefix()."post where type='post' and deleted=0 and date_entered <= NOW()");
		$page_count = ceil($query->_result(0)/$this->get_config('es_posts_per_page'));
		$page_list = '<p id="page-list">';
		$page_number = ($start/$this->get_config('es_posts_per_page'))+1;
		if ($page_number  > 1) {
			$page_list .= '<a href="'.$this->get_config('es_main_url').'page/'.($page_number-1).'">&lt;newer</a>&nbsp;';
		}
		for($i='1';$i <= $page_count;$i++) {
			if ($i != $page_number) {
				if ($i > 1) {
					$page_list .= '<a href="'.$this->get_config('es_main_url').'page/'.$i.'">'.$i.'</a>&nbsp;';
				}
				else {
					$page_list .= '<a href="'.$this->get_config('es_main_url');
					if ($this->get_config('es_homepage') != '!posts!') {
						$page_list .= $this->get_config('es_postpage');
					}
					if ($page_count > 1) { $page_list .= '">1</a> '; }
				}
			}
			else {
					if ($page_count > 1) { $page_list .= $i.'&nbsp;'; }
			}
		}
		if ($page_number < $page_count) {
			$page_list .= '<a href="'.$this->get_config('es_main_url').'page/'.($page_number+1).'">older&gt;</a>';
		}
		$page_list .= '</p>';
		echo $page_list;
	}
	public function get_post_titles($limit,$links = false) {
		$query = $this->db->_limit_query($this->db->get_table_prefix().'post','id','id,title,urltag','0',$limit,"deleted=0 and type='post' and date_entered <= NOW()",'date_entered desc');
		$post_title_array = array();
		while ($post = $query->_fetch_assoc()) {
			$post['title'] = stripslashes($post['title']);
			$post_line = ($links) ? '<a href="'.$this->get_config('es_main_url').'post/'.$post['urltag'].'">'.$post['title'].'</a>' : $post['title'];
			array_push($post_title_array,$post_line);
		}
		return $post_title_array;
	}
	public function get_links($prepend,$class,$append) {
		$links = '';
		$query = $this->db->_query("select link_id,url,title,nofollow,target,link_order
									 from ".$this->db->get_table_prefix()."links where deleted=0 order by link_order asc");
		$first_link = true;
		$max_link = $this->db->_query("select max(link_order) from ".$this->db->get_table_prefix()."links");
		if ($max_link->_num_rows() == 1) {
			$max_link = $max_link->_result(0);
		}
		while ($link = $query->_fetch_assoc()) {
			$link['title'] = stripslashes($link['title']);
			$nofollow = ($link['nofollow'] == '1') ? ' rel="nofollow" ' : '';
			$links .= $prepend.'<a href="'.$link['url'].'" id="link-'.$link['link_id'].'" class="'.$class.'"'.$nofollow.' target="'.$link['target'].'">'.$link['title'].'</a>';
			$links .= '&nbsp;<img src="'.$this->get_config('es_main_url').'includes/layout/images/delete.png" class="delete-link" onclick="xajax_deleteLink('.$link['link_id'].')" title="Delete Link" alt="Delete Link"/>';
			if (!$first_link) {
				$links .= '&nbsp;<img src="'.$this->get_config('es_main_url').'includes/layout/images/arrow_up.png" class="up-arrow-link" onclick="xajax_moveLink('.$link['link_id'].',\'up\')" title="Move Link Up" alt="Move Link Up"/>';
			}
			if ($link['link_order'] != $max_link) {
				$links .= '&nbsp;<img src="'.$this->get_config('es_main_url').'includes/layout/images/arrow_down.png" class="down-arrow-link" onclick="xajax_moveLink('.$link['link_id'].',\'down\')" title="Move Link Down" alt="Move Link Down"/>';
			}
			$links .= $append."\n";
			$first_link = false;
		}
		return $links;
	}
	public function get_categories($prepend,$value,$append,$selected) {
		$categories = '';
		$query = $this->db->_query("select tag from ".$this->db->get_table_prefix()."tags where type='cat'");
		while ($category = $query->_fetch_assoc()) {
			$categories .= '<'.$prepend;
			if ($value == true)
				$categories .= ' value="'.$category['tag'].'"';
			if ($category['tag'] == $selected)
				$categories .= ' selected="selected"';
			$categories .= '>';
			$categories .= $category['tag'].$append."\n";
		}
		return $categories;
	}
	public function is_page($post_id) {
		$query = $this->db->_query("select type from ".$this->db->get_table_prefix()."post where id='".$post_id."'");
		if ($query->_num_rows() == 1) {
			$type = $query->_result(0);
		}
		if ($type == 'page')
			return true;
		else
			return false;
	}
	public function get_pages($prepend,$class,$append) {
		$pages = '';
		if ($this->get_config('es_homepage') != '!posts!') {
			$pages .= $prepend.'<a href="'.$this->get_config('es_main_url').$this->get_config('es_postpage').'"';
			$pages .= ' class ="'.$class.'"';
			$pages .= '>'.ucwords($this->get_config('es_postpage')).'</a>'.$append."\n";
		}
		$query = $this->db->_query($this->post_select("and post.type='page' order by page_order asc"));
		while ($page = $query->_fetch_assoc()) {
			if (($page['in_nav'] == '1') || ($this->loggedIn() && $page['in_nav'] == '0')) {
				$pages .= $prepend.'<a href="'.$this->get_config('es_main_url').$page['urltag'].'"';
				$pages .= ' class ="'.$class.'"';
				$pages .= '>'.$page['title'];
				if ($page['in_nav'] == '0') {
					$pages .= ' (Hidden)';
				}
				$pages .= '</a>'.$append."\n";
			}
		}
		return $pages;
	}
	public function get_comments($post_id) {
		$comments = '';
		$query = $this->db->_query("select `comment_id`,`name`,`comment`,`date_entered` from `".$this->db->get_table_prefix()."comments` 
									where linked_post_id='".$post_id."' and deleted=0");
		if ($query->_num_rows() >= 1) {
			while ($comment = $query->_fetch_assoc()) {
				$comments .= '<div class="comment-block"><div class="post-edit">
				<img src="'.$this->get_config('es_main_url').'includes/layout/images/post_delete.png" onclick="xajax_deleteComment('.$comment['comment_id'].'.)" title="Delete Comment" alt="Delete Comment" />
				</div><strong class="comment-name">'.$comment['name'].'</strong> @ '.date('M jS, Y g:ia',strtotime($comment['date_entered'])).'
				<br/><p>'.$comment['comment'].'</p>
				</div>';
			}
		}
		return $comments;
	}
	public function get_ad_code($ad_number) {
		$query = $this->db->_query("select data from ".$this->db->get_table_prefix()."custom where section='ad-code-".$ad_number."'");
		if ($query->_num_rows() == 1) {
			return $query->_result(0);
		}
		else {
			return '';
		}
	}
	public function get_user_email($user_id) {
		$query = $this->db->_query("select email from ".$this->db->get_table_prefix()."user where user_id='".$user_id."'");
		if ($query->_num_rows() == '1') {
			return $query->_result(0);
		}
	}
	public function get_post_user($post_id) {
		$query = $this->db->_query("select user_id from ".$this->db->get_table_prefix()."post where id='".$post_id."'");
		if ($query->_num_rows() == '1') {
			return $query->_result(0);
		}
	}
	public function get_post_title($post_id) {
		$query = $this->db->_query("select title from ".$this->db->get_table_prefix()."post where id='".$post_id."'");
		if ($query->_num_rows() == '1') {
			return $query->_result(0);
		}
	}
	public function change_password($existing,$new_password) {
		$query = $this->db->_query("update ".$this->db->get_table_prefix()."user 
										set password=md5('".$this->db->_escape_string($new_password)."') 
										where user_id='".$_SESSION['user_id']."'
										and password=md5('".$this->db->_escape_string($existing)."')");
		if ($query->_affected_rows() == 1)
			return true;
		else 
			return false;
	}
	public function random_password() {
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;
		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}
	public function undeleteList($type) {
		if ($type == 'comments') {
			$title = 'name';
		}
		else {
			$title = 'title';
		}
		$return_msg = 'No deleted '.$type.' found';
		if ($type == 'post' || $type == 'page') {
			$extra_where = " and type='".$type."'";
			$type = 'post';
		}

		$result = $this->db->_query("select * from ".$this->db->get_table_prefix().$type." where deleted=1".$extra_where);
		$return = '';
		while($row = $result->_fetch_assoc()) {
			$return .= '<tr><td>'.$row[$title].'</td><td><input type="submit" value="restore" onclick="xajax_unDelete(\''.$row['id'].'\',\''.$type.'\'); return false;" /></td></tr>'; 
		}
		if ($return == '') {
			$return = $return_msg;
		}
		else {
			$return = '<table>'.$return.'</table>';
		}
		return $return;
	}
}
?>
