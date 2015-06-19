<?php
        require_once('../api/rsstojson.php');

        $categories=array('top'=>'主要','dom'=>'国内','int'=>'海外','eco'=>'IT経済','ent'=>'芸能','spo'=>'スポーツ','52'=>'映画','gourmet'=>'グルメ','love'=>'女子','trend'=>'トレンド');

        $rss = new RSS_Parse();
        $rss->set('base_url','http://news.livedoor.com/topics/rss/');
        $rss->set('base_ext','.xml');
        $rss->set('categories',$categories);
        foreach ($categories as $cat_en => $cat_ja){
        	$result=$rss->addAnalyze($cat_en);
        }
?>
