<?php
//!!deprecated module
require_once "XML/RSS.php";

Class RSS_Parse{
	//mongoを作るまでの仮の変数
	public $db=array();
	public $params;

	public function returnDB(){
		return $this->db;
	}

	public function getALL(){
		$categories = $this->params['categories'];
		foreach ($categories as $category){
			$this->getRSS($category);
		}
	}
	public function getRSS($category){
		$url =$this->makeURI($category);

		$rss =& new XML_RSS($url);
		$rss->parse();
		foreach ($rss->getItems() as $item) {
		$this->registerItem(array('title'=>$item['title'],'link'=>$item['link']));
		}
	}

	private function registerItem($array){
		if(isset($array['title']) && isset($array['link'])){
			$db[]=$array;
		}else{
			return false;
		}
	}

	private function makeURI($category){
		$base_url = $this->params['base_url'];
		$base_ext = $this->params['base_ext'];
		return $base_url.$category.$base_ext;
	}

	public function set($entry,$param){
		$this->params[$entry]=$param;

	}
}

?>