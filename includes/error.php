<?php if($_SESSION["systemError"]<>""){ ?>
    <div class="alert alert-warning alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h4><i class="icon fa fa-warning"></i> Alert!</h4>
      <?=$_SESSION["systemError"]?>
    </div>
<?php $_SESSION["systemError"]=""; } ?>

<?php if($_SESSION["systemSucces"]<>""){ ?>
    <div class="alert alert-success alert-dismissible">
    	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h4><i class="icon fa fa-check"></i> Success!</h4>
      <?=$_SESSION["systemSucces"]?>
    </div>
<?php $_SESSION["systemSucces"]=""; } ?>
