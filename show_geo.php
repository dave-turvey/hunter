<html>
<head>
</head>
<body>
<p>Starting file read</p>
<script>
<?php

 // if this is a page refresh then we should have the lat lon as params
 if(empty($_GET))
 {
 	echo "document.write('No lat long data passed in URI');";
 	$lat="-1";
 	$lon="-1";
 }
 else
 { 
 	$clat = $_GET['lat'];
 	$clon = $_GET['lon'];
 }
 // write the javascript vars that contain the data we need
 
 echo "var ip_addr='".$_SERVER["REMOTE_ADDR"]."';\n";
 echo "function player(ip, lat, lon){ this.ip = ip; this.lat = lat; this.lon = lon; }\n";
 echo "var players=new Array();\n"; 
 echo "var p;\n"; 
 $geoFile = "geo.dat";
 $fh = fopen($geoFile, 'r+') or die("Unable to open geo data storage file on server");
 $tmpFile = uniqid(); //should work but doesnt
 $tmpFile = $tmpFile.".dat";
 $th = fopen($tmpFile, 'w') or die("Unable to open temporary data storage file on server");
 $i = 0;
 $hostFound=false;
 while(!feof($fh))
 {
  if(($line=fgets($fh))!=false){
   $parts=explode(',',$line);
   $ip=trim($parts[0]);
   $lat=floatval($parts[1]);
   $lon=floatval($parts[2]);
   if(ip2long($ip)==ip2long($_SERVER["REMOTE_ADDR"]))
   {
    $output = $ip.",".$clat.",".$clon;
    fwrite($th,$output."\n") or die ("failed to write data");
    $hostFound=true;
    $str = "Found target <br>";
    echo "document.write('".$str."');";    
   }else{
      	$str = "Missed target <br>";
    	echo "document.write('".$str."');";  
    	$line=$ip.",".$lat.",".$lon;
    	fwrite($th,$line."\n") or die ("failed to write data");
   }
   echo "p=new player('".$ip."','".$lat."','".$lon."');";
   echo "players[".$i."]=p;";
   $i+=1;
  }
 }
 // If this ip isnt registered add it to the list of players
 if(!$hostFound)
 {
 	$str = "New target <br>";
    echo "document.write('".$str."');";  
    $output = $ip.",".$lat.",".$lon;
    fwrite($th,$output."\n");
 }
 
 // Rename the temporary file so that it overwrites the original - this updates the callers location
 // but has the potential side effect of lost updates 
 fclose($th);
 fclose($fh);
 rename($tmpFile,$geoFile) or die("unable to rename tmpfile to geofile");
 // Hack to allow remote editing of geo file contents - remove for production
 chmod($geoFile,0777) or die("unable to change file ownership");
 
 // Test print of the values in the javascript array
 echo "for (var i in players) { document.write(players[i].ip+','+players[i].lat+','+players[i].lon+'<br>'); }";
 
?>

// timer callback function
function updateLocation()
{
	if (navigator.geolocation)
    {
    	navigator.geolocation.getCurrentPosition(updatePosition);
  	}
}

// call the page with the updated lat long
function updatePosition(position)
{
	$loc = "show_geo.php";
	$loc = $loc +"?lat="+position.coords.latitude+"&lon="+position.coords.longitude;
    window.location.href = $loc;
}

// refresh the page every second
setTimeout(function(){updateLocation();},5000);


</script>
</p>End of file read</p>
</body>
</html>