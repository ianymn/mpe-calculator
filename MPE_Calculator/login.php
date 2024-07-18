<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['uname']) && isset($_POST['password'])) {

        function validate($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $uname = validate($_POST['uname']);
        $pass = validate($_POST['password']);

        if (empty($uname)) {
            header("Location: login.php?error=UserName is required");
            exit();
        } else if (empty($pass)) {
            header("Location: login.php?error=Password is required");
            exit();
        } else {
            $storedUsername = 'admin';
            //$storedPasswordHash = '$2y$10$X3rdjRIGKQ6/nZH5Evm6Z.z7GE211cLDjl4NUwmOCFKAy88ggjq36'; // Hash of 'admin1234'
            $storedPasswordHash = 'admin1234';
            //if ($uname === $storedUsername && password_verify($pass, $storedPasswordHash)) { //hashing not in PHP versions older than 5.5.0
            if($uname === $storedUsername && $pass === $storedPasswordHash){
                $_SESSION['username'] = $uname;
                echo "Login successful. Redirecting to admin.php...";
                header("Location: admin.php");
                exit();
            } else {
                header("Location: login.php?error=Wrong credentials");
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>    
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        input {
            display: block;
            border: 2px solid #ccc;
            width: 95%;
            padding: 10px;
            margin: 10px auto;
            border-radius: 5px;
        }
        label {
            color: #888;
            font-size: 18px;
            padding: 10px;
        }
        button {
            float: right;
            background: #007BFF;
            padding: 10px 15px;
            color: #fff;
            border-radius: 5px;
            margin-right: 10px;
            border: none;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            background: #F2DEDE;
            color: #A94442;
            padding: 10px;
            width: 95%;
            border-radius: 5px;
            margin: 20px auto;
        }
    </style>
</head>
<body>
    <div id="login-container"> 
        <form id="login-form" action="login.php" method="post">
            <h2 id="login-label">LOGIN</h2>
            <?php if(isset($_GET['error'])) { ?>
                <p class="error"><?php echo $_GET['error']; ?></p>
            <?php } ?>
            <label>User Name</label>
            <input type="text" name="uname" placeholder="User Name"><br>

            <label>Password</label>
            <input type="password" name="password" placeholder="Password"><br>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>