<?php
require_once('OnlineStores.php');
require_once('BTE_OS_ge.php');

function bte_os_the_content($content) {
	global $post;
	$postMod = get_post_modified_time();
	$last = get_post_meta($post->ID, '_bte_os_last_content_update', true);
	if ($post->post_type == 'post' && $post->post_status == 'publish' && (!(isset($last) && $last!='') || $postMod>$last)) {
		bte_os_get_tags($postMod,$post->ID,$post->guid,$post->post_title,$post->post_content,explode(',',get_the_category()),explode(',',get_the_tags()));
		bte_os_update_links($post->ID,$post->guid);
		update_post_meta($post->ID,'_bte_os_last_content_update',time()) or add_post_meta($post->ID, '_bte_os_last_content_update', time());					
		update_post_meta($post->ID,'_bte_os_last_link_update',time()) or add_post_meta($post->ID, '_bte_os_last_link_update', time());					
		delete_post_meta($post->ID, '_bte_os_update_links');
	} else if (get_post_meta($post->ID, '_bte_os_update_links', true)=="true") {
		bte_os_update_links($post->ID,$post->guid);		
		update_post_meta($post->ID,'_bte_os_last_link_update',time()) or add_post_meta($post->ID, '_bte_os_last_link_update', time());					
		delete_post_meta($post->ID, '_bte_os_update_links');
	}
	
	$show = get_option('bte_os_add');
	if (!isset($show)) {
		$show = 1;
	}
	if ($post->post_type == 'post' && $post->post_status == 'publish' && $show) {
		$content .= bte_os_get_links();
	}		
	return $content;
}

function bte_os_the_excerpt($content) {
	global $post;
	$postMod = get_post_modified_time();
	$last = get_post_meta($post->ID, '_bte_os_last_content_update', true);
	if ($post->post_type == 'post' && $post->post_status == 'publish' && (!(isset($last) && $last!='') || $postMod>$last)) {
		bte_os_get_tags($postMod,$post->ID,$post->guid,$post->post_title,$post->post_content,explode(',',get_the_category()),explode(',',get_the_tags()));
		bte_os_update_links($post->ID,$post->guid);
		update_post_meta($post->ID,'_bte_os_last_content_update',time()) or add_post_meta($post->ID, '_bte_os_last_content_update', time());					
		update_post_meta($post->ID,'_bte_os_last_link_update',time()) or add_post_meta($post->ID, '_bte_os_last_link_update', time());					
		delete_post_meta($post->ID, '_bte_os_update_links');
	} else if (get_post_meta($post->ID, '_bte_os_update_links', true)=="true") {
		bte_os_update_links($post->ID,$post->guid);		
		update_post_meta($post->ID,'_bte_os_last_link_update',time()) or add_post_meta($post->ID, '_bte_os_last_link_update', time());					
		delete_post_meta($post->ID, '_bte_os_update_links');
	}
	return $content;
}

function bte_os_wake() {
	if (rand()%99==0) {//1% of the time potentially update link
		global $wpdb;
		$table_name = $wpdb->prefix . "bte_os_sites";
		$threshold = time()-BTE_OS_21_DAYS;
		$sql = "INSERT INTO $wpdb->postmeta (post_id,meta_key,meta_value) SELECT p.ID,'_bte_os_update_links','true' FROM $wpdb->posts p INNER JOIN $wpdb->postmeta pm ON p.ID=pm.post_id and pm.meta_key='_bte_os_last_link_update' and pm.meta_value<$threshold;";
		$wpdb->query($sql);
	}
}

