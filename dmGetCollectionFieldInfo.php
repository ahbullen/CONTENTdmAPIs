<?php

$xmlData = file_get_contents('https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCollectionFieldInfo/linl/xml');

// Create the document object

$xml = simplexml_load_string($xmlData);

$result = array();

// Get the nodes and loop them

foreach ($xml->xpath('//field') as $record) {
	$result[] = array(
		'nick' => (string) $record->nick,
		'name' => (string) $record->name
	);
}

// Find the nickname of a term, find its array index, and then find the name value at the same index

$id = searchForId('dmoclcno', $result);
$name = $result[$id]["name"];

echo "Name: $name\n";

// This function from Jacob Trunecek (http://stackoverflow.com/users/819364/jakub-trune%C4%8Dek)

function searchForId($id, $array) {
   foreach ($array as $key => $val) {
       if ($val['nick'] === $id) {
           return $key;
       }
   }
   return null;
}
?>
