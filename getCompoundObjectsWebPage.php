<?php

// Sent from the browse record when user selects an image for "full record"

$collection = $_GET["collection"];
$pointer = $_GET["pointer"];

$xmlData = file_get_contents("https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetItemInfo/$collection/$pointer/xml");

// $xmData now holds the contents of the metadata for the record pointed at by $pointer

$fieldData = file_get_contents("https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCollectionFieldInfo/$collection/xml");

// $fieldData now holds the nicknames/proper field names of the metadata

$sxe = new SimpleXMLElement($xmlData);

$xml = simplexml_load_string($xmlData);

// load the metadata into a data structure, $xml

$fieldData = simplexml_load_string($fieldData);

// load the nicknames into a data structure, $fieldData

foreach ($fieldData->xpath('//field') as $record) {

	// build a key/value structure from the raw structure

	$result[] = array(
		'nick' => (string) $record->nick,
		'name' => (string) $record->name
	);
}

// I have found the quickest way to determine if a record points to a compound object is to simply check for the file extension .cpd in the <find> element

$typeOfRecord = $xml[0]->{"find"};
$pos = strpos($typeOfRecord, ".cpd");

// This bit should be expanded; it just separates Compound objects from single images. You might want to match this per file ending for more sensitive treatment...

if ($pos === false) { $typeOfRecord = "Image"; }
else {
	$typeOfRecord = "Text"; 
	$tableStr = getCompoundObjectData($collection,$pointer); 
}

function searchForId($id, $array) {
   foreach ($array as $key => $val) {
       if ($val['nick'] === $id) {
           return $key;
       }
   }
   return null;
}

function getCompoundObjectData($collection, $pointer) {

	// gets the compound object pages, then places them in the cells of a table  
	
	$tableStr = "<table width=\"80%\" border=\"1\">\n";

        $coData = file_get_contents("https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCompoundObjectInfo/$collection/$pointer/xml");
        $coXML = simplexml_load_string($coData);
        $cpd = $coXML->{"type"};
        if ($cpd == "Monograph") {
	        foreach ($coXML->xpath('//node') as $coRecord) {
        	        foreach ($coRecord->xpath('//page') as $page) {
					$pagePtr = (string) $page->pageptr;
					$tableStr = $tableStr . "<tr>\n<td width=\"100%\">\n";
					$urlStr = "dmGetItemInfoWebPage.php?collection=$collection&pointer=$pagePtr";
					$tableStr = $tableStr . "<a href=\"$urlStr\"><img src=\"http://www.idaillinois.org/utils/getthumbnail/collection/$collection/id/$pagePtr\"></a>\n</td>\n</tr>\n";
                        }
                }
	}
        else {
		foreach ($coXML->xpath('//page') as $coRecord) {
                	$pagePtr = (string) $coRecord->pageptr;
                        $tableStr = $tableStr . "<tr>\n<td width=\"100%\">\n";
                        $urlStr = "dmGetItemInfoWebPage.php?collection=$collection&pointer=$pagePtr";
                        $tableStr = $tableStr . "<a href=\"$urlStr\"><img src=\"http://www.idaillinois.org/utils/getthumbnail/collection/$collection/id/$pagePtr\"></a>\n</td>\n</tr>\n";
		}
        }
	$pageFileStr = $pageFileStr . "</table>\n";
	return ($tableStr);
}

?>

<html>
        <head>
                <title>My Collections</title>
        </head>
        <body>
                <div id="header">
                        <h1 style="text-align: center;">My Collections</h1>
                </div>
                <div id="list">
			<center>
				<?php 
					if ($typeOfRecord != "Text") {
						echo "<img src=\"http://www.idaillinois.org/utils/ajaxhelper/?CISOROOT=$collection&CISOPTR=$pointer&action=2&DMSCALE=100&DMWIDTH=3000&DMHEIGHT=3000&DMX=0&DMY=0\">\n";
					}	
					else if ($typeOfRecord == "Text") { 
						echo $tableStr;
					}
				?>
			</center>
			<p />
                        <table border="1">
                        <?php 
				foreach ($sxe->children() as $child) {

					// getName gets the name of the XML element.

				        $str = $child->getName();
        				$title = $xml[0]->{"$str"};
					$id = searchForId($str, $result);
					if (isset($id)) {
						$fieldName = $result[$id]["name"];
						echo "<tr><td width=\"25%\"><strong>$fieldName</strong></td>\n";
						echo "<td width=\"75%\">$title</td></tr>\n";
					}
				}
			?>

 			</table>
                </div>
                <div id="footer">
                        <p />
                        <strong>Our Library</strong>
                </div>
        </body>
</html>
