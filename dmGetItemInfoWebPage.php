<?php

$collection = $_GET["collection"];
$pointer = $_GET["pointer"];

$xmlData = file_get_contents("https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetItemInfo/$collection/$pointer/xml");
$fieldData = file_get_contents("https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCollectionFieldInfo/$collection/xml");

$sxe = new SimpleXMLElement($xmlData);

$xml = simplexml_load_string($xmlData);
$fieldData = simplexml_load_string($fieldData);

foreach ($fieldData->xpath('//field') as $record) {
	$result[] = array(
		'nick' => (string) $record->nick,
		'name' => (string) $record->name
	);
}
function searchForId($id, $array) {
   foreach ($array as $key => $val) {
       if ($val['nick'] === $id) {
           return $key;
       }
   }
   return null;
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
					$imageXMLData = file_get_contents("https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetImageInfo/$collection/$pointer/xml");
					$imageXML = simplexml_load_string($imageXMLData);
					$height = $imageXML[0]->{'height'};
					$width = $imageXML[0]->{'width'};
					if ($width > 800) {
						$percentage = (800 / $width) * 100;
					}
					echo "<img src=\"http://www.idaillinois.org/utils/ajaxhelper/?CISOROOT=$collection&CISOPTR=$pointer&action=2&DMSCALE=$percentage&DMWIDTH=$width&DMHEIGHT=$height&DMX=0&DMY=0\">\n"; ?>
			</center>
			<p />
                        <table border="1">
                        <?php
				foreach ($sxe->children() as $child) {
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

