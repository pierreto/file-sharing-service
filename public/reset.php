<?php

// reset.php
// author: Pierre Quang Linh To
// System reset script

include_once("config.php");

echo "<h1>System Reset</h1>";

// Clean uploads
rrmdir("uploads/");
mkdir("uploads/");

echo "[1] Files removed from <b>./uploads/</b> successfully <br>";

// Name of the file
$filename = '../config/uploads.sql';
// MySQL host
$host = 'localhost';
// MySQL username
$db_user = 'root';
// MySQL password
$db_password = 'test';
// Database name
$db_name = 'share_40105375';

// Connect to MySQL server
$conn = new PDO("mysql:host=".$host, $db_user, $db_password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db_name = "`".str_replace("`","``",$db_name)."`";

// Database is created if it does not exist
$conn->query("CREATE DATABASE IF NOT EXISTS $db_name");

// Select database
$conn->query("use $db_name");

echo "[2] Database <b>share_40105375</b> created successfully <br>";

// Temporary variable, used to store current query
$templine = '';
// Read in entire file
$lines = file($filename);

// Loop through each line
foreach ($lines as $line)
{
    // Skip it if it's a comment
    if (substr($line, 0, 2) == '--' || $line == '')
        continue;

    // Add this line to the current segment
    $templine .= $line;
    // If it has a semicolon at the end, it's the end of the query
    if (substr(trim($line), -1, 1) == ';')
    {
        // Perform the query
        $conn->query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
        // Reset temp variable to empty
        $templine = '';
    }
}

echo "[3] Table <b>uploads</b> imported successfully <br>";

echo "<br>Done. Thank you! <br>";

?>