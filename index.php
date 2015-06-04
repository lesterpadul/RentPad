<?php

function execUrl($url){
	$request = curl_init();

	curl_setopt_array($request, array
	(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HEADER         => FALSE,
			CURLOPT_SSL_VERIFYPEER => TRUE,
			CURLOPT_CAINFO         => 'cacert.pem',
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_MAXREDIRS      => 10,
	));

	$response = curl_exec($request);
	curl_close($request);

	$document = new DOMDocument();

	if($response)
	{
	    libxml_use_internal_errors(true);
	    $document->loadHTML($response);
	    libxml_clear_errors();
	}

	return array("document"=>$document,"response"=>$response);
}

#execute url
$document = execUrl("http://rentpad.com.ph/long-term-rentals/cebu/apartment");

#perform xpath
$xpath = new DOMXpath($document["document"]);

#get text header
$properties = $xpath->query('//div[@class="view-tile-left-floater listing-holder"]'); 

#loop through each of the row items
foreach($properties as $container) {

	//get the anchor tag
  $arr = $container->getElementsByTagName("a");

  //loop through the items
  foreach($arr as $item) {
   	
  	#links
    $href =  $item->getAttribute("href");
    $explodedHref  = explode("/", $href);

		#container doc
    $containerDoc = execUrl($href);
  	
    #xpath the content of each row
    $xpathRow = new DOMXpath($containerDoc["document"]);

    #get title
    $rowTitle = $xpathRow->query('//span[@itemprop="name"]');
    $propTitle = "";
    foreach($rowTitle as $rowItem){ $text = trim(preg_replace("/[\r\n]+/", " ", $rowItem->nodeValue));  $propTitle = $text;  }

    #get ID
   	$propId  = $explodedHref[count($explodedHref)-1];
   	
   	#get the prop desc
   	$propDesc = "";
   	$rowDesc = $xpathRow->query('//span[@style="font-size: 14px; line-height: 20px;"]');
   	foreach($rowDesc as $rowItem){ $propDesc = trim(preg_replace("/[\r\n]+/", " ", $rowItem->nodeValue)); }

   	#get the prop location
   	$propLocation = "";
   	$rowLocation = $xpathRow->query('//span[@style="font-size:14px; font-weight: normal; margin-top:10px; position: relative; top:-5px;"]');
   	foreach($rowLocation as $rowItem){ $propLocation = trim(preg_replace("/[\r\n]+/", " ", $rowItem->nodeValue)); }

   	#get the prop contact name
		$propContactPerson = "";
		$rowContactPerson = $xpathRow->query('//div[@id="contact-name"]');
   	foreach($rowContactPerson as $rowItem){ $propContactPerson = trim(preg_replace("/[\r\n]+/", " ", $rowItem->nodeValue)); }

   	#get the prop contact email
		$propContactEmail    = "";
		$rowContactEmail = $xpathRow->query('//div[@id="contact-email"]');
   	foreach($rowContactEmail as $rowItem){ $propContactEmail = trim(preg_replace("/[\r\n]+/", " ", $rowItem->nodeValue)); }

   	#get the prop contact mobile
		$propContactMobile   = "";
		$rowContactMobile = $xpathRow->query('//div[@id="contact-mobile"]');
   	foreach($rowContactMobile as $rowItem){ $propContactMobile = trim(preg_replace("/[\r\n]+/", " ", $rowItem->nodeValue)); }

   	#get the prop contact landline
		$propContactLandline = "";
		$rowContactLandline = $xpathRow->query('//div[@id="contact-landline"]');
   	foreach($rowContactLandline as $rowItem){ $propContactLandline = trim(preg_replace("/[\r\n]+/", " ", $rowItem->nodeValue)); }

		$itemRight  = $xpathRow->query('//table[@id="table-listing-details"]');

		#get prop price
		$propPrice      = "";
		$propCity       = "";
		$propLoc        = "";
		$propFloor      = "";
		$propSq         = "";
		$propBedRooms   = "";
		$propBathRooms  = "";
		$propFurnishing = "";

		#get the table 
		foreach($itemRight as $rowItem):
			$cNodes = $rowItem->childNodes;


			for($i=0;$i<$cNodes->length;$i++):
				$tdcNodes = $cNodes->item($i)->childNodes;
				switch ($i) {
					case 1:
						$propPrice = trim(preg_replace("/[\r\n]+/", " ", $tdcNodes->item(2)->nodeValue));
						break;
					case 5:
						$propLoc = trim(preg_replace("/[\r\n]+/", " ", $tdcNodes->item(2)->nodeValue));
						break;
					case 6:
						$propFloor = trim(preg_replace("/[\r\n]+/", " ", $tdcNodes->item(2)->nodeValue));
						break;
					case 7:
						$propSq = trim(preg_replace("/[\r\n]+/", " ", $tdcNodes->item(2)->nodeValue));
						break;
					case 8:
						$propBedRooms = trim(preg_replace("/[\r\n]+/", " ", $tdcNodes->item(2)->nodeValue));
						break;
					case 9:
						$propBathRooms = trim(preg_replace("/[\r\n]+/", " ", $tdcNodes->item(2)->nodeValue));
						break;
					case 10:
						$propFurnishing = trim(preg_replace("/[\r\n]+/", " ", $tdcNodes->item(2)->nodeValue));
						break;
					default:
						break;
				}
			endfor;
		endforeach;

    echo "<pre>";
    echo "TITLE : {$propTitle} <br/>";
    echo "ID : {$propId} <br/>";
    echo "DESC : {$propDesc} <br/>";
    echo "LOCATION : {$propLocation}";
    var_dump("=======================================================================================================");
    

    echo "</pre>";
    die();

  }
}