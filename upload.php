<?php
include "connection.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdfFile'])) {
    $fileName = $_FILES['pdfFile']['name'];
    $fileType = $_FILES['pdfFile']['type'];
    $fileSize = $_FILES['pdfFile']['size'];
    $fileContent = file_get_contents($_FILES['pdfFile']['tmp_name']);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO pdf_files (name, type, size, content) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $fileName, $fileType, $fileSize, $fileContent);

    if ($stmt->execute()) {
        echo "File uploaded and stored in the database successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No file uploaded.";
}

?>

<form enctype="multipart/form-data" action="upload.php" method="POST">
        <input type="file" name="pdfFile" id="pdfFile">
        <input type="submit" value="Upload PDF" name="submit">
</form>
