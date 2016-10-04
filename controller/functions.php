<?php
$systemWebLaunch=0;$isBackEnd="false";
if($systemDemo=="yes"){
  if(preg_match("/\badmincp\b/i", $_SERVER["REQUEST_URI"], $match)){ $isBackEnd="true"; }else{
    include("demo_access/demo_access.php"); 
  }
  $systemWebLaunch=getSystem("web_launch");
}
/* Get SQL dataset functions */
function getTable($tableset, $option)
{
	include("conn.php");
    $result = mysqli_query($con, "SELECT * FROM ".$tableset." ".$option);
    
    $table = array();
    
    $i = 0;
    while($table[$i] = mysqli_fetch_assoc($result)) {
        $i++;
    }
    unset($table[$i]);
    return $table;
}

function getSystem($option="", $adminid=""){
	if($option==""){
		$output="";
	}else{
		include("conn.php");

		$adminSQL="admin_id='1'";
		if($adminid<>""){
			$adminSQL="admin_id='$adminid'";
		}

		$result=mysqli_query($con, "SELECT * FROM db_admin WHERE ".$adminSQL);
		while($row=mysqli_fetch_array($result)){
			if($option==""){
				$output=$row["website_title"];
			}elseif($option=="visitor_count_new_today"){
				$result2=mysqli_query($con, "SELECT * from db_visitor_log WHERE (log_stamp_date='0000-00-00' AND log_date='$Today') OR (log_stamp_date='$Today' AND log_date='$Today')");
				$output=mysqli_num_rows($result2);
			}elseif($option=="visitor_count_return_today"){
				$result2=mysqli_query($con, "SELECT * from db_visitor_log WHERE log_stamp_date='$Today' AND log_date<>'$Today'");
				$output=mysqli_num_rows($result2);
			}elseif($option=="visitor_count_today"){
				$result2=mysqli_query($con, "SELECT * from db_visitor_log WHERE log_date='$Today' OR log_stamp_date='$Today'");
				$output=mysqli_num_rows($result2);
			}elseif($option=="visitor_count_total"){
				$result2=mysqli_query($con, "SELECT * from db_visitor_log");
				$output=mysqli_num_rows($result2);
			}elseif($option=="founder_cover"){
				$output="https://placeholdit.imgix.net/~text?txtsize=33&txt=512%C3%97512&w=512&h=512";
				if($row["founder_cover"]<>""){
					$output = $row["founder_cover"];
				}
			}else{
				$output=$row[$option];
			}
		}
	}
	return $output;
}

function inputToken($inputToken=""){
	$output=false;
	if($inputToken<>""){
		include("conn.php");
		$result=mysqli_query($con, "INSERT INTO db_token (token_date, token_time, token_code) VALUES ('$Today', '$Time', '$inputToken')");
		$getID=mysqli_insert_id($con);
		if($getID>0){
			$output=true;
		}else{
			$output=false;
		}
	}
	return $output;
}

function getToken($inputToken=""){
	include("conn.php");
	$output=false;
	if($inputToken<>""){
		$result=mysqli_query($con, "SELECT * FROM db_token WHERE token_code='$inputToken' AND token_stat=''");
		if(mysqli_num_rows($result)==0){
			$output=true;
		}else{
			$output=false;
		}
	}
	return $output;
}

function getDataTable($tableset="", $columnName="", $inputData="", $filter=""){
	include("conn.php");
	$output = false;	
    if($columnName<>""&&$tableset<>""&&$filter<>""&&$inputData<>""){
    	$dataSQL=mysqli_query($con, "SELECT * FROM ".$tableset);
    	if($dataSQL){
	    	$result=mysqli_query($con, "SELECT ".$filter." FROM ".$tableset." WHERE ".$columnName."='".$inputData."'");
	    	if (!$result) {
	    		$output=mysqli_error($con);
			}else{
		    	while($row=mysqli_fetch_array($result)){
		    		$output=$row[$filter];
		    	}
	    	}
    	}else{
    		$output="No data table.";
    	}
	}
    return $output;
}

function updateTable($tableset="", $columnName="", $inputData="", $filter="", $option="", $setting=false){
	include("conn.php");
	$result = false;	
    if($columnName<>""&&$tableset<>""&&$filter<>""&&$option<>""){
    	
    	if($setting==true){
    		$result = mysqli_query($con, "UPDATE ".$tableset." SET ".$columnName."='".$inputData."' WHERE ".$filter."='".$option."'");
    	}
    	if($setting==false&&$inputData<>""){
    		$result = mysqli_query($con, "UPDATE ".$tableset." SET ".$columnName."='".$inputData."' WHERE ".$filter."='".$option."'");
    	}

    	$result = true;
	}else{
		$result = false;	
	}
    return $result;
}

function makeURL($str, $option=''){
	if($str<>""){
		$str=preg_replace("/[^A-Za-z0-9 ]/", '', $str);
		$str=str_replace(array("[","]","{","}","(",")",";","<",">","?","'",'"',":","!","@","#","$","%","^","*","&", "&#39;", "00390"), "", str_replace(array(" ", ",", ".", "/"),"", $str));;
		if($option==""||$option==0){ $htmlend=""; }else{ $htmlend=".html"; }
		return $str.$htmlend;
	}
}

function getPageWithSlug($inputSlug="", $option=""){
	include("conn.php");
	$output="";
	if($inputSlug<>""){
		$result=mysqli_query($con, "SELECT * FROM db_pages WHERE page_slug='$inputSlug'");
		while($row=mysqli_fetch_array($result)){
			if($option==""){
				$output=$row["page_title"];
			}else{
				$output=$row[$option];
			}
		}
	}
	if($option=="backend_menu_name"&&$output==""){ $output=ucfirst($inputSlug); }
	return $output;
}

