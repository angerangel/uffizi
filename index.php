<?php 
error_reporting(E_ALL & ~E_DEPRECATED);  
ini_set('display_errors',0);  

//****Configuration*****
$max_file_p_pa_h = 4 ; //max files per pag hor
$max_file_p_pa_v = 4 ; //max files per pag ver
$max_file_pa = $max_file_p_pa_v * $max_file_p_pa_h ; //max files per page
$base_url = 'www.maxvessi.net/uffizi/pictures' ; //you must put your main folder without http://
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
$version = "4.17"; 
$jw_videos = array("mp4", "webm", "ogv", "flv", "mov", "f4v", "3gp", "3g2"); //videos readed by JWPlayer
$jw_audios = array("aac", "m4am", "ogg", "mp3");  //audios readed by JWPLayer

//***************HEADER**************************
?>
<html>
<head>
<link rel="stylesheet" type="text/css" <?php echo "href=http://$base_url/.css/mystyle.css />"; ?>
<?php echo "<script type=\"text/javascript\" src=\"http://$base_url/.jwplayer/jwplayer.js\"></script>"; ?>
</head>
<body>
<DIV ALIGN="CENTER">
<div id=topleft > 
<div id=bottomright >
<h1>UFFIZI <i>web gallery</i></h1>


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
 	
 if (isset($user) && ($password == $passwords[$user]) ) {
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


if (!(file_exists('.img/'))) {mkdir('.img/');}

$files1 = scandir('.',1); //lista dei file attuali in ordine inverso

//removing not video, not image, not audio, hidden files from file list
$all_exts = array_merge($videos, $images, $audios); //all extensions
$temp = 0;
foreach	($files1 as $value) {
 	$test = false ;
 	if (is_dir($value)) {$test = true ;} else {  //we show directories
		$ext = end(explode('.', $value)); //we get file extension
		$ext = strtolower($ext); //we trasnform extension in lower letters
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


//krsort($files_temp);




$files1 = array_values($files_temp);

//Creating list of only images, without folders
$temp = 0;
$files3 = $files1 ; 
foreach	($files3 as $value) {
 	if (is_dir($value)) {	unset($files3[$temp]);}
 	$temp = $temp + 1;
 		}
$files3 = array_values($files3) ;

//Useful function to remove unwanted charachters, for example ""underscore":
function removeunder ($arg) {
 	$unwantedchars = array("_", "-");
	$tag = str_replace($unwantedchars , " ", "$arg");
	return $tag;
}

//let's create a thumb for this folder, if it contains images
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


//Preview functions:
//resizing function
function createthumb ($name,$filename,$new_w,$new_h){
	$system = pathinfo($name);
	if (preg_match('/jpg|jpeg/i',$system['extension'])) { $src_img=imagecreatefromjpeg($name); }
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
	imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
	$system = pathinfo($filename);
	if (preg_match("/png/i",$system['extension'])) { imagepng($dst_img,$filename); }		
	if (preg_match("/jpg|jpeg/i",$system['extension'])) { imagejpeg($dst_img,$filename); }
	if (preg_match("/gif/i",$system['extension'])) { imagegif($dst_img,$filename); }	
	imagedestroy($dst_img); 
	imagedestroy($src_img); 	
	return ;
	}

//make_shot function crate all the previews (folders or images)
function make_shot ($arg ) {
 	global $images , $videos , $max1_x , $max1_y , $audios , $jw_videos , $jw_audios, $base_url ;
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
		 	//Control if it's supported by JWPlayer
		 	if (in_array($ext, $jw_videos)) {
				echo "<td align=center valign=middle width=$max1_x >
					<a href=\"{$_SERVER['PHP_SELF']}?img=$arg\" > 
				 	<div id=\"container\" >Carico il lettore ...</div>
					<script type=\"text/javascript\">
					jwplayer(\"container\").setup({
					flashplayer: \"http://$base_url/.jwplayer/player.swf\",
					controlbar: \"none\" ,
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
				echo "<td align=center valign=middle width=$max1_x >
					<a href=\"{$_SERVER['PHP_SELF']}?img=$arg\" > 
				 	<div id=\"container\" >Carico il lettore ...</div>
					<script type=\"text/javascript\">
					jwplayer(\"container\").setup({
					flashplayer: \"http://$base_url/.jwplayer/player.swf\",
					controlbar: \"none\" ,
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

//building navigation
function directories()   {         
         $directory = $_SERVER["PHP_SELF"]; //esempio /temp/prova3.php
	     $directories = array();
         $next_slash = 0;
         do {//Creates an array with all the parent folders
             //of the file where this function is called.
            $next_slash = strpos($directory, "/", $next_slash); //trova la prima / = 0 poi 5
            if($next_slash !== false)
              {
              $next_slash++;
              $directories[count($directories)] = substr($directory, 0, $next_slash); // prina / poi /temp/
              }
            } while($next_slash !== false);
	     return($directories);
         }

//Uses the directories() function to create the navigation links. 
function directory_navigation_links()         {
  	 		global $base_url ;
  	 		$index = substr_count($base_url, '/');
			$temp = substr_count($base_url, '/');
         $directories = directories();
         $links = "";
         for($index ; $index < count($directories); $index++)
            {
            //Handling the display is a little harder than the links themselves.
            $dir_name = substr($directories[$index], 0, strlen($directories[$index]) - 1);
            $dir_name = substr($dir_name, strrpos($dir_name, "/") + 1, strlen($dir_name) - 1);
            if($index == $temp) {              $links .= "<a href=\"" . $directories[$index] . "\">Home</a>  / ";} else {            
            	$links .= "<a href=\"" . $directories[$index] . "\">" . $dir_name . "</a> / ";
				}
            }
         echo "<i>$links</i>" ;
         }


//This function create the table with image of directories and single images/videos
function buildtable () {
	global $page, $max_file_pa, $files1, $max_file_p_pa_h, $max_file_p_pa_v, $pages ;
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
	$prev = $page - 1 ;	
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


//This function builds pages with single screenshot of phots, videos, etc.
function buildpage ($arg) {
 	global $videos, $images, $base_url , $max2_x, $max2_y , $files3, $files3_n , $audios, $jw_videos, $jw_audios;
 	directory_navigation_links() ;	
 	echo "<h2>$arg</h2>"; 	
 	$system = pathinfo($arg);
 	$ext = $system['extension']; //we get file extension
	$ext = strtolower($ext); //we trasnform extension in lower letters
	//check if it's a image
	foreach ($images as $value2) {
		if ($ext == $value2 ) {
	 		//check if small image exists, or otherwise create it
			if (!(file_exists(".img/{$max2_x}x{$max2_y}_{$arg}"))) {
				$newname = ".img/{$max2_x}x{$max2_y}_{$arg}";
				createthumb($arg,$newname,$max2_x,$max2_y);
				} 	
			echo "<br><a href=\"$arg\"><img class=image2 src=\".img/{$max2_x}x{$max2_y}_{$arg}\"></a><br>"	;
			}
		}
	foreach ($videos as $value2) {
		if ($ext == $value2 ) {	
		 	//It's a video
		 	//Let's check if it's supported by JWplayer		 	
		 	if (in_array($ext, $jw_videos)) {
			 	echo "
			 	<div id=\"container\" >Carico il lettore ...</div>
				<script type=\"text/javascript\">
				jwplayer(\"container\").setup({
				flashplayer: \"http://$base_url/.jwplayer/player.swf\",
				autostart: true,
				file: \"$arg\",
				height: $max2_y ,
				width: $max2_x
				});
				</script>" ;
				} else { //else we'll use HTML 5
				echo "<a href=\"$arg\"><video src=\"$arg\" width=$max2_x heigth=$max2_y controls=\"controls\" > $arg <br><small>(your browser does not support the video tag)</small></audio></a><br>";	
				}
			}	
		}
		foreach ($audios as $value2) {
		if ($ext == $value2 ) {
		 	//It'a a audio
			//Let's check if it's supported by JWplayer				
		 	if (in_array($ext, $jw_videos)) {
			 	echo "
			 	<div id=\"container\" >Carico il lettore ...</div>
				<script type=\"text/javascript\">
				jwplayer(\"container\").setup({
				flashplayer: \"http://$base_url/.jwplayer/player.swf\",
				autostart: true,
				file: \"$arg\",
				height: $max2_y ,
				width: $max2_x
				});
				</script>" ;
				} else { //else we'll use HTML 5
		 			echo "<a href=\"$arg\"><audio src=\"$arg\" width=$max2_x  controls=\"controls\" > $arg <br><small>(your browser does not support the audio tag)</small></audio></a><br>";
		 			}
			} 
		}		
	//***	
	//krsort($files3) ;
	$temp = array_search( $arg, $files3) ;	
	$prev = $temp - 1 ; //prev array item, not page!
	$page = $temp + 1 ; //actual page
	$next = $temp + 1 ; //next array item, not page!
	$pages = count($files3);
	$last = $pages - 1; //last array item	
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


//Now we write the navigation link on the bottom of the page
if ( empty($_GET['page']) && empty($_GET['img']) ) { 
	$page = 1;
	buildtable()  ;}  //no get: standard table page
if ($_GET['page']) { 
	$page = $_GET['page']; 
	buildtable() ;
	} //Get with page: table at that page
if ($_GET['img']) { 
 	$image = $_GET['img'] ;
	buildpage($image) ;
	} //Get with image: image gallery 
	

?>
<br><br>
<small><i>Powered by <a href="http://www.maxvessi.net/pmwiki/pmwiki.php?n=Main.UffiziWebGallery">Uffizi web gallery</a>
<?php echo " $version"; 
if (!(isset($uffizzi[user])) && !(isset($_GET[Logon])) )  {
	echo " - <a href=\"{$_SERVER['php_self']}?Logon=yes";
	if (isset($_GET[img])) {echo "&img=$img";}
	if (isset($_GET[page])) {echo "&page=$page";}
	echo "\">Login</a><br>"	;
	}
?>
<br>
Best viewed with <a href="http://www.google.com/chrome" target="_blank" >Google Chrome</a></i></small>
</div></div></div>
</body>
</html>