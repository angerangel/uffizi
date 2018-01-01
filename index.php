<?php 
//****For debugging*******************
error_reporting(E_ALL & ~E_DEPRECATED);  
ini_set('display_errors',0);  
//*************************************

//****Configuration*****
ini_set('memory_limit', '100M'); //set the memeroy usage, with big images you need a lot of memory!
$max_file_p_pa_h = 4 ; //max files per pag hor
$max_file_p_pa_v = 4 ; //max files per pag ver
$max_file_pa = $max_file_p_pa_v * $max_file_p_pa_h ; //max files per page
$base_url = 'www.maxvessi.net/uffizi/pictures' ; //you must put your main folder without http://
$searchboxprefill = 'Cerca'; //here you put the 
$max1_x = 100 ; //max x size for preview images
$max1_y = 100 ; //max y size for preview images
$max2_x = 640 ; //max x size big photos
$max2_y = 420 ; //max y size big photos
$images = array('jpg','gif','jpeg', 'png');
$videos = array('3gp','avi','wmv','flv','ogv','webm','mp4','mov','f4v','3g2');
$audios = array('wav','mid','mp3','ogg','aac','m4a');
$passwords = array(max => "mypassword", ale => "ale"); //add users and password
$levels = array(max => 2, ale => 1) ; //choose user level of security. 0 level are public files; levels 1 can see only levels 0 and 1; levels 2 can see levels 0, 1  and 2; and so on... There is no level limit you can assign.
//******************End of configuration**********************
//Author: Massimiliano Vessi 
//email: maxint@tiscali.it
//********************GPL License***************************
/*This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>. */
//***********************************************   
$version = "8.1"; 
$jw_videos = array("mp4", "webm", "ogv", "flv", "mov", "f4v", "3gp", "3g2"); //videos readed by JWPlayer
$jw_audios = array("aac", "m4am", "ogg", "mp3");  //audios readed by JWPLayer
 $videocounter = 0 ;
//***************HEADER**************************


//***********GPS reading function*******************
/**
 * Returns an array of latitude and longitude from the Image file
 * @param image $file
 * @return multitype:number |boolean
 */
function read_gps_location($file){
    if (is_file($file)) {
        $info = exif_read_data($file);
        if (isset($info['GPSLatitude']) && isset($info['GPSLongitude']) &&
            isset($info['GPSLatitudeRef']) && isset($info['GPSLongitudeRef']) &&
            in_array($info['GPSLatitudeRef'], array('E','W','N','S')) && in_array($info['GPSLongitudeRef'], array('E','W','N','S'))) {

            $GPSLatitudeRef  = strtolower(trim($info['GPSLatitudeRef']));
            $GPSLongitudeRef = strtolower(trim($info['GPSLongitudeRef']));

            $lat_degrees_a = explode('/',$info['GPSLatitude'][0]);
            $lat_minutes_a = explode('/',$info['GPSLatitude'][1]);
            $lat_seconds_a = explode('/',$info['GPSLatitude'][2]);
            $lng_degrees_a = explode('/',$info['GPSLongitude'][0]);
            $lng_minutes_a = explode('/',$info['GPSLongitude'][1]);
            $lng_seconds_a = explode('/',$info['GPSLongitude'][2]);

            $lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
            $lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
            $lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
            $lng_degrees = $lng_degrees_a[0] / $lng_degrees_a[1];
            $lng_minutes = $lng_minutes_a[0] / $lng_minutes_a[1];
            $lng_seconds = $lng_seconds_a[0] / $lng_seconds_a[1];

            $lat = (float) $lat_degrees+((($lat_minutes*60)+($lat_seconds))/3600);
            $lng = (float) $lng_degrees+((($lng_minutes*60)+($lng_seconds))/3600);

            //If the latitude is South, make it negative. 
            //If the longitude is west, make it negative
            $GPSLatitudeRef  == 's' ? $lat *= -1 : '';
            $GPSLongitudeRef == 'w' ? $lng *= -1 : '';

            return array(
                'lat' => $lat,
                'lng' => $lng
            );
        }           
    }
    return false;
}


?>

<html>

<head>
<link rel="stylesheet" type="text/css" <?php echo "href=http://$base_url/.css/mystyle.css />"; ?>
<!-- JWPlayer configuration -->
<?php echo "<script type=\"text/javascript\" src=\"http://$base_url/.jwplayer/jwplayer.js\"></script>"; ?>
</head>


