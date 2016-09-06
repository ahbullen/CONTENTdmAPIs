<?php
$xmlData = file_get_contents('https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCollectionList/xml');

$xml = simplexml_load_string($xmlData);

$result = array();

foreach ($xml->xpath('//collection') as $record) {
	$result[] = array(
		'alias' => (string) $record->alias,
		'name' => (string) $record->name
	);
}

$resultCount = count($result) - 1;
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
			<ol>
			<?php
				for ($i=0;$i<=$resultCount;$i++) {
				        $alias = $result[$i]["alias"];
				        $name = $result[$i]["name"];
					$alias = str_ireplace("/", "", "$alias");
        				echo "\t\t<li><strong>$name</strong> <em>($alias)</em></li>\n";
				}
			?>
			</ol>
		</div>
		<div id="footer">
			<p />
			<strong>Our Library</strong>
		</div>
	</body>
</html>
