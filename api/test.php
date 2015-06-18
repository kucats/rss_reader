<?php
//test.php
require_once('./rsstojson.php');

$rss = new RSS_Parse();
$rss->getALL();

?>