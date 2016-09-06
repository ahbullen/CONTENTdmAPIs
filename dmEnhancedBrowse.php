<?php

$searchTerm = $_POST["searchterm"];

$xmlData = file_get_contents("https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmQuery/all/title^$searchTerm^all^and/title!subjec!descri/title/50/1/0/0/0/0/xml");

// Create the document object

$xml = simplexml_load_string($xmlData);

$pager = array();

// How many hits did the search yield

foreach ($xml->xpath('//pager') as $hits) {
	$pager[] = array(
		'start' => (string) $hits->start,
		'total' => (string) $hits->total
	);
}

$result = array();

// Get the nodes and loop them

foreach ($xml->xpath('//record') as $record) {
	$result[] = array(
		'collection' => (string) $record->collection,
		'title' => (string) $record->title,
		'subject' => (string) $record->subjec,
		'descri' => (string) $record->descri,
		'thumb' => (string) $record->pointer
	);
}

$numberOfHits = $pager[0]["total"];
$resultCount = count($result) - 1;

?>
<html>
        <head>
                <title>My Search Results</title>
        </head>
        <body>
                <div id="header">
                        <h1 style="text-align: center;">My Library</h1>
			<h2 style="text-align: center;">Browsing our collections, with <?php echo $numberOfHits ?> results</h2>
                </div>
                <div id="list">
                        <ol>
                        <?php
for ($i=0;$i<=$resultCount;$i++) {
        $title = $result[$i]["title"];
        $subject = $result[$i]["subject"];
        $description = $result[$i]["descri"];
        $thumb = $result[$i]["thumb"];
        $collection = $result[$i]["collection"];
        $collection = str_ireplace("/", "", "$collection");
        $urlStr = "getCompoundObjectsWebPage.php?collection=$collection&pointer=$thumb";
        $imgStr = "http://www.idaillinois.org/utils/getthumbnail/collection/$collection/id/" . $thumb;
        echo "<li><a href=\"$urlStr\"><img src=\"$imgStr\"></a> <strong>$title</strong><br /><em>$description</em><br />\n$subject\n<p /></li>\n";
}
?>
                        </ol>
                </div>
                <div id="footer">
                        <p />
                        <strong>Our Library</strong>
                </div>
        </body>
</html>
