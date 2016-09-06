<?php

$xmlData = file_get_contents('https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmQuery/all/title^Titanic^all^and/title!subjec!descri/title/100/1/0/0/0/0/xml');

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
$startingAt = $pager[0]["start"];

$resultCount = count($result) - 1;

?>
<html>
        <head>
                <title>My Search Results</title>
        </head>
        <body>
                <div id="header">
			<h1 style="text-align: center;">Searched for <em>titanic</em>, with <?php echo $numberOfHits ?> results, starting at record number <?php echo $startingAt ?></h1>
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

	// I have to check each and every single alias to find out its full name

	$collection = str_ireplace("/", "", "$collection");
	$dmCP = file_get_contents("https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCollectionParameters/$collection/xml");
	$collectionXML = simplexml_load_string($dmCP);
	
	// Because this is a simple XML record, and I know how many elements it has, I can reference it directly instead of porting it over to an array

	$collectionName = $collectionXML[0]->{'name'};

	$imgStr = "http://www.idaillinois.org/utils/getthumbnail/collection/$collection/id/" . $thumb;
	echo "<li><a href=\"dmMoreSophisticatedItemInfo.php?collection=$collection&pointer=$thumb\"><img src=\"$imgStr\"></a> <strong>$title</strong><br /><em>$description</em><br />\n$subject<br />\n<em>From: $collectionName</em>\n<p /></li>\n";
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
