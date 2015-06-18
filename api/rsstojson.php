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
		foreach ($categories as $category_en => $category_ja){
			$this->getRSS($category_en);
		}
	}
	public function getRSS($category){
		$url =$this->makeURI($category);
		$rss =& new XML_RSS($url);
		$rss->parse();
		foreach ($rss->getItems() as $item) {
			$item['category']=$category;
			$this->registerItem($item);
		}
	}
	private function retrieveStrings($description){
		preg_match_all('/<li[^>]*>([^<]*)<\/li>/iu', $description, $summary);
		if(count($summary[0])>=2){
			foreach($summary[0] as $key){
				$result[] = strip_tags($key);
			}
			return $result;
		}else{
			return NULL;
		}
	}
	private function registerItem($array){
		if(isset($array['title']) && isset($array['link'])){
			$array['summary']=$this->retrieveStrings($array['description']);
			$array['unixtime']=strtotime($array['pubdate'])
			$this->db[]=$array;
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