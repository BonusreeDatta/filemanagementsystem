<?php
include "connection.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdfFile'])) {
    $fileName = basename($_FILES['pdfFile']['name']);
    $fileType = $_FILES['pdfFile']['type'];
    $fileSize = $_FILES['pdfFile']['size'];
    $fileTmpName = $_FILES['pdfFile']['tmp_name'];
    $filePath = $uploadDir . $fileName;

    // Check if file already exists
    if (file_exists($filePath)) {
        echo "Sorry, file already exists.";
    } else {
        // Move the file to the specified directory
        if (move_uploaded_file($fileTmpName, $filePath)) {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO pdf_files (name, type, size, path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $fileName, $fileType, $fileSize, $filePath);

            if ($stmt->execute()) {
                echo "File uploaded and path saved to database successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    $stmt->close();
} else {
    echo "No file uploaded.";
}
    $conn->close();

?>

<form enctype="multipart/form-data" action="upload.php" method="POST">
        <input type="file" name="pdfFile" id="pdfFile">
        <input type="submit" value="Upload PDF" name="submit">
</form>
