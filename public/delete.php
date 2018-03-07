<?php

// delete.php
// author: Pierre Quang Linh To
// Delete file from server and database

include_once("config.php");

// Connect to database
$conn = connect();

$save_file_name = $_POST['filename'];

// Remove from databse
$mysql_delete = "DELETE FROM uploads WHERE save_file_name = '" . $save_file_name . "';)";
$conn->exec($mysql_delete);

// Remove from server
unlink($save_file_name);

echo $save_file_name . ' was deleted successfully.';

?>