function getPageWithId($inputId="", $option=""){
	include("conn.php");
	$output="";
	if($inputId<>""){
		$result=mysqli_query($con, "SELECT * FROM db_pages WHERE page_id='$inputId'");
		while($row=mysqli_fetch_array($result)){
			if($option==""){
				$output=$row["page_title"];
			}else{
				$output=$row[$option];
			}
		}
	}
	return $output;
}

function newVisitor($cookieToken=""){
	$output=false;
	if($cookieToken<>""){
		include("conn.php");
		$result=mysqli_query($con, "INSERT INTO db_visitor_log (log_date, log_time, log_unique) VALUES ('$Today', '$Time', '$cookieToken')");
		$getID=mysqli_insert_id($con);
		if($getID>0){
			$output=true;
		}else{
			$output=false;
		}
	}
	return $output;
}

function getImage($imageId, $filter=""){
	include("conn.php");
	$output="";
	if($imageId<>""&&is_numeric($imageId)){
		$result=mysqli_query($con, "SELECT * FROM db_gallery WHERE image_id='$imageId'");
		while($row=mysqli_fetch_array($result)){
			if($filter==""){
				$output=$row["image_name"];
			}else{
				$output=$row[$filter];
			}
		}
	}
	return $output;
}

function updateVisitor($cookieToken=""){
	$output=false;
	if($cookieToken<>""){
		include("conn.php");
		$getCurrentCount=getDataTable("db_visitor_log", "log_unique", $cookieToken, "log_count");
		$getDayCount=getDataTable("db_visitor_log", "log_unique", $cookieToken, "log_day_count");
		$getDayDate=getDataTable("db_visitor_log", "log_unique", $cookieToken, "log_stamp_date");
		if($getDayDate==$Today){
			$getDayCount++;
		}else{
			$getDayCount=1;
		}
		$getCurrentCount++;
		$result=mysqli_query($con, "UPDATE db_visitor_log SET log_stamp_date='".$Today."', log_stamp_time='".$Time."', log_count='".$getCurrentCount."', log_day_count='".$getDayCount."' WHERE log_unique='".$cookieToken."'");
		$output=true;
	}
	return $output;
}
// Photo Resize Functions
class ResizePhoto {
 
   var $image;
   var $image_type;
 
   function load($filename) {
 
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
 
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
 
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
 
         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image,$filename);
      }
      if( $permissions != null) {
 
         chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image);
      }
   }
   function getWidth() {
 
      return imagesx($this->image);
   }
   function getHeight() {
 
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
 
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
 
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
 
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }
 
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
   }      
 
}
?>
<?php
function pageJson($page, $filter=""){
	if($filter==""){ $filter="Title"; }
    $str_data = file_get_contents("pages/".$page."/page.json");
    $obj = json_decode($str_data);
    return $obj->{$filter};
}

$blogSQL=mysqli_query($con, "SELECT * FROM db_blog_post");
$blogModuleBEDir="admincp/modules/pages/blog/index.php";
if($isBackEnd=="true"){
	$blogModuleBEDir="../../admincp/modules/pages/blog/index.php";
}
if(file_exists($blogModuleBEDir)&&$blogSQL){
	function getPost($postID, $filter=""){
		include("conn.php");
		$output="";
		if($postID<>""){
			$result=mysqli_query($con, "SELECT * FROM db_blog_post WHERE post_id='$postID'");
			while($row=mysqli_fetch_array($result)){
				if($filter==""){
					$output=$row["post_title"];
				}elseif($filter=="post_comment_count"){
					$resultComment=getTable("db_blog_post_comments", "WHERE comment_stat<>'3' AND comment_display=1 AND blog_id='".$postID."'");
	                $output=count($resultComment);
				}else{
					$output=$row[$filter];
				}
			}
		}
		return $output;
	}

	function getPostCategory($cateID, $filter=""){
		include("conn.php");
		$output="";
		if($cateID==0){ $output="-n/a-"; }
		if($cateID<>""&&$cateID<>0){
			$result=mysqli_query($con, "SELECT * FROM db_blog_post_category WHERE cate_id='$cateID'");
			while($row=mysqli_fetch_array($result)){
				if($filter==""){
					$output=$row["cate_name"];
				}else{
					$output=$row[$filter];
				}
			}
		}
		return $output;
	}
}

$productSQL=mysqli_query($con, "SELECT * FROM db_products");
$productModuleBEDir="admincp/modules/pages/products/index.php";
if($isBackEnd=="true"){
	$productModuleBEDir="../../admincp/modules/pages/products/index.php";
}
if(file_exists($productModuleBEDir)&&$productSQL){
	function getProduct($postID, $filter=""){
		include("conn.php");
		$output="";
		if($postID<>""){
			$result=mysqli_query($con, "SELECT * FROM db_products WHERE post_id='$postID'");
			while($row=mysqli_fetch_array($result)){
				if($filter==""){
					$output=$row["post_title"];
				}else{
					$output=$row[$filter];
				}
			}
		}
		return $output;
	}

	function getProductCategory($cateID, $filter=""){
		include("conn.php");
		$output="";
		if($cateID==0){ $output="-n/a-"; }
		if($cateID<>""&&$cateID<>0){
			$result=mysqli_query($con, "SELECT * FROM db_products_cate WHERE cate_id='$cateID'");
			while($row=mysqli_fetch_array($result)){
				if($filter==""){
					$output=$row["cate_name"];
				}else{
					$output=$row[$filter];
				}
			}
		}
		return $output;
	}
}
?>