<body>
<DIV ALIGN="CENTER">
<div id=topleft > 
<div id=bottomright >

<h1>UFFIZI <i>web gallery</i></h1>

<?php 
//box di ricerca
echo "<iframe src=\"https://duckduckgo.com/search.html?width=220&site=" .  $base_url . "&prefill=" . $searchboxprefill ."\" style=\"overflow:hidden;margin:0;padding:0;width:278px;height:40px;\" frameborder=0></iframe>" ;
?>


<?php
echo "<h1>" ;
echo $SERVER['PHP_SELF'];
echo "</h1>"; 
//*****************************************
//Security

$level = 0 ;

if ($_GET[Logon] == "yes" ) {
	 echo "<form method=post action=\"{$_SERVER['php_self']}?Logon=no";
	if (isset($_GET[img])) {echo "&img=$img";}
	if (isset($_GET[page])) {echo "&page=$page";}
	 echo "\">
	<i>User:	<input type=\"text\" name=\"user\">
	Password: <input type=\"password\" name=\"password\"></i>
	<input type=\"submit\">
	</form><br>" ;
	}

if (isset($uffizzi[user])) { 	
 	$user = $uffizzi[user];
 	$password = $uffizzi[password];
	 }
	
if ($_GET[Logoff] == "yes") {
 	setcookie("uffizzi[user]","" ,time()-3600);
 	setcookie("uffizzi[password]","", time()-3600);
 	$user = "xxx";
 	$password = "xxx";
	 } //cancelliamo il cookie
	

if (isset($_POST[user])) {
 	$user = $_POST[user];
	$password = $_POST[password];
	}  
 	
 if (isset($user) && ($password === $passwords[$user]) ) {
 	 	setcookie("uffizzi[user]",$user, time() +2592000 );
		setcookie("uffizzi[password]",$password, time() +2592000 );
 	 	$level = $levels[$user];
 	 	echo "User: $user , your level is $level. <small>(<a href=\"{$_SERVER['php_self']}?Logoff=yes";
		if (isset($_GET[img])) {echo "&img=$img";}
		if (isset($_GET[page])) {echo "&page=$page";}
		echo "\">Logout</a>)</small><br><br>";
		} else {
			if (isset($user)) {	
			 	if ($_GET[Logoff] == "yes") {echo "<font color=#FF0000 >You are logout</font><br>";} else {
					echo "<font color=#FF0000 >Bad username or password</font><br>";}
				setcookie("uffizzi[user]","", time()-3600);
			 	setcookie("uffizzi[password]","", time()-3600);	
				}
			}
//End of security configuration

//Starting folder analasys

if (!(file_exists('.img/'))) {mkdir('.img/');} //cheack .img/ folder existance

$files1 = scandir('.',1); //file list in reverse order

//removing not video, not image, not audio, hidden files from file list
$all_exts = array_merge($videos, $images, $audios); //all extensions
$temp = 0;
foreach	($files1 as $value) {
 	$test = false ;
 	if (is_dir($value)) {$test = true ;} else {  //we show directories
		$ext = end(explode('.', $value)); //we get file extension
		$ext = strtolower($ext); //we transform extension in lower letters
		foreach ($all_exts as $value2) {
			if ( $value2 == $ext ) { $test = true ;} //it's something to show
			}
		}
	if ( substr( $value,0, 1) == '.') {$test = false ; } //we remove all filese starting for "."  (Linux hidden files)
	if ($level > 0 ) {
 	 	for ( $i = 1 ; $i <= $level ; $i++) {
			if ( substr( $value,0, 1) == '-') {   //hidden files, only users with correct level can see it
				$value = substr($value,1);				
				} 
			} 
		}	
	
	if ( substr( $value,0, 1) == '-') {$test = false ; }
	if ($test == false) { unset($files1[$temp]);} //it's removed from file list
	$temp = $temp + 1;
	}


//This correct the order of secret files to show, because they start with "-"
$files_temp = array();
foreach ($files1 as $item) {
 	$name = $item ;
	if ( substr( $item,0, 1) == '-') {$name = substr($item,1);}
	$files_temp[$name] = $item ;
	}

//In the base directory we put in reverse order the folder,  
//this way the last inserted folder are showe as first if you put the date
//in the subfolder in the right order
$temp = "http://" . $base_url . "/" ; //http://www.maxvessi.net/uffizzi/pictures/
$temp2 = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"]; //  http://www.maxvessi.net/uffizzi/pictures/example/index.php or else

