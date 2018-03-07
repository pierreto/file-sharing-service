<?php

// download.php
// author: Pierre Quang Linh To
// Download file from server with given short code

include_once("config.php");

// Connect to database
$conn = connect();

$share_code = $_GET['code'];

// Get all files with the same share code
$mysql_select = "SELECT id, file_name, type, save_file_name FROM uploads WHERE share_code = '".$share_code."';";
$stmt = $conn->prepare($mysql_select);
$stmt->execute();

// Download single file
if ($stmt->rowCount() == 1) {
    echo "<h1>Downloading file...</h1>";

    $file = $stmt->fetch();

    $fileURL = $file['save_file_name'];
    $fileName = $file['file_name'];
    $fileType = $file['type'];

    print_r ($fileURL."<br>");
    echo "[1] ".$fileName."<br>";
    echo $fileType."<br>";

    if(!$fileURL){ // file does not exist
        die('File not found.');
    } else {
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename= ".$fileName);
        header("Content-Type: $fileType");
        header("Content-Length: " . filesize($fileURL));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');

        ob_clean();
        flush(); // Flush system output buffer
        readfile($fileURL);

        exit;
    }
}
// Download multiple files (zip)
else {
    echo "<h1>Downloading files...</h1>";

    $files = $stmt->fetchAll();

    // We keep track of the local names used so far to prevent duplicates.
    $arrLocalNames = array();

    // Create zip
    $zipname = $share_code.'.zip';
    $zip = new ZipArchive;

    // Populate zip
    $zip->open($zipname, ZipArchive::CREATE);
    foreach ($files as $file) {
        $fileURL = $file['save_file_name'];
        $fileName = $file['file_name'];

        // We have to make sure that there aren't two of the same local names.
        // Otherwise, one file will override the other and we will be missing the file.
        while( $arrLocalNames[ $fileName ] > 0 ) {
            $arrPathInfo = pathinfo( $fileName );
            $intNext = $arrLocalNames[ $fileName ]++;
            $fileName = "$arrPathInfo[filename] ($intNext).$arrPathInfo[extension]";
        }
        // Add to the count.
        $arrLocalNames[ $fileName ]++;

        // Add file and rename to the original file name
        $zip->addFile($fileURL, $fileName);
    }
    $zip->close();

    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename='.$zipname);
    header('Content-Length: ' . filesize($zipname));

    ob_clean();
    flush(); // Flush system output buffer
    readfile($zipname);
    unlink($zipname); // Delete zip at root
}

?>