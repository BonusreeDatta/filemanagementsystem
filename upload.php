<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf'])) {
    if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['pdf']['name']);

        if (move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadFile)) {
            echo "File is valid, and was successfully uploaded.\n";
        } else {
            echo "Failed to move uploaded file.\n";
        }
    } else {
        echo "File upload error: " . $_FILES['pdf']['error'] . "\n";
    }
}
?>

<form enctype="multipart/form-data" action="upload.php" method="POST">
    <input type="file" name="pdf" />
    <input type="submit" value="Upload PDF" />
</form>