$temp3 = strlen($temp); //example: 38
$temp2 = substr($temp2, $temp3); //example: /example/index.php

if (strpos($temp2,"/") === false ) {
 	//we are in home folder
	krsort($files_temp);
	} else {
	ksort($files_temp);		
	//we are in subfolder
	}




$files1 = array_values($files_temp); //indexes array numerically ([0] , [1], [2], ... )

//Creating list of only images, without folders
$temp = 0;
$files3 = $files1 ; 
foreach	($files3 as $value) {
 	if (is_dir($value)) {	unset($files3[$temp]);}
 	$temp = $temp + 1;
 		}
$files3 = array_values($files3) ; //indexes array numerically ([0] , [1], [2], ... )

//Useful function to remove unwanted charachters, for example ""underscore":
function removeunder ($arg) {
 	$unwantedchars = array("_", "-");
	$tag = str_replace($unwantedchars , " ", "$arg");
	return $tag;
}

//Function to create a thumb preview inside folders, if it contains images
function folder_preview ($arg) {
 	global $images, $max1_x, $max1_y; 	
 	$test = false; 
 	$files1 = scandir($arg,1);		
 	if (!(file_exists($arg . '/.img/'))) {  mkdir( $arg . '/.img/');}
 	$test = false ;
 	foreach ($files1 as $value) { 
 	 	$system = pathinfo($value);
 	 	$ext = $system['extension']; //we get file extension
		$ext = strtolower($ext); //we trasnform extension in lower letters		
		//check if it's a image with the $images array, image will be converted in jpg format
		foreach ($images as $value2) {
		 	if ($value2 == $ext ) {	 	 	 	 	
			 	$newname = "{$arg}/.img/{$max1_x}x{$max1_y}_preview.jpg";		 	
				createthumb($arg . '/' . $value,$newname,$max1_x,$max1_y);
				$test = true ;
				}
			}
		if ($test == true) { break ;}
		}	
	}
			
//call folder_preview if needed	
if (!(file_exists('.img/' . $max1_x . 'x'. $max1_y . '_preview.jpg'))) {
		folder_preview('.') ;		
		}
	
 	
$files3_n = count($files3);//number of files without directories
$files2 = count($files1) ; //number of files in this directory
$temp = $files2 / $max_file_pa ; 
$pages = ceil( $temp) ; //number of pages


//Function to get EXIF orientation data
 function imagecreatefromjpegexif($filename)
    {
        $img = imagecreatefromjpeg($filename);
        $exif = exif_read_data($filename);
        if ($img && $exif && isset($exif['Orientation']))
        {
            $ort = $exif['Orientation'];

            if ($ort == 6 || $ort == 5)
                $img = imagerotate($img, 270, null);
            if ($ort == 3 || $ort == 4)
                $img = imagerotate($img, 180, null);
            if ($ort == 8 || $ort == 7)
                $img = imagerotate($img, 90, null);

            if ($ort == 5 || $ort == 4 || $ort == 7)
                imageflip($img, IMG_FLIP_HORIZONTAL);
        }
        return $img;
    }

