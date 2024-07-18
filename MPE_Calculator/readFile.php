<?php

    $directoryPath='C:/xampp/htdocs/MPE_Calculator/';
    //$directoryPath='/var/www/html/sysadmin/MPE_Calculator/';
    $textFiles=glob($directoryPath . '*.txt');

    $currentFullPath=$_SERVER['PHP_SELF'];
    $currentPage=basename($currentFullPath);

    if($currentPage == 'index.php'){
        foreach($textFiles as $filePath){

            $filename = basename($filePath);
            //$webPath='http://tnfsoftwaredev/sysadmin/MPE_Calculator/' . $filename;
            //$webPath='http://10.91.25.16:8080/MPE_Calculator/' . $filename;
            $webPath='http://localhost/MPE_Calculator/' . $filename;
            
            echo '<a href="#" class="file-link" data-filepath="' . $webPath  . '">' . substr($filename, 0, strlen($filename) - 4)  . '</a>&nbsp';
        }
    }
    elseif($currentPage == 'admin.php'){
        $numberofTextFiles=count($textFiles);
        $fileNames=array_map('basename', $textFiles);

        echo"<h1>ADMIN</h1>";

        foreach($fileNames as $fileName){
            $fileUrl=$directoryPath . $fileName;
            echo"<a href=\"admin.php?file=$fileName\">" . substr($fileName, 0, strlen($fileName) - 4) . "</a>&nbsp";
        }
        echo"<a href=\"admin.php?file=newProduct\"> NEW PRODUCT </a>&nbsp";
    }
    
    
?>
