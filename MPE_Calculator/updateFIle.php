<?php
    if (isset($_POST['delete_error'])) {
        $deleteId = $_POST['delete_error'];
    
        unset($errors[$deleteId]);
        unset($limits[$deleteId]);
    
        $errors = array_values($errors);
        $limits = array_values($limits);
    
        $handle = fopen($clickedFile, 'w');
        if ($handle) {
            foreach ($errors as $id => $error) {
                
                if (isset($limits[$id]) && $error != '') {
                    
                    $limit = $limits[$id];
                    $data = $limit . ',' . $error;
                    fwrite($handle, $data . "\n");
                }
            }
            fclose($handle);
        } else {
            echo "Failed to open file for writing.";
        }
    
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    if (isset($_POST['submit']) && !isset($_POST['delete'])) {
        if ($clickedFile == 'newProduct') {
            $temp = strtoupper($_POST['addProd']) . '.txt';
            
            if (file_exists($temp)) {
                echo "File already exists.";
            } else {
                $file = fopen($temp, 'w');
                //Default data for new product
                if ($file) {
                    $e = array(
                        0 => "(FN08) Faded Character",
                        1 => "(FN15) Handler's Error",
                        2 => "(FN26) Extraneous Material",
                        3 => "(FN50) Package Locate ",
                        4 => "(FN97) Package Scratch"
                    );
    
                    $l = array(
                        0 => 0.34,
                        1 => 0.128,
                        2 => 0.2,
                        3 => 0.2,
                        4 => 0.13
                    );
    
                    foreach ($e as $errorId => $error) {
                        if (isset($l[$errorId])) {
                            $limit = $l[$errorId];
                            fwrite($file, $limit . ',' . $error . "\n");
                        }
                    }
                    fclose($file);
                } else {
                    echo "Failed to open file";
                }
            }
        } else {
            foreach ($errors as $id => $error) {
                $errorText = 'error_' . $id;
                $limitText = 'limit_' . $id;            
                //Textboxes population
                if (isset($_POST[$errorText]) && $_POST[$errorText] !== '') {
                    $errors[$id] = trim($_POST[$errorText]);
                }
                if (isset($_POST[$limitText]) && $_POST[$limitText] !== '') {
                    $limits[$id] = trim($_POST[$limitText]);
                }
                if ($error == "Others") {
                    $limits[$id] = 100;
                }
            }
    
            $x = count($errors);
            //For new data/error
            $errorText = 'error_' . htmlspecialchars($x);
            $limitText = 'limit_' . htmlspecialchars($x);
    
            if (!empty($_POST[$errorText]) && !empty($_POST[$limitText])) {            
                $newError = trim($_POST[$errorText]);
                $newLimit = trim($_POST[$limitText]);
    
                if ($newError == "Others") { //Default Others to 100 to avoid being tagged
                    $newLimit = 100;
                }
    
                // Debugging: Print the new error and existing errors
                echo "New Error: " . htmlspecialchars($newError) . "<br>";
                echo "Existing Errors: <br>";
                foreach ($errors as $existingError) {
                    echo htmlspecialchars($existingError) . "<br>";
                }            
    
                if (!in_array($newError, $errors)) { // Validation to check if error already exists
                    array_push($errors, $newError);
                    array_push($limits, $newLimit);
                } else {
                     
                    echo "Error already exists and will not be added again.<br>";
                }
            } else {
                echo "No new error to add<br>";
            }
    
            $handle = fopen($clickedFile, 'w');
    
            if ($handle) {
                echo "Opening";
                foreach ($errors as $id => $error) {
                    if (isset($limits[$id]) && $error !== '') {
                        $limit = $limits[$id];
                        $data = $limit . ',' . $error;
                        fwrite($handle, $data . "\n");
                    }
                }
                fclose($handle);
            } else {
                echo "Failed to open file for writing.";
            }
    
            foreach ($errors as $id => $error) {
                echo "Error: " . htmlspecialchars($error) . " with " . htmlspecialchars($limits[$id]) . "<br>";
            }
        }
    
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    if (isset($_POST['deleteProd'])) {
        //$filePath = '/var/www/html/sysadmin/MPE_Calculator/' . basename($clickedFile);
        $filePath = 'C:/xampp/htdocs/MPE_Calculator/' . basename($clickedFile);
    
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                echo "File deleted successfully.";
            } else {
                echo "Failed to delete file.";
            }
        } else {
            echo "File does not exist.";
        }
    
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

?>