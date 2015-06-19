<?php
//test.php
require_once('./rsstojson.php');

$categories=array('top'=>'主要','dom'=>'国内','int'=>'海外','eco'=>'IT経済','ent'=>'芸能','spo'=>'スポーツ','52'=>'映画','gourmet'=>'グルメ','love'=>'女子','trend'=>'トレンド');

$rss = new RSS_Parse();
$rss->set('base_url','http://news.livedoor.com/topics/rss/');
$rss->set('base_ext','.xml');
$rss->set('categories',$categories);

if(!isset($_GET['category'])){
$category='top';
}else{
$category=htmlspecialchars($_GET['category']);
}
$act=$_GET['act'];
if(!$act){
	if(is_numeric($category)===false){
		$result=$rss->getCategoryArticles($category);
	}else{
		$result=$rss->getSimilarArticle($category);
	}
}elseif($act='getsimilar' && $_GET['article_num']){
	$result=$rss->getSimilarArticle(htmlspecialchars($_GET['article_num']));
}

$json=array();
$json['data']=$result;
echo json_encode($json);
?>