//Preview functions:
//resizing function
function createthumb ($name,$filename,$new_w,$new_h){
	$system = pathinfo($name);
	if (preg_match('/jpg|jpeg/i',$system['extension'])) { $src_img=imagecreatefromjpegexif($name); }
	if (preg_match('/png/i',$system['extension'])) { $src_img=imagecreatefrompng($name); }
	if (preg_match('/gif/i',$system['extension'])) { $src_img=imagecreatefromgif($name); }
	$old_x = imageSX($src_img);
	$old_y = imageSY($src_img);	
	if ($old_x > $old_y) {
		$thumb_w = $new_w; 
		$thumb_h = $new_w*($old_y/$old_x) ; //$old_y*($new_h/$old_x);
		}
	if ($old_x < $old_y) {
		//$thumb_w = $old_x*($new_w/$old_y);
		$thumb_h = $new_h;
		$thumb_w = $new_h*($old_x/$old_y);
		}
	if ($old_x == $old_y) {	
		$thumb_w = $new_w;
		$thumb_h = $new_w*($old_y/$old_x) ;
		}
	$dst_img = ImageCreateTrueColor($thumb_w,$thumb_h);	
	//imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
	imagecopyresized($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
	$system = pathinfo($filename);
	if (preg_match("/png/i",$system['extension'])) { imagepng($dst_img,$filename); }		
	if (preg_match("/jpg|jpeg/i",$system['extension'])) { imagejpeg($dst_img,$filename); }
	if (preg_match("/gif/i",$system['extension'])) { imagegif($dst_img,$filename); }	
	imagedestroy($dst_img); 
	imagedestroy($src_img); 	
	return ;
	}

//make_shot function crates all the previews pages, containting $max_file_pa images or folders
function make_shot ($arg ) {
 	global $images , $videos , $max1_x , $max1_y , $audios , $jw_videos , $jw_audios, $base_url , $videocounter;
 	//check if it's a folder
 	$text = removeunder($arg);
 	if (is_dir($arg)) { 
 	 	folder_preview ($arg) ;
 	 	//copy index.php and .htaccess in subfolders
		if (!(file_exists('$arg/index.php'))) {
		 	copy('index.php', $arg . '/index.php');
		 	chmod("$arg/index.php", 0755);
			} 
		if (!(file_exists('$arg/.htaccess'))) {
		 	copy('.htaccess', $arg . '/.htaccess');
		 	chmod("$arg/.htaccess", 0755);
			} 	
		//end-copy index.php and .htaccess in subfolders
		//check if subfolder index.php and .htaccess is the newest, otherwise it will be overwritten by the new one	
		if (file_exists('$arg/index.php')) { 
		 	$temp_a = file_get_contents('$arg/index.php');
			$temp_b = file_get_contents('index.php');
			if ( strcmp($temp_a, $temp_b) != 0 ) {
				copy('index.php', $arg . '/index.php'); 
				chmod("$arg/index.php", 0755);
				}
			 } 
		if (file_exists('$arg/.htaccess')) { 
		 	$temp_a = file_get_contents('$arg/.htaccess');
			$temp_b = file_get_contents('.htaccess');
			if ( strcmp($temp_a, $temp_b) != 0 ) {
				 copy('.htaccess', $arg . '/.htaccess'); 
				 chmod("$arg/.htaccess", 0755);
				 }
			 } 				
		//end-update index.php and .htaccess in subfolders		
		//Now we must put the folder preview, but if doesnt exists, just a text:		
 		if (file_exists($arg . '/.img/' . $max1_x . 'x' . $max1_y . '_preview.jpg')) {echo "<td class=directory  align=center valign=middle><a href=\"$arg\">ALBUM<br><img class=directory src=\"$arg/.img/{$max1_x}x{$max1_y}_preview.jpg\"><br><br>$text</a></td>"; } else {
			echo "<td class=directory ><a href=\"$arg\" align=center valign=middle >$text</a></td>";
			}
		}
	//If it's not a folderm it's a file 
	$system = pathinfo($arg);
 	$ext = $system['extension']; //we get file extension
	$ext = strtolower($ext); //we trasnform extension in lower letters	
	//check if it's a image
	foreach ($images as $value2) {
		if ($ext == $value2 ) {
			if (!(file_exists(".img/{$max1_x}x{$max1_y}_{$arg}"))) {
				$newname = ".img/{$max1_x}x{$max1_y}_{$arg}";
				createthumb($arg,$newname,$max1_x,$max1_y);
				}
			echo "<td align=center valign=middle><a href=\"{$_SERVER['PHP_SELF']}?img=$arg\" ><img class=image src=\".img/{$max1_x}x{$max1_y}_{$arg}\" alt=\"$text\" > <br> $text </a></td>";
			return ;
			}
		}
	//check if it's a video	
	
	foreach ($videos as $value2) {
		if ($ext == $value2) {
		 	//Check if it's supported by JWPlayer
		 	if (in_array($ext, $jw_videos)) {
		 			$videocounter = $videocounter + 1 ;
				echo "<td align=center valign=middle width=$max1_x >
					<a href=\"{$_SERVER['PHP_SELF']}?img=$arg\" > 
				 	<div id=\"container$videocounter\"  >Carico il lettore ...</div>
					<script type=\"text/javascript\">
					jwplayer(\"container$videocounter\").setup({					
					controls: false ,
					autostart: true,
					file: \"$arg\",
					height: $max1_y ,
					width: $max1_x
					});
					</script>$text</a></td>" ;
					} else { 			
						echo "<td align=center valign=middle width=$max1_x ><a href=\"{$_SERVER['PHP_SELF']}?img=$arg\" > <video src=\"$arg\" width=$max1_x height=$max1_y controls=\"controls\" >$text <br><small>(your browser does not support the video tag)</small></video></a></td>" ;
					}
			return;
			}
		}
		//check if it's an audio
	foreach ($audios as $value2) {
		if ($ext == $value2) {
		 	//Control if it's supported by JWPlayer
		 	if (in_array($ext, $jw_audios)) {
		 		$videocounter = $videocounter + 1 ;
				echo "<td align=center valign=middle width=$max1_x >
					<a href=\"{$_SERVER['PHP_SELF']}?img=$arg\" > 
				 	<div id=\"container$videocounter\" >Carico il lettore ...</div>
					<script type=\"text/javascript\">
					jwplayer(\"container$videocounter\").setup({					
					controls: false ,
					file: \"$arg\",
					height: $max1_y ,
					width: $max1_x
					});
					</script>$text</a></td>" ;
					} else { 			
						echo "<td align=center valign=middle width=$max1_x ><a href=\"{$_SERVER['PHP_SELF']}?img=$arg\" > <audio src=\"$arg\" width=$max1_x controls=\"controls\" >$text <br> <small>(your browser does not support the audio tag)</small></audio></a></td>" ;
					}
			return;
			}
		}	
	echo "\n";	
	return ;
	}

