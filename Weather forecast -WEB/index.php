<?php
    header('Access-Control-Allow-Origin: *');

    if($_GET["unit1"] == "si")
     $unit="si";
     else
     $unit="us";
     
        $urlA = rawurlencode("https://maps.googleapis.com/maps/api/geocode/xml?");
        $urlB = urlencode("address=".$_GET["address1"]. "," .  $_GET["city1"] . "," . $_GET["state1"] . "&key=AIzaSyAjBE8Khra6i01lddq7_PmsTPqa6qWla-0");
        $url = $urlA . $urlB;                           
        $xml = simplexml_load_file($url) or die("url not loading"); 
          if($xml->result)
          {
         $lat = (string) $xml->result->geometry->location->lat;
         $long = (string) $xml->result->geometry->location->lng;
         }
         else{
         $lat=NULL;
         $long=NULL;
         }

         if($lat && $long)
         {
             $urlFa="https://api.forecast.io/forecast/01f5252f70291f9066491f595975ab0b/$lat,$long?";
             $urlFb="units=$unit&exclude=flags";
             $foreCastApiUrl=$urlFa.$urlFb;
             
             $json = file_get_contents($foreCastApiUrl);
             $obj = json_decode($json);

             date_default_timezone_set($obj->timezone);

                for ( $i = 0; $i <= 7; $i++)

                {

                    $riseTime = $obj->daily->data[$i]->sunriseTime;

                    $setTime = $obj->daily->data[$i]->sunsetTime;

                    $obj->daily->data[$i]->sunriseTime = date('h:i A', $riseTime);

                    $obj->daily->data[$i]->sunsetTime = date('h:i A', $setTime);
                    
                    $time = $obj->daily->data[$i]->time;
                    $obj->daily->data[$i]->{'today'} = date("M j", $time);
                    $obj->daily->data[$i]->{'day'} = date("l", $time);


                }
             
             for($i=0;$i<=48;$i++)
            {
                $time = $obj->hourly->data[$i]->time;
                $obj->hourly->data[$i]->time = date('h:i A', $time);
            }
             
             echo json_encode($obj);
        }
    

?>