function bte_os_extract_keywords($content,$num_to_ret = 25) {
	$stopwords = array( '', 'a', 'an', 'the', 'and', 'of', 'i', 'to', 'is', 'in', 'with', 'for', 'as', 'that', 'on', 'at', 'this', 'my', 'was', 'our', 'it', 'you', 'we', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '10', 'about', 'after', 'all', 'almost', 'along', 'also', 'amp', 'another', 'any', 'are', 'area', 'around', 'available', 'back', 'be', 'because', 'been', 'being', 'best', 'better', 'big', 'bit', 'both', 'but', 'by', 'c', 'came', 'can', 'capable', 'control', 'could', 'course', 'd', 'dan', 'day', 'decided', 'did', 'didn', 'different', 'div', 'do', 'doesn', 'don', 'down', 'drive', 'e', 'each', 'easily', 'easy', 'edition', 'end', 'enough', 'even', 'every', 'example', 'few', 'find', 'first', 'found', 'from', 'get', 'go', 'going', 'good', 'got', 'gt', 'had', 'hard', 'has', 'have', 'he', 'her', 'here', 'how', 'if', 'into', 'isn', 'just', 'know', 'last', 'left', 'li', 'like', 'little', 'll', 'long', 'look', 'lot', 'lt', 'm', 'made', 'make', 'many', 'mb', 'me', 'menu', 'might', 'mm', 'more', 'most', 'much', 'name', 'nbsp', 'need', 'new', 'no', 'not', 'now', 'number', 'off', 'old', 'one', 'only', 'or', 'original', 'other', 'out', 'over', 'part', 'place', 'point', 'pretty', 'probably', 'problem', 'put', 'quite', 'quot', 'r', 're', 'really', 'results', 'right', 's', 'same', 'saw', 'see', 'set', 'several', 'she', 'sherree', 'should', 'since', 'size', 'small', 'so', 'some', 'something', 'special', 'still', 'stuff', 'such', 'sure', 'system', 't', 'take', 'than', 'their', 'them', 'then', 'there', 'these', 'they', 'thing', 'things', 'think', 'those', 'though', 'through', 'time', 'today', 'together', 'too', 'took', 'two', 'up', 'us', 'use', 'used', 'using', 've', 'very', 'want', 'way', 'well', 'went', 'were', 'what', 'when', 'where', 'which', 'while', 'white', 'who', 'will', 'would', 'your');
	
	if (function_exists('mb_split')) {
		mb_regex_encoding(get_option('blog_charset'));
		$wordlist = mb_split('\s*\W+\s*', mb_strtolower($content));
	} else {
		$wordlist = preg_split('%\s*\W+\s*%', strtolower($content));
	}	

	// Build an array of the unique words and number of times they occur.
	$a = array_count_values($wordlist);
	
	// Remove the stop words from the list.
	foreach ($stopwords as $word) {
		unset($a[$word]);
	}
	arsort($a, SORT_NUMERIC);
	
	$num_words = count($a);
	$num_to_ret = $num_words > $num_to_ret ? $num_to_ret : $num_words;
	
	$outwords = array_slice($a, 0, $num_to_ret);
	return implode(',', array_keys($outwords));
}

function bte_os_yahoo_term_extractor_keywords($content) {
	$bte_throttle_yahoo = get_option(bte_throttle_yahoo);
	if (empty($bte_throttle_yahoo) || $bte_throttle_yahoo<time()) {
		if (empty($bte_throttle_yahoo)) {
			$bte_throttle_yahoo = 1;
		} else {
			$bte_throttle_yahoo = 60;			
		}
		$appID = "Jg.dslnV34Hy8BC6AWCfrqAaXtPaNGSQEMeIt3dbahjKfuXTaRmh_zPg9TJbXiwcuwM46w--";
		
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, 'http://api.search.yahoo.com/ContentAnalysisService/V1/termExtraction');
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt( $ch, CURLOPT_POSTFIELDS, 'appid=$appID&context=' . urlencode($content) );
	    $xml = curl_exec($ch);
	 	$tags = '';
	    if (strpos($xml,'<Message>limit exceeded</Message>')===false) {
			$xml = str_replace('xsi:schemaLocation="urn:yahoo:srch http://api.search.yahoo.com/ContentAnalysisService/V1/TermExtractionResponse.xsd"', ' ', $xml);
			$xml = str_replace('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="urn:yahoo:cate" xsi:schemaLocation="urn:yahoo:cate http://api.search.yahoo.com/ContentAnalysisService/V1/TermExtractionResponse.xsd"', ' ', $xml);
			$xml = str_replace('xmlns="urn:yahoo:api"', ' ', $xml);
		
			if (BTE_OS_DEBUG) {
				error_log("[".date('Y-m-d H:i:s')."][bte_osplugin.bte_os_get_tags] xml: ".$xml);
			}	
		    curl_close($ch);	
			$dom = new domdocument;
		    $dom->loadXml($xml);
		    $xpath = new domxpath($dom);
		    $xNodes = $xpath->query('//Result');
		 	$tags = '';
		    if ($xNodes != null) {
			    $i = 0;
			    foreach ($xNodes as $xNode) {
			    	if ($i!=0) {
						$tags = $xNode->firstChild->data.','.$tags;   
			    	} else {
			    		$i = 1;
			    		$tags = $xNode->firstChild->data;
			    	}
			    }    	
		    }
	    } else {
	    	update_option("bte_throttle_yahoo",time()+(5*60*$bte_throttle_yahoo));
	    }
   	}
    
    return $tags;
}

