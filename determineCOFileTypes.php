<?php

	$collection = "p16614coll23"; $pointer = 232;
        $coData = file_get_contents("https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetCompoundObjectInfo/$collection/$pointer/xml");
        $coXML = simplexml_load_string($coData);
        $cpd = $coXML->{"type"};
        foreach ($coXML->xpath('//page') as $coRecord) {
        	$pagePtr = (string) $coRecord->pageptr;
		$pageFile = (string) $coRecord->pagefile;

		$typeOfFile = explode ('.',$pageFile);
		switch ($typeOfFile[1]) {
			case "jp2":
				echo "$pageFile IMAGE! JPEG2000\n";
				break;
			case "mp3":
				echo "$pageFile MPEG\n";
				break;
			case "pdf":
				echo "$pageFile PDF\n";
				break;
			case "mid":
                                echo "$pageFile MIDI\n";
                                break;
			case "aif":
                                echo "$pageFile AIFF\n";
                                break;
		}

		//  feed it the API like:  https://server16614.contentdm.oclc.org/dmwebservices/index.php?q=dmGetStreamingFile/p16614coll23/232.pdf/xml

	}
?>

