<?php

$xmlData = file_get_contents('https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetItemInfo/linl/85/xml');
$fieldData = file_get_contents('https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCollectionFieldInfo/linl/xml');

$sxe = new SimpleXMLElement($xmlData);

$xml = simplexml_load_string($xmlData);
$fieldData = simplexml_load_string($fieldData);

foreach ($fieldData->xpath('//field') as $record) {
	$result[] = array(
		'nick' => (string) $record->nick,
		'name' => (string) $record->name
	);
}

// Find the nickname of a term, find its array index, and then find the name value at the same index

foreach ($sxe->children() as $child) {
        $str = $child->getName();
        $title = $xml[0]->{"$str"};

	if ($str == "dmrecord") { $pointer = $title; }

	$id = searchForId($str, $result);
	if (isset($id)) {
		$fieldName = $result[$id]["name"];
        	echo "$fieldName: $str: $title\n";
	}
}


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
