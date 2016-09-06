<?php

class simple_xml_extended extends SimpleXMLElement
{
    public    function    Attribute($name)
    {
        foreach($this->Attributes() as $key=>$val)
        {
            if($key == $name)
                return (string)$val;
        }
    }
}


$xmlData = file_get_contents('https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmQuery/linl/title^^all^and/title!subjec!descri/title/100/1/0/0/0/0/xml');

// Create the document object

$xml = simplexml_load_string($xmlData);

$pager = array();

// How many hits did the search yield

foreach ($xml->xpath('//pager') as $hits) {
	$pager[] = array(
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

$collectionName = $result[0]["collection"];
$collectionName = str_ireplace("/", "", "$collectionName");
$collection = $collectionName;


$dmCP = file_get_contents("https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCollectionParameters/$collectionName/xml");
$collectionParameters = simplexml_load_string($dmCP, 'simple_xml_extended');
$collectionName = $collectionParameters->name;

$numberOfHits = $pager[0]["total"];

$resultCount = count($result) - 1;

?>

<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<?php echo "<title>Demo for a Masonry-browse of the $collectionName</title>\n"; ?>
		<link rel="stylesheet" type="text/css" href="css/default.css" />
		<link rel="stylesheet" type="text/css" href="css/component.css" />
		<script src="js/modernizr.custom.js"></script>
	</head>
	<body>
		<div class="container">
			<!-- Top Navigation -->
			<header>
                                <nav class="codrops-demos">
		                        <h1 style="text-align: center;"><?php echo $collectionName ?></h1>
                		        <h2 style="text-align: center;">Browsing collection <?php echo $collection ?>, with <?php echo $numberOfHits ?> results</h2>
				</nav>
			</header>
			<ul class="grid effect-1" id="grid">
                        <?php
for ($i=0;$i<=$resultCount;$i++) {
        $title = $result[$i]["title"];
        $subject = $result[$i]["subject"];
        $description = $result[$i]["descri"];
        $thumb = $result[$i]["thumb"];
	$imgStr = "http://www.idaillinois.org/utils/getthumbnail/collection/linl/id/" . $thumb;
	echo "<li><a href=\"dmGetItemInfoWebPage.php?collection=$collection&pointer=$thumb\"><img src=\"$imgStr\"></a><br />$title</li>\n";
}
?>

			</ul>
		</div>
		<script src="js/masonry.pkgd.min.js"></script>
		<script src="js/imagesloaded.js"></script>
		<script src="js/classie.js"></script>
		<script src="js/AnimOnScroll.js"></script>
		<script>
			new AnimOnScroll( document.getElementById( 'grid' ), {
				minDuration : 0.4,
				maxDuration : 0.7,
				viewportFactor : 0.2
			} );
		</script>
	</body>
</html>
