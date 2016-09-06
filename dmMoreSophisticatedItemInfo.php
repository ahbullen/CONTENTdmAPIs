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


$typeOfRecord = $xml[0]->{"find"};
$pos = strpos($typeOfRecord, ".cpd");

// This bit should be expanded; it just separates Compound objects from single images. You might want to match this per file ending for more sensitive treatment...

if ($pos === false) { $typeOfRecord = "Image"; }
else {
	$typeOfRecord = "Text"; 
	$flipBookStr = getCompoundObjectData($collection,$pointer); 
}

function searchForId($id, $array) {
   foreach ($array as $key => $val) {
       if ($val['nick'] === $id) {
           return $key;
       }
   }
   return null;
}

function compoundObjectHeader() {

	// this just builds up css and js html code that the image doesn't need

	echo <<<EOHEADER

<style type="text/css">
#magazine {
        width:80%;
        height:80%;
}

#magazine .turn-page {
        background-color:#f7941d;
        background-size:100% 100%;
}
</style>

<script src="http://www.finditillinois.org/idaTest/js/modernizr.custom.js"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="http://www.finditillinois.org/flipBook/js/turn.min.js"></script>

                <script src="http://www.finditillinois.org/idaTest/js/modernizr.custom.js"></script>
                <script language="javascript">
                        function switchMenu(obj) {
                        var el = document.getElementById(obj);
                        if ( el.style.display != 'none' ) {
                                el.style.display = 'none';
                        }
                                else {
                                el.style.display = '';
                        }
                        }
                </script>

EOHEADER;
}

function compoundObjectFooter() {

	// just builds a javascript program at the footer

	echo <<<EOFOOTER
                                </center>
                        </section>
                <script src="http://www.finditillinois.org/idaTest/js/uisearch.js"></script>
                <script>
                        new UISearch( document.getElementById( 'sb-search' ) );
                </script>
                <script>
                        new GridScrollFx( document.getElementById( 'grid' ), {
                                viewportFactor : 0.4
                        } );
                </script>
<script type="text/javascript">
        \$(window).ready(function() {
                \$('#magazine').turn({
                        display: 'double',
                        acceleration: true,
                        gradients: !\$.isTouch,
                        elevation:50,
                        when: {
                                turned: function(e, page) {
                                        /*console.log('Current view: ', \$(this).turn('view'));*/
                                }
                        }
                 });
        });
        \$(window).bind('keydown', function(e){

                if (e.keyCode==37)
                        \$('#magazine').turn('previous');
                else if (e.keyCode==39)
                        \$('#magazine').turn('next');

        });
</script>
EOFOOTER;

}

function getCompoundObjectData($collection, $pointer) {

	$pageFileStr = "\t<strong style=\"color: #333333; background-color: #f7941d;\">Use your arrow keys (&lt;-- &amp; --&gt;) to navigate</strong>\n\t<p />\n\t<div id=\"magazine\">\n";

	// gets the compound object pages, then places them in the "pages" of the flipbook

        $coData = file_get_contents("https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCompoundObjectInfo/$collection/$pointer/xml");
        $coXML = simplexml_load_string($coData);
        $cpd = $coXML->{"type"};
        if ($cpd == "Monograph") {
	        foreach ($coXML->xpath('//node') as $coRecord) {
        	        foreach ($coRecord->xpath('//page') as $page) {
					$pagePtr = (string) $page->pageptr;
					$pageFileStr = $pageFileStr . "<div style=\"background-image:url(http://www.idaillinois.org/utils/ajaxhelper/?CISOROOT=$collection&CISOPTR=$pagePtr&action=2&DMSCALE=100&DMWIDTH=3000&DMHEIGHT=3000&DMX=0&DMY=0\");\"></div>\n";
                        }
                }
	}
        else {
		foreach ($coXML->xpath('//page') as $coRecord) {
                	$pagePtr = (string) $coRecord->pageptr;
                        $pageFileStr = $pageFileStr . "<div style=\"background-image:url(http://www.idaillinois.org/utils/ajaxhelper/?CISOROOT=$collection&CISOPTR=$pagePtr&action=2&DMSCALE=100&DMWIDTH=3000&DMHEIGHT=3000&DMX=0&DMY=0\");\"></div>\n";
		}
        }
	$pageFileStr = $pageFileStr . "\n\t</div>\n";
	return ($pageFileStr);
}

?>

<html>
        <head>
		<?php 
			if ($typeOfRecord == "Text") { compoundObjectHeader(); } 
		?>
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
						echo $flipBookStr;
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
	 		<?php 
				if ($typeOfRecord == "Text") { compoundObjectFooter(); } 
			?>

        </body>
</html>
