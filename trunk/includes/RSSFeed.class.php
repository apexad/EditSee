<?php
class RSSFeed {
// VARIABLES
    // channel vars
    var $channel_url;
    var $channel_title;
    var $channel_description;
    var $channel_lang;
    var $channel_copyright;
    var $channel_date;
    var $channel_creator;
    var $channel_subject;   
    // image
    var $image_url;
    // items
    var $items = array();
    var $nritems;
    
// FUNCTIONS
    // constructor
    function RSSFeed() {
            $this->nritems=0;
        $this->channel_url='';
        $this->channel_title='';
        $this->channel_description='';
        $this->channel_lang='';
        $this->channel_copyright='';
        $this->channel_date='';
        $this->channel_creator='';
        $this->channel_subject='';
        $this->image_url='';
    }   
    // set channel vars
    function SetChannel($url, $title, $description, $lang, $copyright, $creator, $subject) {
        $this->channel_url=$url;
        $this->channel_title=$title;
        $this->channel_description=$description;
        $this->channel_lang=$lang;
        $this->channel_copyright=$copyright;
        $this->channel_date=date(DATE_RSS);
        $this->channel_creator=$creator;
        $this->channel_subject=$subject;
    }
    // set image
    function SetImage($url) {
        $this->image_url=$url;  
    }
    // set item
    function SetItem($guid,$url, $title, $pubdate, $description) {
    	$this->items[$this->nritems]['guid']=$guid;
        $this->items[$this->nritems]['url']=$url;
        $this->items[$this->nritems]['title']=$title;
		$this->items[$this->nritems]['pubDate']=$pubdate;
        $this->items[$this->nritems]['description']=$description;
        $this->nritems++;   
    }
    // output feed
    function Output() {
        $output =  '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$output .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'."\n\n";
        $output .= '<channel>'."\n";
		$feed_uri = 'http://'.str_replace('www.','',$_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI'];
        $output .= '<title>'.$this->channel_title.'</title>'."\n";
		$output .= "\t".'<atom:link href="'.$feed_uri.'" rel="self" type="application/rss+xml" />';
        $output .= "\t".'<link>'.$this->channel_url.'</link>'."\n";
        $output .= "\t".'<description>'.$this->channel_description.'</description>'."\n";
        $output .= "\t".'<language>'.$this->channel_lang.'</language>'."\n";
		$output .= "\t".'<pubDate>'.$this->channel_date.'</pubDate>'."\n";
		$output .= "\t".'<lastBuildDate>'.$this->channel_date.'</lastBuildDate>'."\n";
		$output .= "\t".'<generator>N/A</generator>'."\n";
//		$output .= "\t".'<generator>editsee '.editsee_App::version.'</generator>'."\n";
        for($k=0; $k<$this->nritems; $k++) {
            $output .= "\t\t".'<item>'."\n";
            $output .= "\t\t\t".'<title>'.$this->items[$k]['title'].'</title>'."\n";
            $output .= "\t\t\t".'<link>'.$this->items[$k]['url'].'</link>'."\n";
			$output .= "\t\t\t".'<description>'.$this->items[$k]['description'].'</description>'."\n";
            $output .= "\t\t\t".'<pubDate>'.date(DATE_RSS,strtotime($this->items[$k]['pubDate'])).'</pubDate>'."\n";
			$output .= "\t\t\t".'<guid>'.$this->items[$k]['guid'].'</guid>'."\n";
            $output .= "\t\t".'</item>'."\n";  
        }
		$output .= '</channel>'."\n";
        $output .= '</rss>'."\n";
        return $output;
    }
};
?>
