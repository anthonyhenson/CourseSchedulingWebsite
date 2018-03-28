
<?php
	require_once 'css/cssVersion.php';
	require_once 'session.php';
	require_once 'lp/SQLDataHandler.php';

	
	//<!-- ------------------------- LOGIN ----------------------------------------- -->
	if (isset($_POST['login'])) {
			$dataHandler = new SQLDataHandler();
			$userEmail = filter_input(INPUT_POST, 'emailInput', FILTER_VALIDATE_EMAIL);
	    $passwordInput = filter_input(INPUT_POST, 'passwordInput');
	    $passwordEncr = sha1($userEmail . $passwordInput); //hash password with salt
	    
	    $passwordServer = $dataHandler->getUserPassword($userEmail);
	    
	    $passwordEncr = "password";
	    $passwordServer = "password";
	    
	    if ($userEmail != NULL && $passwordInput != NULL && $passwordEncr == $passwordServer) { 
	    	$_SESSION['user'] = $userEmail; ?>
	    	<script>
	        	window.location.assign('home.php');
	        </script> <?php
	    }
	    else { ?>
	    	<script>
	        	alert('Email or password is incorrect. Please try again.');
	           window.location.load(''); //this page
	        </script> <?php
	    }
	}
	

?>
<!-- ------------------------- HEAD ----------------------------------------- -->
<!DOCTYPE html>
<html lang= "en-us">
	<link href="../css/login.css"  rel="stylesheet" type="text/css" />
	<link href="../css/main.css"  rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	
	<body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  	<div class="center blue" style="margin-top:75px"><h1>Course Scheduling</h1></div>

		<div class="container">
    
  	<div class="row" id="pwd-container">
   	 	<div class="col-md-4"></div>
    
    	<div class="col-md-4">
      <section class="login-form">
        
        <form method="post" action="login.php" role="login">
          <img src="./img/cbu_logo_login.jpg" class="img-responsive" alt="" width="140" height="90" />
          <input type="username" name="emailInput" placeholder="Username" required class="form-control input-lg" value="" />
          
          
          <input type="password" class="form-control input-lg" name="passwordInput" id="password" placeholder="Password" required="" />
          
          
          <div class="pwstrength_viewport_progress"></div>
          
          
          <button type="submit" name="login" id="logIn" class="btn btn-lg btn-default btn-block">Sign in</button>
          <span class="reset_pass" id="forgot_password"><a href="">Forgot Password?</a></span>
          
        </form>
      </section>  
      </div>
      
      <div class="col-md-4"></div>
      

  </div>
  
</div>
</body>