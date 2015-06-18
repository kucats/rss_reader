<?php
//!!deprecated module
require_once "XML/RSS.php";

Class RSS_Parse(){
	//mongoを作るまでの仮の変数
	public $db=array();

	function registerItem($array){
		if(isset($array['title']) && isset($array['link'])){
			$db[]=$array;
		}else{
			return false;
		}
	}

	function getRSS($category){
		$url =$this->makeURI($category);

		$rss =& new XML_RSS($url);
		$rss->parse();
		foreach ($rss->getItems() as $item) {
		$this->registerItem('title'=>$item['title'],'link'=>$item['link']);
		}
	}
	function makeURI($category){
		$base_url = Constants::base_url;
		$base_ext = Constants::base_ext;
		return $base_url.$category.$base_ext;
	}
}
Class Constatnts{
	//起点となるURL
	public $base_url='http://news.livedoor.com/topics/rss/';
	//拡張子
	public $base_ext='.xml';
	//カテゴリ
	public $rss_category=array('top'=>'主要','dom'=>'国内','int'=>'海外','eco'=>'IT経済','ent'=>'芸能','spo'=>'スポーツ','52'=>'映画','gourmet'=>'グルメ','love'=>'女子','trend'=>'トレンド');
}
?>