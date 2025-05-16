<?php
session_start();
include 'db_ec.php'; // Database connection

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            if ($user['user_status'] == 0) {
                $error = "Your account is pending admin approval. You will be notified via email once it's approved.";
            } elseif ($user['user_status'] == 1) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] == 1) {
                    header("Location: admin.php");
                    exit();
                } elseif ($user['role'] == 2) {
                    header("Location: seller.php");
                    exit();
                } else {
                    $error = "Unauthorized role.";
                }
            } else {
                $error = "Unknown account status.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <title>User Login</title>
    <style>
        .error-message {
            background: linear-gradient(-45deg, #66BB6A, #388E3C, #2C6B3F);

    padding: 10px;
    border-radius: 8px;
    color: white;
    display: flex;
    font-weight: bold;
    margin-bottom: 15px;
    animation: gradientAnimation 5s ease infinite;
    transition: all 0.3s ease;
}
        body {
            background: linear-gradient(-45deg, #2C6B3F, #388E3C, #66BB6A);
            background-size: 400% 400%;
            animation: gradientAnimation 20s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            width: 100%;
            max-width: 330px;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            position: relative;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            padding: 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 100%;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: white;
            margin-bottom: 5px;
            align-self: flex-start;
        }

        .form-group input {
            width: 300px;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.3);
            font-size: 16px;
            color: white;
            outline: none;
            transition: 0.3s ease-in-out;
            text-align: center;
        }

        .form-group input:focus {
            background: rgba(255, 255, 255, 0.4);
        }

        .btn-primary {
            background: linear-gradient(-45deg, #2C6B3F, #388E3C, #66BB6A);

            color: white;
            border: none;
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            background: linear-gradient(-45deg, #2C6B3F, #388E3C, #66BB6A);

            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        .btn-primary:active {
            transform: scale(0.97);
        }


        .logo {
    width: 100px;
    margin-bottom: 15px;
    border-radius: 50%; /* Optional: makes it circular if your logo is square */
    background: white; /* Makes the logo stand out */
    padding: 10px; /* Adds space inside the logo */
    box-shadow: 0 8px 16px rgba(0, 100, 0, 0.3); /* Subtle green glow */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.logo:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 24px rgba(76, 175, 80, 0.6); /* Brighter green glow on hover */
}


    
    </style>

<style>
    .signup-link {
        margin-top: 15px;
        color: white; /* Change the text color to white */
        font-size: 14px;
    }

    .signup-link a {
    color:rgb(202, 234, 203); /* Change the link color torgb(143, 249, 148) */
    text-decoration: none;
    font-weight: bold;
}

.signup-link a:hover {
    text-decoration: underline;
}
</style>

</head>
<body>

<div class="login-container">
        <div class="login-card">
            <img src="assets/img/logo.png" alt="Logo" class="logo">
            <h2 style="color: white; width: 100%;">Project Hiraya</h2><br>
            <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-primary">Login</button>
            </form>
            <div class="signup-link">
                <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
            </div>
        </div>
    </div>
</body>
</html>