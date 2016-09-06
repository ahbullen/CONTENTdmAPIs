<?php

$imageXMLData = file_get_contents('https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetImageInfo/pshs/140/xml');
$imageXML = simplexml_load_string($imageXMLData);


$height = $imageXML[0]->{'height'};
$width = $imageXML[0]->{'width'};

echo "H: $height W: $width\n";

$ratio_orig = 800/600;

if ($width/$height > $ratio_orig) {
   $width = $height*$ratio_orig;
} else {
   $height = $width/$ratio_orig;
}

echo "H: $height W: $width\n";


?>
