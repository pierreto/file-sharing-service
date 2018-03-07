# FILE SHARING SERVICE
## Tested with Chrome 64, Firefox 58, Safari 11

### SETUP
1. Copy and paste /file_sharing_service folder on your computer
2. Define /public folder as the document root of your web server
3. Start Apache Server and MySQL Server
3. Configure the database (phpMyAdmin) as follow:
    a. username: root
    b. password: test
4. Run reset.php script by typing localhost:your_apache_port/reset.php
    a. Database share_40105375 should be created.
    b. Table uploads should be added.
    c. Uploaded files folder /public/uploads should be empty.
5. Start single page application by typing localhost:your_apache_port/
    a. If you want to upload files > 450M, change values in .htaccess file.

### FEATURES
1. Drag-and-drop files to upload
2. Select files to upload from computer
3. Delete uploaded file from server/database
4. Generate short URL to download uploaded files (share)
5. Download uploaded files with short URL