//building TOP navigation links functions
//We store current url in an array, the array in a cookie

if (isset($_COOKIE['UFFIZIHIST'])) {
	$uff_hist = unserialize(base64_decode($_COOKIE['UFFIZIHIST']));
	//uncmment for debug
	/*echo "<br>*****************<br>";	
	print_r($uff_hist);
	echo "<br>*****************<br>";
	*/
	} else {
	$uff_hist = array() ;
	}




function directories()   {         
	$directory = $_SERVER["PHP_SELF"]; //esempio /temp/prova3.php
	$directories = array();
        $next_slash = 0;
        do {
		//Creates an array with all the parent folders
		//of the file where this function is called.
		$next_slash = strpos($directory, "/", $next_slash); //trova la prima / = 0 poi 5
		if($next_slash !== false)  {
			$next_slash++;
			$directories[count($directories)] = substr($directory, 0, $next_slash); // prina / poi /temp/
			}
            } while($next_slash !== false);
	   $temp2 =  end(array_values($directories)) ;
	
	return($directories);
        }

//Uses the directories() function to create the navigation links. 
function directory_navigation_links()  {
	global $base_url ; //example: 'www.maxvessi.net/uffizi/pictures' 
  	$index = substr_count($base_url, '/'); //it returns the number of '/', example: it returns 2
	$temp = substr_count($base_url, '/'); 
	$directories = directories(); //an array of directories of the current path	
	$links = "";
	global $uff_hist;   
	$numerodir =count($directories) ;
	$numerodir2 = $numerodir - 1;
        for($index ; $index < $numerodir ; $index++) {
            //Handling the display is a little harder than the links themselves.
            $dir_name = substr($directories[$index], 0, strlen($directories[$index]) - 1);
            $dir_name = substr($dir_name, strrpos($dir_name, "/") + 1, strlen($dir_name) - 1);
            if($index == $temp) {             		
		//it's the home
		if ($uff_hist[$dir_name] != ''){	
			$links .= "<a href=\"" . $uff_hist[$dir_name] . "\">Home</a> / ";		
			} else{		
			$links .= "<a href=\"" . $directories[$index] . "\">Home</a>  / ";
			}
		} else {            
			if ($uff_hist[$dir_name] != ''){		
			$links .= "<a href=\"" . $uff_hist[$dir_name] . "\">" . $dir_name . "</a> / ";
			} else{		
			$links .= "<a href=\"" . $directories[$index] . "\">" . $dir_name . "</a> / ";
			}			
		}		
		If ($index == $numerodir2) {
			// we are arrived a the current directory
			$uff_hist[$dir_name] = "http://" . $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; //store current path
			$temp3 =  base64_encode(serialize( $uff_hist));
			setcookie("UFFIZIHIST",$temp3,time()+60*60*24);						
			}
		}
         echo "<i>$links</i>" ;
	}
//END of buoling top navigation links functions

