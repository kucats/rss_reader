<?php
//!!deprecated module
require_once 'XML/RSS.php';

require_once './dbconn.php';
require_once './mecab.php';

Class RSS_Parse{
	//mongoを作るまでの仮の変数
	public $db=array();
	public $params;

	private $dbh;

	private function prepareDB(){
		if(!isset($this->dbh)){
			$db = new DB_MYSQL();
			$dbh = $db->getDBConn();
			return $dbh;
		}else{
			return $this->dbh;
		}
	}
	public function returnDB(){
		return $this->db;
	}
	public function textAnalyze($category){
		$ma = new Mecab_Analyze();

		$data=$this->getCategoryArticles($category);
		$data_compare=$data;
		foreach ($data as $news_source){
			$AnalyzeText_Source=$news_source['Title'].$news_source['Strings1'].$news_source['Strings2'].$news_source['Strings3'];
			$num_source=$news_source['ArticleID'];
			
			foreach ($data_compare as $news_dest){
				$AnalyzeText_Dest=$news_dest['Title'].$news_dest['Strings1'].$news_dest['Strings2'].$news_dest['Strings3'];
				$num_dest=$news_dest['ArticleID'];
				
				$similar[$num_source][$num_dest]=$ma->similar_mecab($AnalyzeText_Source,$AnalyzeText_Dest);
			}
		}
		return $similar;
	}
	public function getSimilarArticle($ArticleID){
			try{
				$dbh = $this->prepareDB();
				
				$stmt = $dbh -> prepare("SELECT * from similar WHERE ArticleID = :ArticleID");
				$stmt->bindParam(':ArticleID', $ArticleID, PDO::PARAM_INT);

				$ret=$stmt->execute();
				if(!$ret){
					echo 'SQL Error';
				}
				$result = $stmt-> fetchAll();
				$return = array();
				foreach ($result as $keys){
					$TargetArticleID=$keys['TargetArticleID'];
					$Similarity=$keys['Similarity'];
					$return[$TargetArticleID]=$Similarity;
				}
				asort($return);
				return $return;
			}catch  (PDOException $e) {
			    print "Exception:SQL";
				//print $e->getMessage();
			}

	}
	public function addAnalyze($category){
		$result = $this->textAnalyze($category);
		$dbh = $this->prepareDB();

		foreach ($result as $source_article_num => $compare_array){
			foreach ($compare_array as $dest_article_num => $similarity){
				if($source_article_num == $dest_article_num){
					break;
				}
				$stmt = $dbh -> prepare("INSERT INTO similar (ArticleID, TargetArticleID, Similarity, LastUpdated) VALUES (:ArticleID, :TargetArticleID, $similarity, now())");
				$stmt->bindValue(':ArticleID', $source_article_num, PDO::PARAM_INT);
				$stmt->bindParam(':TargetArticleID', $dest_article_num, PDO::PARAM_INT);

				$ret=$stmt->execute();
				if(!$ret){
					echo 'SQL Error';
				}

			}
		}
	}


	public function getCategoryArticles($category){
			if(!isset($category)){return false;}
			try{
				$dbh = $this->prepareDB();
				
				$stmt = $dbh -> prepare("SELECT * from rssfeed WHERE Category = :Category");
				$stmt->bindParam(':Category', $category, PDO::PARAM_STR);

				$ret=$stmt->execute();
				if(!$ret){
					echo 'SQL Error';
				}
				$result = $stmt-> fetchAll();
				/*
				$d = new DateTime($result['Time']);
				$result['UnixTime']=$d->format("U");
				*/
					
				return $result;
			}catch  (PDOException $e) {
			    print "Exception:SQL";
				//print $e->getMessage();
			}
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
		if(!isset($array['title']) || !isset($array['link'])){
			return false;
		}
			$array['summary']=$this->retrieveStrings($array['description']);
			$array['unixtime']=strtotime($array['pubdate']);
			$array['articleid']=substr($array['link'],-9,8);
			$this->db[]=$array;
			
			try{
			
				$dbh = $this->prepareDB();
				
				$stmt = $dbh -> prepare("INSERT INTO rssfeed (ArticleID, Title, Category, Strings1, Strings2, Strings3, Url, Time, LastUpdated) VALUES (:ArticleID, :Title, :Category, :Strings1, :Strings2, :Strings3, :Url, FROM_UNIXTIME(".$array['unixtime']."), now())");
				$stmt->bindValue(':ArticleID', $array['articleid'], PDO::PARAM_INT);
				$stmt->bindParam(':Title', $array['title'], PDO::PARAM_STR);
				$stmt->bindParam(':Category', $array['category'], PDO::PARAM_STR);
				$stmt->bindParam(':Strings1', $array['summary'][1], PDO::PARAM_STR);
				$stmt->bindParam(':Strings2', $array['summary'][2], PDO::PARAM_STR);
				$stmt->bindParam(':Strings3', $array['summary'][3], PDO::PARAM_STR);
				$stmt->bindParam(':Url', $array['link'], PDO::PARAM_STR);

				$ret=$stmt->execute();
				if(!$ret){
					echo 'SQL Error';
				}
			}catch  (PDOException $e) {
			    print "Exception:SQL";
				//print $e->getMessage();
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