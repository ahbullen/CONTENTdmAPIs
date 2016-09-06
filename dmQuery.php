<?php

$xmlData = file_get_contents('https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmQuery/linl/title^Amelia^all^and/title!subjec!descri/title/100/1/0/0/0/0/xml');

// Create the document object

$xml = simplexml_load_string($xmlData);

$pager = array();

// How many hits did the search yield?

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

// The first record, $pager[0], will have the total number of results and the starting record

$numberOfHits = $pager[0]["total"];
$startingAt = $pager[0]["start"];

echo "Number of hits: $numberOfHits starting at record number $startingAt\n\n";

// I can get away with just checking the first record for the collection alias, since I am only searching 1 collection. If I was searching across all the collections, I would have to check each alias

$collectionName = $result[0]["collection"];

echo "Collection alias: $collectionName\n\n";

$resultCount = count($result) - 1;

for ($i=0;$i<=$resultCount;$i++) {
        $title = $result[$i]["title"];
        $subject = $result[$i]["subject"];
        $description = $result[$i]["descri"];
        $thumb = $result[$i]["thumb"];
        echo "$title: $subject: $description: $thumb\n\n";
}

?>
