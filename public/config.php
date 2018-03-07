<?php

// config.php
// author: Pierre Quang Linh To
// Connection to database
// Helper functions

// PDO connect
function connect() {
    $host = 'localhost';
    $db_name = 'share_40105375';
    $db_user = 'root';
    $db_password = 'test';
    $conn = new PDO('mysql:host='.$host.';dbname='.$db_name, $db_user, $db_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    return $conn;
}

// PDO disconnect
function disconnect($conn) {
    $conn -> close();
}

// Remove all files, folders and their subfolders
function rrmdir($dir) {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          if (filetype($dir."/".$object) == "dir") 
             rrmdir($dir."/".$object); 
          else unlink   ($dir."/".$object);
        }
      }
      reset($objects);
      rmdir($dir);
    }
}

?>