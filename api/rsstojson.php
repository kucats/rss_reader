<?php
require_once "XML/RSS.php";

$rss =& new XML_RSS("http://rss.slashdot.org/Slashdot/slashdot");
$rss->parse();

echo "<h1>Headlines from <a href=\"http://slashdot.org\">Slashdot</a></h1>\n";
echo "<ul>\n";

foreach ($rss->getItems() as $item) {
    echo "<li><a href=\"" . $item['link'] . "\">" . $item['title'] . "</a></li>\n";
}

echo "</ul>\n";
?>