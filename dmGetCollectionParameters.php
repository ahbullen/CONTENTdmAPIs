<?php

$xmlData = file_get_contents('https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCollectionParameters/linl/xml');

// Create the document object

$xml = simplexml_load_string($xmlData);

// simplexml_load_string interprets a string of XML ($xml) into an object

$result = array();

// Initializes an array


foreach ($xml->xpath('//parameters') as $record) {

	// Get the nodes and loop them

	$result[] = array(
		'path' => (string) $record->path,
		'rc' => (string) $record->rc,
		'name' => (string) $record->name
	);
	
	// This loads the data in $xml as key:value pairs into the array $result
}

$resultCount = count($result) - 1;

// How many records are in $result? And, since the first element is 0, subtract 1

for ($i=0;$i<=$resultCount;$i++) {

	// Loop through the array $result and display the key:value pairs for each element $i, and then print them

        $path = $result[$i]["path"];
        $name = $result[$i]["name"];
	$rc = $result[$i]["rc"];

        echo "Record Count: $rc Name: $name ($path)\n";
}

?>
