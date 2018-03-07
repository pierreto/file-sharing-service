<?php

// index.php
// author: Pierre Quang Linh To
// Landing page

include_once("config.php");

// Connect to database
$conn = connect();

// e.g localhost:8888
$host = $_SERVER['HTTP_HOST'];

// Get short code previously generated
$mysql_select = "SELECT DISTINCT share_code FROM uploads;";
$stmt = $conn->prepare($mysql_select);
$stmt->execute();
$shareCodes = json_encode($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
?>

<!DOCTYPE html>
<html>

    <head>
        <title>File Sharing Service</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="css/style.css"/>
        <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
    </head>

    <body>

        <div class="container" role="main"> 

            <!-- HEADER -->
            <h2 id="header">File Sharing Service</h2>

            <!-- FILE INPUT -->
            <form id="dragDropFiles" class="box" method="post" action="upload.php" enctype="multipart/form-data">
                <div class="box__input">
                    <input disable onclick="" class="box__file" type="file" name="files[]" id="file" multiple />
                    <label for="file">
                        <figure>
                            <svg class="box__icon" xmlns="http://www.w3.org/2000/svg" width="50" height="43" viewBox="0 0 50 43"><path d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z"/></svg>
                        </figure> 
                        <span class="box__dragndrop">Click to choose a file or drag it here.</span>
                    </label>
                </div>
            </form>

            <div id="blockContainer">
                <!-- PROGRESS BAR -->
                <div id="progressBar">
                    <div class="bar"></div >
                    <div class="percent">0%</div >
                </div>

                <!-- DOWNLOAD URL -->
                <div id="urlContainer">
                    <span id="shareCode"></span>
                    <div class="tooltip">
                        <button type="button" onclick="copyToClipboard()" onmouseout="copyToClipboardOnMouse()">
                            <img src="img/copy_icon.png" alt="copy_icon" />
                            <span class="tooltiptext" id="myTooltip">Copy to clipboard</span>
                        </button>
                    </div>
                </div>
                
                <!-- FILE SHARING -->
                <div id="shareContainer">
                    <button type="button" id="shareButton" disabled>Share</button>
                </div>
            </div>

            <br>
            <br>
            <br>

            <!-- FILE LISTING -->
            <div id="fileContainer">
                <h4>FILES UPLOADED</h4>
                <form method="post" action="delete.php" >
                    <table cellpadding="2px" cellspacing="10px" border="0px" class="table table-condensed">
                        <thead>
                            <tr>
                                <th id="fileName">File name</th>
                                <th id="type">Type</th>
                                <th id="size">Size</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="filesUploaded">
                        </tbody>
                    </table>
                </form>
            </div>

        </div>
        <script type="text/javascript" src="js/share.js"></script>
        <script type="text/javascript" src="js/drag-and-drop.js"></script>
    </body>

</html>

<!-- SHARE CODE GENERATOR -->
<script>
    // Generate share code only once per page load
    $( document ).ready(function() {
        var host = "<?php echo $host; ?>";
        var shareCodes = <?php echo $shareCodes; ?>;
        var shareCode = generateUnique(shareCodes);
        
        $("#shareCode").text("http://" + host + "/" + shareCode);
        $("#shareCode").attr("name", shareCode);

        $("#dragDropFiles").show("slow");
        $("#progressBar").show("slow");
        $("#shareButton").show("slow");
    });

    var ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var ID_LENGTH = 6;

    var generate = function () {
        var rtn = '';

        for (var i = 0; i < ID_LENGTH; i++) {
            rtn += ALPHABET.charAt(Math.floor(Math.random() * ALPHABET.length));
        }

        return rtn;
    }

    var UNIQUE_RETRIES = 9999;

    var generateUnique = function (previous) {
        previous = previous || [];
        var retries = 0;
        var id;

        // Try to generate a unique ID,
        // i.e. one that isn't in the previous.
        while (!id && retries < UNIQUE_RETRIES) {
            id = generate();
            if (previous.indexOf(id) !== -1) {
                id = null;
                retries++;
            }
        }

        return id;
    };
</script>