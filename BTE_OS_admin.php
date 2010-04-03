<?php
require_once('OnlineStores.php');

function bte_os_head_admin() {
		wp_enqueue_script('jquery-ui-tabs');
		$home = get_settings('siteurl');
		$base = '/'.end(explode('/', str_replace(array('\\','/BTE_OS_admin.php'),array('/',''),__FILE__)));
		$stylesheet = $home.'/wp-content/plugins' . $base . '/css/online_stores.css';
		echo('<link rel="stylesheet" href="' . $stylesheet . '" type="text/css" media="screen" />');
}

function bte_os_options_setup() {	
	add_options_page('OnlineStores', 'Online Stores', 10, basename(__FILE__), 'bte_os_options');
}

function bte_os_options() {		
	$message = null;
	$message_updated = __("Online Stores Options Updated.", 'bte_online_stores');
	if (!empty($_POST['bte_os_action'])) {
		$message = $message_updated;
		if (isset($_POST['bte_os_interval'])) {
			update_option('bte_os_interval',$_POST['bte_os_interval']);
		}
		if (isset($_POST['bte_os_links'])) {
			update_option('bte_os_links',$_POST['bte_os_links']);
		}
		if (isset($_POST['bte_os_title'])) {
			update_option('bte_os_title',$_POST['bte_os_title']);
		}
		if (isset($_POST['bte_os_linktitle'])) {
			update_option('bte_os_linktitle',$_POST['bte_os_linktitle']);
		}
		if (isset($_POST['bte_os_links_header'])) {
			update_option('bte_os_links_header',$_POST['bte_os_links_header']);
		}
		if (isset($_POST['bte_os_links_footer'])) {
			update_option('bte_os_links_footer',$_POST['bte_os_links_footer']);
		}
		if (isset($_POST['bte_os_link_header'])) {
			update_option('bte_os_link_header',$_POST['bte_os_link_header']);
		}
		if (isset($_POST['bte_os_link_footer'])) {
			update_option('bte_os_link_footer',$_POST['bte_os_link_footer']);
		}
		if (isset($_POST['bte_lang'])) {
			update_option('bte_lang',$_POST['bte_lang']);
		}
		if (isset($_POST['bte_os_add'])) {
			update_option('bte_os_add',$_POST['bte_os_add']);
		}
		
		print('
			<div id="message" class="updated fade">
				<p>'.__('Online Stores Options Updated.', 'OnlineStores').'</p>
			</div>');
	}
	$bte_os_interval = get_option('bte_os_interval');
	if (!isset($bte_os_interval)) {
		$bte_os_interval = BTE_OS_LINK_INTERVAL;
	}
	$bte_os_links = get_option('bte_os_links');
	if (!isset($bte_os_links)) {
		$bte_os_links = BTE_OS_LINKS;
	}
	$bte_os_title = get_option('bte_os_title');
	if (!isset($bte_os_title)) {
		$bte_os_title = BTE_OS_TITLE;
	}
	$bte_os_linktitle = get_option('bte_os_linktitle');
	if (!isset($bte_os_linktitle)) {
		$bte_os_linktitle = true;
	}
	$bte_os_links_header = get_option('bte_os_links_header');
	if (!isset($bte_os_links_header)) {
		$bte_os_links_header = BTE_OS_LINKS_HEADER;
	}
	$bte_os_links_footer = get_option('bte_os_links_footer');
	if (!isset($bte_os_links_footer)) {
		$bte_os_links_footer = BTE_OS_LINKS_FOOTER;
	}
	$bte_os_link_header = get_option('bte_os_link_header');
	if (!isset($bte_os_link_header)) {
		$bte_os_link_header = BTE_OS_LINK_HEADER;
	}
	$bte_os_link_footer = get_option('bte_os_link_footer');
	if (!isset($bte_os_link_footer)) {
		$bte_os_link_footer = BTE_OS_LINK_FOOTER;
	}
	$bte_os_add = get_option('bte_os_add');
	if (!isset($bte_os_add)) {
		$bte_os_add = BTE_OS_ADD;
	}
	$bte_lang = get_option('bte_lang');
	if (!isset($bte_lang)) {
		if (WPLANG=='') {
			$bte_lang = "en";		
		} else {
			$bte_lang = WPLANG;		
		}
	}
	
	print('
			<div class="wrap">
				<h2>'.__('Online Stores by', 'OnlineStores').' <a href="http://www.blogtrafficexchange.com">Blog Traffic Exchange</a></h2>
				<form id="bte_os" name="bte_onlinestores" action="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=BTE_OS_admin.php" method="post">
					<input type="hidden" name="bte_os_action" value="bte_os_update_settings" />
					<fieldset class="options">
						<div class="option">
							<label for="bte_os_links">'.__('Number of Links: ', 'OnlineStores').'</label>
							<select name="bte_os_links" id="bte_os_links">
									<option value="2" '.bte_os_optionselected(2,$bte_os_links).'>'.__('2', 'OnlineStores').'</option>
									<option value="3" '.bte_os_optionselected(3,$bte_os_links).'>'.__('3', 'OnlineStores').'</option>
									<option value="4" '.bte_os_optionselected(4,$bte_os_links).'>'.__('4', 'OnlineStores').'</option>
									<option value="5" '.bte_os_optionselected(5,$bte_os_links).'>'.__('5', 'OnlineStores').'</option>
							</select>
						</div>
						<div class="option">
							<label for="bte_os_title">'.__('Online Stores Title: ', 'OnlineStores').'</label>
							<input size="80" name="bte_os_title" type="text" value="'.htmlspecialchars(stripslashes($bte_os_title)).'" /><br/>
							<label for="bte_os_links_header">'.__('Link Block Header: ', 'OnlineStores').'</label>
							<input size="80" name="bte_os_links_header" type="text" value="'.htmlspecialchars(stripslashes($bte_os_links_header)).'" /><br/>
							<label for="bte_os_link_header">'.__('Individual Link Header: ', 'OnlineStores').'</label>
							<input size="80" name="bte_os_link_header" type="text" value="'.htmlspecialchars(stripslashes($bte_os_link_header)).'" /><br/>
							<label for="bte_os_link_footer">'.__('Individual Link Footer: ', 'OnlineStores').'</label>
							<input size="80" name="bte_os_link_footer" type="text" value="'.htmlspecialchars(stripslashes($bte_os_link_footer)).'" /><br/>
							<label for="bte_os_links_footer">'.__('Link Block Footer: ', 'OnlineStores').'</label>
							<input size="80" name="bte_os_links_footer" type="text" value="'.htmlspecialchars(stripslashes($bte_os_links_footer)).'" />
						</div>
						<div class="option">
							<label for="bte_os_add">'.__('Automaticaly add to the content (will not add to excerpt): ', 'OnlineStores').'</label>
							<select name="bte_os_add" id="bte_os_add">
									<option value="0" '.bte_os_optionselected(0,$bte_os_add).'>'.__('No', 'OnlineStores').'</option>
									<option value="1" '.bte_os_optionselected(1,$bte_os_add).'>'.__('Yes', 'OnlineStores').'</option>
							</select>
						</div>
						<div class="option">
							<label for="bte_os_linktitle">'.__('Link Title to Online Stores plugin page? ', 'OnlineStores').'</label>
							<select name="bte_os_linktitle" id="bte_os_title">
									<option value="0" '.bte_os_optionselected(0,$bte_os_linktitle).'>'.__('No', 'OnlineStores').'</option>
									<option value="1" '.bte_os_optionselected(1,$bte_os_linktitle).'>'.__('Yes', 'OnlineStores').'</option>
							</select>
						</div>
						<div class="option">
							<label for="bte_lang">'.__('Website Lang: ', 'OnlineStores').'</label>
							<select name="bte_lang" id="bte_os_add">
									<option value="en" '.bte_os_optionselected("en",$bte_lang).'>'.__('en', 'OnlineStores').'</option>
							</select>
						</div>
						<div class="option">
							<p>To manually place the formatted links within your template tags add this code with "The Loop".  This is how to use the links within an excerpt section.</p>
							<p><strong><code>&lt;?php if (function_exists(\'bte_os_links\')) { bte_os_links(); } ?&gt;</code></strong></p>
						</div>
					</fieldset>
					<p class="submit">
						<input type="submit" name="submit" value="'.__('Update Online Stores Options', 'OnlineStores').'" />
					</p>
						<div class="option">
							<h4>Other Blog Traffic Exchange <a href="http://www.blogtrafficexchange.com/wordpress-plugins/">Wordpress Plugins</a></h4>
							<ul>
							<li><a href="http://www.blogtrafficexchange.com/wordpress-backup/">Wordpress Backup</a></li>
							<li><a href="http://www.blogtrafficexchange.com/blog-copyright/">Blog Copyright</a></li>
							<li><a href="http://www.blogtrafficexchange.com/old-post-promoter/">Old Post Promoter</a></li>
							<li><a href="http://www.blogtrafficexchange.com/related-websites/">Related Websites</a></li>
							<li><a href="http://www.blogtrafficexchange.com/related-posts/">Related Posts</a></li>
							</ul>
						</div>
				</form>' );

}

function bte_os_js_header() { 
} 

function bte_os_optionselected($opValue, $value) {
	if($opValue==$value) {
		return 'selected="selected"';
	}
	return '';
}
?>
