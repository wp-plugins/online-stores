<?php
/*
Plugin Name: Online Stores (by BTE)
Plugin URI: http://www.blogtrafficexchange.com/online-stores
Description: Add your stores online to your content from the BTE online store newtork.  Membership is a privledge, dont abuse it. <a href="options-general.php?page=BTE_OS_admin.php">Configuration options are here.</a>
Version: 1.2.2
Author: Blog Traffic Exchange
Author URI: http://www.blogtrafficexchange.com/
License: GPL
*/
/*  Copyright 2008-2009  Blog Traffic Exchange (email : kevin@blogtrafficexchange.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require_once('BTE_OS_admin.php');
require_once('BTE_OS_core.php');
require_once('BTE_OS_ge.php');

if (!class_exists('xmlrpcmsg')) {
	require_once('lib/xmlrpc.inc');
}		

// Play nice to PHP 5 installations with REGISTER_LONG_ARRAYS off
if(!isset($HTTP_POST_VARS) && isset($_POST)) {
	$HTTP_POST_VARS = $_POST;
}

define ('BTE_OS_DEBUG', false); 
define ('BTE_OS_KEYWORDS', 'freq'); 
define ('BTE_OS_1_HOUR', 60*60); 
define ('BTE_OS_4_HOURS', 4*BTE_OS_1_HOUR); 
define ('BTE_OS_6_HOURS', 6*BTE_OS_1_HOUR); 
define ('BTE_OS_12_HOURS', 12*BTE_OS_1_HOUR); 
define ('BTE_OS_24_HOURS', 24*BTE_OS_1_HOUR); 
define ('BTE_OS_48_HOURS', 48*BTE_OS_1_HOUR); 
define ('BTE_OS_72_HOURS', 72*BTE_OS_1_HOUR); 
define ('BTE_OS_168_HOURS', 168*BTE_OS_1_HOUR); 
define ('BTE_OS_21_DAYS', 21*BTE_OS_24_HOURS); 
define ('BTE_OS_DB_SCHEMA', '1.0'); 
/*
define ('BTE_OS_XMLRPC_URI', 'localweb'); 
define ('BTE_OS_XMLRPC', 'BTE/bte.php'); 
*/
define ('BTE_OS_XMLRPC_URI', 'bteservice.com'); 
define ('BTE_OS_XMLRPC', 'bte.php'); 

define ('BTE_OS_LINK_INTERVAL', 1); 
define ('BTE_OS_LINKS', 5); 
define ('BTE_OS_TITLE','<strong>Online Stores</strong>');
define ('BTE_OS_LINKTITLE',false);
define ('BTE_OS_LINKS_HEADER', '<ul>'); 
define ('BTE_OS_LINKS_FOOTER', '</ul>'); 
define ('BTE_OS_LINK_HEADER', '<li>'); 
define ('BTE_OS_LINK_FOOTER', '</li>'); 
define ('BTE_OS_ADD', '1'); 

register_activation_hook(__FILE__, 'bte_os_activate');
register_deactivation_hook(__FILE__, 'bte_os_deactivate');
add_filter('the_content', 'bte_os_the_content', 1100);
add_filter('the_excerpt', 'bte_os_the_excerpt', 1100);
add_action('init','bte_os_wake');
add_action('admin_menu', 'bte_os_options_setup');
add_action('admin_head', 'bte_os_head_admin');
add_action('wp_head', 'bte_os_js_header' );

function bte_os_links($num=0) {
	echo bte_os_get_links($num);
}

function bte_os_get_links_tag($num=0) {
	echo bte_os_get_links($num);
}

function bte_os_deactivate() {
	global $wpdb;
   	$table_name = $wpdb->prefix . "bte_os_sites";
   	$clicks_table_name = $wpdb->prefix . "bte_os_clicks";
   	$sql = "DROP TABLE $table_name;";
	$res = $wpdb->query($sql);
   	$sql = "DROP TABLE $clicks_table_name;";
	$res = $wpdb->query($sql);
   	$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key='_bte_os_last_content_update';";
	$res = $wpdb->query($sql);
   	$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key='_bte_os_last_link_update';";
	$res = $wpdb->query($sql);
   	$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key='_bte_content';";
	$res = $wpdb->query($sql);
   	$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key='_bte_last_content_update';";
	$res = $wpdb->query($sql);
}

function bte_os_activate() {
	global $wpdb;
	add_option("bte_os_db_version", BTE_OS_DB_SCHEMA);
	add_option('bte_os_link_interval',BTE_OS_LINK_INTERVAL);
	add_option('bte_key','');
	if (WPLANG=='')
	{
		add_option('bte_lang','en');	
	} else {
		add_option('bte_lang',WPLANG);			
	}
   	add_option('bte_os_links',BTE_OS_LINKS);
   	add_option('bte_os_title',BTE_OS_TITLE);
   	add_option('bte_os_linktitle',true);
   	add_option('bte_os_links_header',BTE_OS_LINKS_HEADER);
   	add_option('bte_os_links_footer',BTE_OS_LINKS_FOOTER);
   	add_option('bte_os_link_header',BTE_OS_LINK_HEADER);
   	add_option('bte_os_link_footer',BTE_OS_LINK_FOOTER);
   	add_option('bte_os_add',BTE_OS_ADD);	
   	$table_name = $wpdb->prefix . "bte_os_sites";
	$result = mysql_list_tables(DB_NAME);
	$tables = array();
	while ($row = mysql_fetch_row($result)) {
		$tables[] = $row[0];
	}
	if (!in_array($table_name, $tables)) {
	   	$sql = "CREATE TABLE $table_name (
				ID bigint(20) NOT NULL AUTO_INCREMENT,
				post_id bigint(20) NOT NULL,
				link text NOT NULL,
				UNIQUE KEY id (id)
				);";
		$res = $wpdb->query($sql);
		$sql = "CREATE INDEX bte_os_posts_post_id_idx ON $table_name(post_id);";
		$res = $wpdb->query($sql);
	}
	$clicks_table_name = $wpdb->prefix . "bte_os_clicks";
	if (!in_array($clicks_table_name, $tables)) {
	   	$sql = "CREATE TABLE $clicks_table_name (
				ID bigint(20) NOT NULL AUTO_INCREMENT,
				guid text NOT NULL,
				click text NOT NULL,
				UNIQUE KEY id (id)
				);";
		$res = $wpdb->query($sql);
	}	
}

?>