//This function create the table with images of directories and images/videos
function buildtable () {
	global $page, $max_file_pa, $files1, $max_file_p_pa_h, $max_file_p_pa_v, $pages, $max2_y, $base_url, $videocounter ;
 	directory_navigation_links() ;
	//****Bulding album page	
	//not we delete item from list already in prev pages
	//$files1 contains all files and directories names in the current directory
	if ($page > 1){
    	$deleting =	$max_file_pa * ($page - 1) ;
	 	for ($i = 0; $i < $deleting; $i++) {
		 	array_shift($files1);
	 		}
		}
	
	//navigation link creation operations:
	$prev = $page - 1 ;	
	//left arrow
	echo "<table width=100% ><tr><td align=left valign=middle >";
	if ($page > 1) { echo "<a href=\"index.php?page=$prev\"><img src=http://$base_url/.css/leftarrow.png  height=$max2_y width=50 ></a>";} 
	echo "</td><td align=center>";
	$next = $page + 1 ;
	
			
	//building main table with images
	echo "\n<table cellspacing=5 >\n";
	$temp1 = 0; //horizontal
	$temp2 = 0; //vertical
	foreach ($files1 as $value) {   
    		if ($temp1 <= $max_file_p_pa_h) {
        		if ($temp1 == 0) { echo "<tr>"; }
			make_shot($value); //basic function to create the table elemnt***************
			$temp1 = $temp1 +1;
			if ($temp1 == $max_file_p_pa_h) {
				echo "</tr>\n"; 
				$temp1 = 0;
				$temp2 = $temp2 +1;
				}
			}
			if ($temp2 == $max_file_p_pa_v) {break;}
		}
	echo "</table>"; 
	//rigth navigation arrows
	echo "</td><td valign=middle align=rigth>";
	if ($page < $pages ) {echo "<a href=\"index.php?page=$next\"><img src=http://$base_url/.css/rigtharrow.png  height=$max2_y width=50 ></a>";}
	echo "</td></tr></table>";

	if ($page > 1) { echo "<a href=\"index.php?page=$prev\"> &lt;&lt; Prev</a>";} 
	if ($page > 1 && $page < $pages ) {echo " / ";}
	$next = $page + 1 ;
	if ($page < $pages ) {echo "<a href=\"index.php?page=$next\">Next &gt;&gt;</a>";}
	echo "<br>";
	  for ($i = 1 ; $i <= $pages ; $i++ ){
	  if ($i == $page) {echo "<b>$i</b>";}
	  if ($i <> $page) {echo "<a href=\"index.php?page=$i\"> $i </a>";}
  	  if ($i == $page) {echo "</b>";}
  	  if ($i < $pages){echo " - ";}
	  }	  
	return ;
	}


