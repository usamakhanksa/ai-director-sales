<?php
// Required: anonymous function reference number as explained above.
$funcNum = $_GET['CKEditorFuncNum'] ;
// Optional: instance name (might be used to load a specific configuration file or anything else).
$CKEditor = $_GET['editor'] ;
// Optional: might be used to provide localized messages.
$langCode = $_GET['langCode'] ;
// Optional: compare it with the value of `ckCsrfToken` sent in a cookie to protect your server side uploader against CSRF.
// Available since CKEditor 4.5.6.
$token = $_POST['ckCsrfToken'] ;


// Check the $_FILES array and save the file. Assign the correct path to a variable ($url).
$url = base_url().'uploads/ckImages/file.jpg';
// Usually you will only assign something here if the file could not be uploaded.
$message = 'The uploaded file has been renamed';

echo "<script type='text/javascript'>window.parent.editor.tools.callFunction($funcNum, '$url', '$message');</script>";
?>