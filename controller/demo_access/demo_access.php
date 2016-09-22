<?php
$systemDemoStart="";
if(!empty($_COOKIE["systemDemoStart"])&&$_COOKIE["systemDemoStart"]<>""){
	$systemDemoStart="yes";
}
if($systemDemoStart=="yes"){ ?>
<div class="col-md-3 col-sm-6" style="position:fixed;bottom:0px;left:0px;z-index:9999;background-color:#FFF;">
	<div class="widget-body text-center">
		<p class="text-muted m-b-lg">You are viewing your website as demo</p>
		<a href="/controller/index.php?module=startdemo&data=no" class="btn p-v-xl btn-default">Logout Demo View</a>
	</div>
</div><!-- END column -->
<?php }?>