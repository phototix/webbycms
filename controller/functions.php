<?php
function pageJson($page, $filter=""){
	if($filter==""){ $filter="Title"; }
    $str_data = file_get_contents("pages/".$page."/page.json");
    $obj = json_decode($str_data);
    return $obj->{$filter};
}
?>