//This function builds a page with a single screenshot of a photo or video or ...
function buildpage ($arg) {
 	global $videos, $images, $base_url , $max2_x, $max2_y , $files3, $files3_n , $audios, $jw_videos, $jw_audios, $videocounter;
 	directory_navigation_links() ;	// it's "/Home/.../..."
 	echo "<h2>$arg</h2>"; 	//It's page title
	//now let's analyze image
 	$system = pathinfo($arg);
 	$ext = $system['extension']; //we get file extension
	$ext = strtolower($ext); //we trasnform extension in lower letters
	//navigation links operation
	$temp = array_search( $arg, $files3) ;	
	$prev = $temp - 1 ; //prev array item, not page!
	$page = $temp + 1 ; //actual page
	$next = $temp + 1 ; //next array item, not page!
	$pages = count($files3);
	$last = $pages - 1; //last array item
	echo "<br>"	;
	    //left navigation with arrows
	echo "<table width=100% ><tr><td valign=middle align=left >";
	if ($page > 1) { echo "<a href=\"index.php?img=$files3[$prev]\"><img src=http://$base_url/.css/leftarrow.png  height=$max2_y width=50 ></a>";} 
	echo "</td><td align=center >";


	//end of navigation links operation
	
	//check if it's a image
	foreach ($images as $value2) {
		if ($ext == $value2 ) {
	 		//check if small image exists, or otherwise create it
			if (!(file_exists(".img/{$max2_x}x{$max2_y}_{$arg}"))) {
				$newname = ".img/{$max2_x}x{$max2_y}_{$arg}";
				createthumb($arg,$newname,$max2_x,$max2_y);
				} 	
			echo "<a href=\"$arg\"><img class=image2 src=\".img/{$max2_x}x{$max2_y}_{$arg}\"  ></a><br><i><small>(click on image to see real size)</small></i>"	;
			//GPS
			$geopos = read_gps_location($file) ;
		echo "<br>Position: " . $geopos['lat'] . "," . $geopos['lng'] ;

			}
		}
	foreach ($videos as $value2) {
		if ($ext == $value2 ) {	
		 	//It's a video
		 	//Let's check if it's supported by JWplayer		 	
		 	if (in_array($ext, $jw_videos)) {
		 		$videocounter = $videocounter + 1 ;
			 	echo "
			 	<div id=\"container$videocounter\" >Carico il lettore ...</div>
				<script type=\"text/javascript\">
				jwplayer(\"container$videocounter\").setup({				
				autostart: true,
				file: \"$arg\",
				height: $max2_y ,
				width: $max2_x
				});
				</script>" ;
				} else { //else we'll use HTML 5
				echo "<a href=\"$arg\"><video src=\"$arg\" width=$max2_x heigth=$max2_y controls=\"controls\" > $arg <br><small>(your browser does not support the video tag)</small></audio></a>";	
				}
			}	
		}
		foreach ($audios as $value2) {
		if ($ext == $value2 ) {
		 	//It'a a audio
			//Let's check if it's supported by JWplayer				
		 	if (in_array($ext, $jw_videos)) {
		 			$videocounter = $videocounter + 1 ;
			 	echo "
			 	<div id=\"container$videocounter\" >Carico il lettore ...</div>
				<script type=\"text/javascript\">
				jwplayer(\"container$videocounter\").setup({				
				autostart: true,
				file: \"$arg\",
				height: $max2_y ,
				width: $max2_x
				});
				</script>" ;
				} else { //else we'll use HTML 5
		 			echo "<a href=\"$arg\"><audio src=\"$arg\" width=$max2_x  controls=\"controls\" > $arg <br><small>(your browser does not support the audio tag)</small></audio></a>";
		 			}
			} 
		}		
	//***
	//right navigation
	echo "</td><td alig=right >";
	if ($page < $pages ) {echo "<a href=\"index.php?img=$files3[$next]\"><img src=http://$base_url/.css/rigtharrow.png  height=$max2_y width=50 ></a>";}	
	echo "</td></tr></table><br>";
	
	if ($page > 1) { echo "<a href=\"index.php?img=$files3[$prev]\">&lt;&lt; Prev</a>";} 
	if ($page > 1 && $page < $pages ) {echo " / ";}
	if ($page < $pages ) {echo "<a href=\"index.php?img=$files3[$next]\">Next &gt;&gt;</a>";}
	echo "<br>";
	  for ($i = 1 ; $i <= $pages ; $i++ ){
	  if ($i == $page) {echo "<b>$i</b>";}
	  $i2 = $i - 1 ; //because arrays start from 0
	  if ($i <> $page) {echo "<a href=\"index.php?img=$files3[$i2]\"> $i </a>";}
  	  if ($i == $page) {echo "</b>";}
  	  if ($i < $pages){echo " - ";}
	  }	  
	return ;
	}


//Now we write the page ()and the navigation link on the bottom of the page):
//no get: standard table page
if ( empty($_GET['page']) && empty($_GET['img']) ) { 
	$page = 1;
	buildtable()  ;}  
//Get with page: table at that page	
if ($_GET['page']) { 
	$page = $_GET['page']; 
	buildtable() ;
	} 
//Get with image: image gallery 
if ($_GET['img']) { 
 	$image = $_GET['img'] ;
	buildpage($image) ;
	} 
	

?>
<br><br>
<small><i>Powered by <a href="http://angerangel.github.io/uffizi/">Uffizi web gallery</a>
<?php echo " $version"; 
if (!(isset($uffizzi[user])) && !(isset($_GET[Logon])) )  {
	echo " - <a href=\"{$_SERVER['php_self']}?Logon=yes";
	if (isset($_GET[img])) {echo "&img=$img";}
	if (isset($_GET[page])) {echo "&page=$page";}
	echo "\">Login</a><br>"	;
	}
?>
<br>
<?php 
echo "<a href=http://www.anybrowser.org/campaign/ > <img src=http://$base_url/.css/biga.gif ></a>";
?>
</div>
</div>
</div>
</body>
</html>
