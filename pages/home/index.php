<?php
// To show this page as a mock home page.
// And PHP strings can be echo using <?=$string
?>
<header>
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	    <div class="container">
	        <div class="navbar-header">
	            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
	                <span class="sr-only">Toggle navigation</span>
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	            </button>
	            <a class="navbar-brand" href="index.html">Starway Travel</a>
	        </div>

	        <!-- Collect the nav links, forms, and other content for toggling -->
	        <div class="collapse navbar-collapse navbar-ex1-collapse">

	            <ul class="nav navbar-nav">
	                <li><a href="index.html">Home</a>
	                </li>                    
	                <li><a href="#about">Desinations</a>
	                </li>
	                <li><a href="#services">Gallery</a>
	                </li>
	                <li><a href="#contact">Contact Us</a>
	                </li>
	            </ul>
	        </div>
	        <!-- /.navbar-collapse -->
	    </div>
	    <!-- /.container -->
	</nav>
</header>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1>Welcome to Starway Travel!</h1>
            <p>Come travel with us.</p>
            <p><?=initWebbyCMS()?></p>
            <img src= "https://s3-ap-southeast-1.amazonaws.com/misc-webby/panel-assets/02ebc67d76e30c75ba036ac11e7148ee.png" class="img-responsive img-rounded center-block" alt="World">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
          <h2>Form Post handling sample</h2>
          <!-- Basic form structure for webbycms, no action needed. post in method is mandate -->
          <form method="post">
          	<!-- First input hidden and name as form, value as the file in your 'forms' folder, check /pages/home/forms/sample.php -->
          	<input type="hidden" name="form" value="sample">
          	<label for="sample_input">
          		<div>Test Send</div>
          		<input type="text" class="form-control" name="send_something">
          		<!-- When this button is click, the form contents will be post to the file in folder forms. -->
          		<button class="btn btn-default">Submit</button>
          	</label>
          </form>
      </div>
    </div><!--/row-->

    <div class="row">
        <div class="col-md-4">
          <h2>Heading A</h2>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn btn-default" href="#" role="button">View details »</a></p>
        </div><!--/span-->

        <div class="col-md-4">
          <h2>Heading B</h2>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn btn-default" href="#" role="button">View details »</a></p>
        </div><!--/span-->

        <div class="col-md-4">
          <h2>Heading C</h2>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn btn-default" href="#" role="button">View details »</a></p>
        </div><!--/span-->
    </div><!--/row-->
</div>