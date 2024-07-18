<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include("readFile.php");

echo "<hr>";

$sepValues = [];
$clickedFile = "";
$limits = [];
$errors = [];
$errorText = "";
$limitText = "";

if (isset($_GET['file']) && !empty($_GET['file'])) {
    $clickedFile = $_GET['file'];
} elseif (isset($_POST['file']) && !empty($_POST['file'])) {
    $clickedFile = $_POST['file'];
}

if (!empty($clickedFile)) {
    //$filePath = '/var/www/html/sysadmin/MPE_Calculator/' . basename($clickedFile);
    $filePath = 'C:/xampp/htdocs/MPE_Calculator/' . basename($clickedFile);

    if (file_exists($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'txt') {
        echo "<h2>" . substr($clickedFile, 0, strlen($clickedFile) - 4) . "</h2>";
        $lines = file($filePath);

        foreach ($lines as $line) {
            $line = trim($line);
            $sepValues[] = explode(',', $line);
        }

        foreach ($sepValues as $values) {
            if (count($values) >= 2) {
                $limits[] = floatval($values[0]);
                $errors[] = implode(',', array_slice($values, 1));
            }
        }
        echo "Errors &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Limits<br>";
    } else if ($clickedFile == 'newProduct') {
    } else {
        echo "File does not exist";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
</head>
<body>
    <form action="admin.php" method="post">        
        <br><br>
        <?php if (!empty($clickedFile)): ?>
            <input type="hidden" name="file" value="<?php echo htmlspecialchars($clickedFile); ?>">
        <?php endif; ?>

        <?php

        if (!empty($clickedFile)) {
            foreach ($errors as $id => $error) {
                $errorText = 'error_' . htmlspecialchars($id);
                $limitText = 'limit_' . htmlspecialchars($id);
                echo "<input type='text' id='" . $errorText . "' name='" . $errorText . "' value='" . htmlspecialchars($error) . "'>";
                echo "&nbsp<input type='text' id='" . $limitText . "' name='" . $limitText . "' value='" . $limits[$id] . "'>";
                echo "&nbsp<input type='submit' id='button_" . $id . "' name='delete_error' value='" . $id . "'>Delete Error</button><br><br>";
            }

            if ($clickedFile == 'newProduct') {
                echo "Add new Product here";
                echo "<br><br><input type='text' id='addProd' name='addProd'><br><br>";
                echo "<input type='submit' name='submit' value='Add Product'>";
            } else {
                $x = count($errors);
                $errorText = 'error_' . htmlspecialchars($x);
                $limitText = 'limit_' . htmlspecialchars($x);

                echo "Add new Error here";
                echo "<br><br><input type='text' id='" . $errorText . "' name='" . $errorText . "'>";
                echo "&nbsp";
                echo "<input type='text' id='" . $limitText . "' name='" . $limitText . "'><br><br>";
                echo "<input type='submit' name='submit' value='Update Error List'><br><br>";
                echo "<input type='submit' name='deleteProd' value='Delete Product'>";
            }
        } else {
            echo "<h2>Click file to update</h2>";
        }

        ?>
        <br><br>
        <button type="button" onclick="window.location.href='logout.php'">Logout</button>
    </form>

</body>
</html>

<?php

        include("updateFile.php");

?>