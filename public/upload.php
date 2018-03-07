<?php

// upload.php
// author: Pierre Quang Linh To
// Save files to server and database

include_once("config.php");

header('Content-type: application/json');

// Connect to database
$conn = connect();

if(!empty($_FILES)){
    $upload_dir = "uploads/";
    
    // Upload each file dropped
    for ($i=0; $i < count($_FILES['files']['name']); $i++){
        $fileName = $_FILES['files']['name'][$i];
        $size = $_FILES['files']['size'][$i];
        $type = $_FILES['files']['type'][$i];
        $tmpName = $_FILES['files']['tmp_name'][$i];
        $shareCode = $_POST['shareCode'];
        $id = uniqid("", true);
        $saveFileName = $upload_dir.$shareCode."-".$id;

        $response = array();

        // Move to upload folder
        if(move_uploaded_file($tmpName, $saveFileName)){
            // Insert file information into db table
            $mysql_insert = "INSERT INTO uploads (id, file_name, size, type, share_code, save_file_name, upload_time) VALUES ('".$id."', '".$fileName."', $size, '".$type."', '".$shareCode."', '".$saveFileName."', '".date("Y-m-d H:i:s")."')";
            $conn->exec($mysql_insert);
            $response['success'] = true;
            $response['save_file_name'] = $saveFileName;
        } else {
            $response['success'] = false;
            $response['error'] = 'File was not uploaded.';
        }
    }
}

// Return response
echo json_encode($response);

?>