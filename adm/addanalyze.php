<?php
        require_once('rsstojson.php');

        $categories=array('top'=>'��v','dom'=>'����','int'=>'�C�O','eco'=>'IT�o��','ent'=>'�|�\','spo'=>'�X�|�[�c','52'=>'�f��','gourmet'=>'�O����','love'=>'���q','trend'=>'�g�����h');

        $rss = new RSS_Parse();
        $rss->set('base_url','http://news.livedoor.com/topics/rss/');
        $rss->set('base_ext','.xml');
        $rss->set('categories',$categories);
        $result=$rss->addAnalyze('top');
?>
