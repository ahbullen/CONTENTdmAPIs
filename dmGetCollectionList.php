<?php

$xmlData = file_get_contents('https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCollectionList/xml');

// Create the document object

$xml = simplexml_load_string($xmlData);

// simplexml_load_string interprets a string of XML ($xml) into an object

$result = array();

// Initializes an array


foreach ($xml->xpath('//collection') as $record) {

	// Get the nodes and loop them

	$result[] = array(
		'alias' => (string) $record->alias,
		'name' => (string) $record->name
	);
	
	// This loads the data in $xml as key:value pairs into the array $result
}

$resultCount = count($result) - 1;

// How many records are in $result? And, since the first element is 0, subtract 1

for ($i=0;$i<=$resultCount;$i++) {

	// Loop through the array $result and display the key:value pairs for each element $i, and then print them

        $alias = $result[$i]["alias"];
        $name = $result[$i]["name"];
        echo "$name: $alias\n";
}

?>
