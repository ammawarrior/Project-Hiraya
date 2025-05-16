<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en-gb" dir="ltr">


<!--   19 Nov 2019 03:38:47 GMT -->
<!--  --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- / -->
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hiraya | Welcome</title>
  <link rel="shortcut icon" type="image/png" href="img/logo1.png" >
  <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,600,700&amp;display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/main.css" />
  <script src="js/uikit.js"></script>
</head>


<body class="uk-background-body">
<header id="header">
	<div data-uk-sticky="animation: uk-animation-slide-top; sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky; cls-inactive: uk-navbar-transparent ; top: #header">
	  <nav class="uk-navbar-container uk-letter-spacing-small uk-text-bold">
	    <div class="uk-container">
	      <div class="uk-position-z-index" data-uk-navbar>
	        <div class="uk-navbar-left">
            <img src="img/logo1.png" alt="Hiraya Logo" width="50" height="50">
	          <a class="uk-navbar-item uk-logo" href="index.php" style="color: #27bf43;">Hiraya</a>
	        </div>

          <!-- Navigation Header -->
	        <div class="uk-navbar-right">
	          <ul class="uk-navbar-nav uk-visible@m" data-uk-scrollspy-nav="closest: li; scroll: true; offset: 80">
	            <li class="uk-active"><a href="index.php">Home</a></li>
	            <li ><a href="about.php">About</a></li>
				<li ><a href="courses.php">Courses</a></li>
	          </ul>
	          <div>
	            <a class="uk-navbar-toggle" data-uk-search-icon href="#"></a>
	            <div class="uk-drop uk-background-default" data-uk-drop="mode: click; pos: left-center; offset: 0">
	              <form class="uk-search uk-search-navbar uk-width-1-1">
	                <input class="uk-search-input uk-text-demi-bold" type="search" placeholder="Search..." autofocus>
	              </form>
	            </div>
	          </div>
	          <?php if(isset($_SESSION['username'])): ?>
            <div class="uk-navbar-item">
              <div class="uk-flex uk-flex-middle">
                <span class="uk-text-bold uk-text-success">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a class="uk-button uk-button-primary-light uk-margin-small-left" href="logoutclient.php">Logout</a>
              </div>
            </div>
            <?php else: ?>
            <div class="uk-navbar-item">
              <div><a class="uk-button uk-button-primary-light" href="login.php">Sign In</a></div>
            </div>
            <?php endif; ?>
            <a class="uk-navbar-toggle uk-hidden@m" href="#offcanvas" data-uk-toggle><span
              data-uk-navbar-toggle-icon></span></a>
	        </div>
	      </div>
	    </div>
        </nav>
        </div>