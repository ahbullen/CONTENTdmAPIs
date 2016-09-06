<?php

$collection = "linl";

$xmlData = file_get_contents("https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmQuery/$collection/title^%20^all^and/title!subjec!descri/title/40/1/0/0/0/0/xml");

// Create the document object

$xml = simplexml_load_string($xmlData);

$pager = array();

// Get the nodes and loop them

foreach ($xml->xpath('//record') as $record) {
	$result[] = array(
		'title' => (string) $record->title,
		'descri' => (string) $record->descri,
		'thumb' => (string) $record->pointer
	);
}

$resultCount = count($result) - 1;
?>

<!DOCTYPE html>
<html lang="en">
        <head>
                <meta charset="UTF-8" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Scattered Polaroids!</title>
                <link rel="stylesheet" type="text/css" href="http://www.finditillinois.org/ida/includes/normalize4.css" />
                <link rel="stylesheet" type="text/css" href="http://www.finditillinois.org/ida/includes/demo4.css" />
                <link rel="stylesheet" type="text/css" href="http://www.finditillinois.org/ida/includes/component4.css" />
                <script src="http://www.finditillinois.org/ida/includes/modernizr.min.js"></script>
        </head>
        <body>
                <div class="container">
                        <header class="codrops-header">
                                <h1>The Springfield Aviation Company</span></h1>
                        </header>
                        <section id="photostack-3" class="photostack">
                                <div>
				<?php
					for ($i=0;$i<=$resultCount;$i++) {
					        $title = $result[$i]["title"];
					        $thumb = $result[$i]["thumb"];
						$description = $result[$i]["descri"];

					        $imgStr = "http://www.idaillinois.org/utils/ajaxhelper/?CISOROOT=$collection&CISOPTR=$thumb&action=2&DMSCALE=25&DMWIDTH=300&DMHEIGHT=300&DMX=0&DMY=0&DMROTATE=0";
						echo "<figure>\n";
        					echo "<a href=\"dmMoreSophisticatedItemInfo.php?collection=$collection&pointer=$thumb\" title=\"$description\" class=\"photostack-img\"><img src=\"$imgStr\"></a>\n";
        	                                echo "<figcaption>\n";
	                                        echo "<h2 class=\"photostack-title\">$title</h2>\n";
                	                        echo "<div class=\"photostack-back\">\n";
                                        	echo "<p>$description</p>\n";
	                                        echo "</div>\n";
        	                                echo "</figcaption>\n";
                	                        echo "</figure>\n";
					}
				?>
                                </div>
                        </section>
                </div><!-- /container -->
                <script src="http://www.finditillinois.org/ida/includes/classie4.js"></script>
                <script src="http://www.finditillinois.org/ida/includes/photostack.js"></script>
                <script>
                        // [].slice.call( document.querySelectorAll( '.photostack' ) ).forEach( function( el ) { new Photostack( el ); } );
                        new Photostack( document.getElementById( 'photostack-3' ), {
                                callback : function( item ) {
                                        //console.log(item)
                                }
                        } );
                </script>
        </body>
</html>
