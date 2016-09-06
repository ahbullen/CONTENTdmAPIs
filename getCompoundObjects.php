<?php

$xmlData = file_get_contents('https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCompoundObjectInfo/isl/40168/xml');

// Create the document object

$xml = simplexml_load_string($xmlData);

$cpd = $xml->{"type"};
$title = $xml->{"node"}->{"nodetitle"};
$pointer = $xml->{"node"}->{"page"}[0]->{"pageptr"};

$result = array();

// Get the <page> nodes and loop them

if ($cpd == "Monograph") {
	foreach ($xml->xpath('//node') as $record) {
		foreach ($record->xpath('//page') as $page) {
	    		$result[] = array(
				'nodeTitle' => (string) $record->nodetitle,
        			'pageTitle' => (string) $page->pagetitle,
	        		'pageFile' => (string) $page->pagefile,
        			'pagePtr' => (string) $page->pageptr
			);
		}
	}
}
else {
foreach ($xml->xpath('//page') as $record) {
        	$result[] = array(
			'nodeTitle' => "NONE",
                        'pageTitle' => (string) $record->pagetitle,
                        'pageFile' => (string) $record->pagefile,
                        'pagePtr' => (string) $record->pageptr
                );
        }
}

// $resultCount is the number of elements in the $result array; the first element is result[0]

$resultCount = count($result) - 1;

$oldNodeTitle = "";

for ($i=0;$i<=$resultCount;$i++) {
	$nodeTitleStr = $result[$i]["nodeTitle"];
	$pageTitleStr = $result[$i]["pageTitle"];
	$jpegStr = $result[$i]["pageFile"];
	$pointer = $result[$i]["pagePtr"];
	if ($oldNodeTitle != $nodeTitleStr) { echo "NODE TITLE: $nodeTitleStr\n"; }
	$oldNodeTitle = $nodeTitleStr;
	echo "$pageTitleStr $jpegStr $pointer\n";
}

?>
