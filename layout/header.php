<?php 
require_once 'config/database.php';
session_start();

// If user is logged in, fetch their information from database
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT username, user_picture FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_picture'] = $user['user_picture'];
        }
    } catch (PDOException $e) {
        error_log("Error fetching user info: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en-gb" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hiraya | Welcome</title>
  <link rel="shortcut icon" type="image/png" href="assets/img/logo.png">
  <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,600,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/main.css" />
  <link rel="stylesheet" href="css/shared-styles.css" />
  <script src="js/uikit.js"></script>
</head>
<body class="uk-background-body">
<header id="header">
  <div data-uk-sticky="animation: uk-animation-slide-top; sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky; cls-inactive: uk-navbar-transparent ; top: #header">
    <nav class="uk-navbar-container uk-letter-spacing-small uk-text-bold">
      <div class="uk-container">
        <div class="uk-position-z-index" data-uk-navbar>
          <div class="uk-navbar-left">
            <img src="assets/img/logo.png" alt="Hiraya Logo" width="50" height="50">
            <a class="uk-navbar-item uk-logo" href="index.php" style="color: #27bf43;">Hiraya</a>
          </div>
          <div class="uk-navbar-right">
            <ul class="uk-navbar-nav uk-visible@m">
              <li class="uk-active"><a href="index.php">Home</a></li>
              <li><a href="about.php">About</a></li>
              <li><a href="courses.php">Courses</a></li>
            </ul>
            <?php if(isset($_SESSION['username'])): ?>
            <div class="uk-navbar-item">
              <div class="uk-flex uk-flex-middle uk-flex-right">
                <div class="uk-flex uk-flex-middle uk-flex-right">
                  <div>
                    <a class="uk-text-bold uk-text-success" href="#" data-uk-toggle="target: #user-dropdown">
                      Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                    </a>

                    <!-- Dropdown Content -->
                    <div id="user-dropdown" class="uk-dropdown uk-dropdown-bottom-right" data-uk-dropdown="mode: click; pos: bottom-right">
                      <ul class="uk-nav uk-dropdown-nav">
                        <li><a href="account-settings.php" class="uk-flex uk-flex-middle">
                            <span class="uk-margin-small-right" uk-icon="icon: settings; ratio: 0.8"></span>
                            Account Settings
                        </a></li>
                        <li><a href="wishlist.php" class="uk-flex uk-flex-middle">
                            <span class="uk-margin-small-right" uk-icon="icon: heart; ratio: 0.8"></span>
                            Wishlist
                        </a></li>
                        <li><a href="logoutclient.php" class="uk-text-danger uk-flex uk-flex-middle">
                            <span class="uk-margin-small-right" uk-icon="icon: sign-out; ratio: 0.8"></span>
                            Logout
                        </a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="uk-margin-small-left">
                    <?php 
                      $user_picture = !empty($_SESSION['user_picture']) ? "uploads/" . $_SESSION['user_picture'] : 'img/profile_null.png';
                      $user_picture = str_replace(['\\', '"'], ['', ''], $user_picture);
                      if (!file_exists($user_picture)) {
                          $user_picture = 'img/profile_null.png';
                      }
                    ?>
                    <img src="<?php echo htmlspecialchars($user_picture); ?>" alt="Profile Picture" class="uk-border-circle" style="width: 32px; height: 32px; object-fit: cover;">
                  </div>
                </div>
              </div>
            </div>
            <?php else: ?>
            <div class="uk-navbar-item">
              <a class="uk-button uk-button-primary-light" href="login.php">Sign In</a>
            </div>
            <?php endif; ?>
            <a class="uk-navbar-toggle uk-hidden@m" href="#offcanvas" data-uk-toggle><span data-uk-navbar-toggle-icon></span></a>
          </div>
        </div>
      </div>
    </nav>
  </div>
</header>
</body>
</html>