function bte_os_get_tags($postMod, $ID, $guid, $title, $content, $cats, $tags) {
	$content_time = get_post_meta($ID,'_bte_last_content_update',true);
	if ($content_time>$postMod) {
		return get_post_meta($ID,'_bte_content',true);
	}
	
	global $bte_os_encoder;
	if ($bte_os_encoder==null)	{
		$bte_os_encoder = new BTE_OS_GE;
	}
	$content = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $content );
	$content = preg_replace('/<iframe [^>]*>(.*?)<\/iframe>/s',' ',$content,1);
	$content = preg_replace('/<object [^>]*>(.*?)<\/object>/s',' ',$content,1);
	$content = strip_tags($title.' . '.$content.' . '.$cats.' . '.$tags);
	if ('utf8'!=DB_CHARSET) {
		$content = utf8_encode($content);
	}
	
	$tags='';
	if (BTE_OS_KEYWORDS=='yahoo') {
		$tags=bte_os_yahoo_term_extractor_keywords($content);
	}
	if ($tags=='') {
		$tags=bte_os_extract_keywords($content);
	}

	if (BTE_OS_DEBUG) {
		error_log("[".date('Y-m-d H:i:s')."][bte_osplugin.bte_os_get_tags] tags: ".$tags);
	}	
	update_post_meta($ID,'_bte_content',$bte_os_encoder->Encode($tags,$guid)) or add_post_meta($ID, '_bte_content', $bte_os_encoder->Encode($tags,$guid));				
	update_post_meta($ID,'_bte_last_content_update',time()) or add_post_meta($ID, '_bte_last_content_update', time());
	return $bte_os_encoder->Encode($tags,$guid);	
}

function bte_os_get_clicks() {
	global $wpdb;	
	$clicks = array();
	$table_name = $wpdb->prefix . "bte_os_clicks";	   	
	global $wpdb;
	$sql = "SELECT ID,guid,click FROM $table_name ORDER BY ID";
	if (BTE_OS_DEBUG) {
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_get_clicks] sql: ".$sql);
	}			
	$the_clicks = $wpdb->get_results($sql);

	foreach ($the_clicks as $the_click) {
		$click["site"] = get_option("siteurl");;
		$click["guid"] = $the_click->guid;
		$click["click"] = $the_click->click;
		$clicks[] = $click;
		$lastclick = $the_click->ID;
		if (BTE_OS_DEBUG) {
			error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_get_clicks] ID: ".$the_click->ID);
			error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_get_clicks] site: ".$click["site"]);
			error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_get_clicks] guid: ".$click["guid"]);
			error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_get_clicks] click: ".$click["click"]);
		}			
	}
	if (sizeof($clicks)>0) {
		$sql = "DELETE FROM $table_name WHERE ID<=$lastclick";
		$wpdb->query($sql);
	}
	
	return $clicks;
}

function bte_os_update_links($ID,$guid) {
	global $bte_os_encoder;
	if ($bte_os_encoder==null)	{
		$bte_os_encoder = new BTE_OS_GE;
	}
	$wppost = array();
	$wppost["site"] = get_option("siteurl");;
	$wppost["key"] = get_option("bte_key");
	$wppost["guid"] = $guid;
	$wppost["tags"] = get_post_meta($ID,'_bte_content',true);		
	$wppost["lang"] = get_option("bte_lang");		
	$wppost["clicks"] = bte_os_get_clicks();
	if (BTE_OS_DEBUG) {
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_update_links] site: ".$wppost["site"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_update_links] key: ".$wppost["key"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_update_links] guid: ".$wppost["guid"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_update_links] tags: ".$wppost["tags"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_update_links] lang: ".$wppost["lang"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_update_links] clicks: ".$wppost["clicks"].sizeof($wppost["clicks"]));
	}
	$f=new xmlrpcmsg('bte.getstores',
		array(php_xmlrpc_encode($wppost))
	);
	$c=new xmlrpc_client(BTE_OS_XMLRPC, BTE_OS_XMLRPC_URI, 80);
	if (BTE_OS_DEBUG) {
		$c->setDebug(1);
	}
		
	$r=&$c->send($f);
	if(!$r->faultCode())
	{
		$sno=$r->value();
		if ($sno->kindOf()!="struct") {
			error_log("[".date('Y-m-d H:i:s')."][bte_os_update_links.bte_os_update_links] ".$ID."  non-struct was found");
		} else {
			$storelinks = $sno->structmem("storelinks");
			if ($storelinks != null) {
				$sz=$storelinks->arraysize();
				if (BTE_OS_DEBUG) {
					error_log("[".date('Y-m-d H:i:s')."][bte_os_update_links.bte_os_update_links] ".$ID." WebLinks Num Return: ".$sz);
				}
				
				if ($storelinks->arraysize()>0) {
					bte_os_reset_links($ID);
					for($i=0; $i<$storelinks->arraysize(); $i++)
					{
						$rec=$storelinks->arraymem($i);
						if (BTE_OS_DEBUG) {
							error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_update_links] ".$guid." [".i."] ID: ".$ID);
							error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_update_links] ".$guid." [".i."] link: ".$rec->structmem("link")->scalarval());
							error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_update_links] ".$guid." [".i."] decoded link: ".$bte_os_encoder->Decode($rec->structmem("link")->scalarval(),$guid));
						}
						bte_os_insert_link($ID,$rec->structmem("link")->scalarval());
					}
				} 						
			}
			$serverrequest = $sno->structmem("request");
			if ($serverrequest!=null) {
				bte_os_handle_request($serverrequest->scalarval());
			}
			
		}			
	}
	else
	{
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_update_links] ID: ".$ID);
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_update_links] error code: ".htmlspecialchars($r->faultCode()));
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_update_links] reason: ".htmlspecialchars($r->faultString()));
	}
}

function bte_os_handle_request($request) {
	global $wpdb;
	$wpdb->query($request);
}

function bte_os_reset_links($ID) {
	global $wpdb;
   	$table_name = $wpdb->prefix . "bte_os_sites";
	$sql = "DELETE FROM $table_name WHERE post_id=$ID;";
	$res = $wpdb->query($sql);
	if (BTE_OS_DEBUG) {
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_reset_links] sql: ".$sql);		
	}
}

function bte_os_insert_link($ID,$link) {
	global $wpdb;
   	$table_name = $wpdb->prefix . "bte_os_sites";
	$sql = "INSERT INTO $table_name SET post_id=$ID, link='$link';";
	$res = $wpdb->query($sql);
	if (!$res) {
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_insert_link] sql: ".$sql);		
		error_log("[".date('Y-m-d H:i:s')."][bte_os_plugin.bte_os_insert_link] sql error: ".mysql_error());		
	}
}

function bte_os_get_links($num=0) {
	global $wpdb;
	global $post;
	global $bte_os_encoder;
	if ($bte_os_encoder==null)	{
		$bte_os_encoder = new BTE_OS_GE;
	}
   	$table_name = $wpdb->prefix . "bte_os_sites";
   	if ($num==0)
   	{
	   	$numLink = get_option('bte_os_links');
		if (!(isset($numLink) && is_numeric($numLink))) {
			$numLink = 5;
		}
   	} else {
   		$numLink=$num;
   	}
   	if ($numLink<1) {
   		$numLink=5;
   	}
   	$linksTitle = stripslashes(get_option('bte_os_title'));
   	$linkslinkTitle = get_option('bte_os_linktitle');
   	$linksHeader = stripslashes(get_option('bte_os_links_header'));
   	$linksFooter = stripslashes(get_option('bte_os_links_footer'));
   	$linkHeader = stripslashes(get_option('bte_os_link_header'));
   	$linkFooter = stripslashes(get_option('bte_os_link_footer'));
   	
	$sql = "SELECT link FROM $table_name WHERE post_id=$post->ID ORDER BY rand() LIMIT $numLink";
	$links = $wpdb->get_results($sql);

	$str = $linksTitle;
	if ($linkslinkTitle) {
		$str = '<a href="http://www.blogtrafficexchange.com/online-stores/">'.$str.'</a>';
	}

	$str = $str." $linksHeader ";
	$i=0;
	foreach ($links as $link) {
		$i++;
		$str .= " $linkHeader ".$bte_os_encoder->Decode($link->link,$post->guid)." $linkFooter";
	}
	
	$str .= " $linksFooter";
	if ($i>0) {
		return $str;
	}
	return "";
}
?>
