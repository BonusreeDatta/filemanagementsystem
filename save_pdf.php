<?php
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all necessary data is provided
    if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['content'])) {
        // Retrieve and sanitize data from the form
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $pdfName = mysqli_real_escape_string($conn, $_POST['name']);
        $pdfContent = base64_decode($_POST['content']);

        // // Fetch the current content of the PDF from the database
        // $sql_fetch = "SELECT content FROM pdf_files WHERE id = $id";
        // $result_fetch = $conn->query($sql_fetch);
        // if ($result_fetch->num_rows > 0) {
        //     $row_fetch = $result_fetch->fetch_assoc();
        //     $currentContent = $row_fetch['content'];
        // } else {
        //     $message = "PDF not found.";
        // }

        // Check if the content has been modified

        $sql = "UPDATE pdf_files SET content = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        var_dump($pdfContent);
        // Bind parameters
        $stmt->bind_param("si", $pdfContent, $id);
        var_dump($stmt);
        die();
        // Execute the statement
        if ($stmt->execute()) {
            echo "PDF saved successfully.";
            // echo "Decoded PDF content: ";
            // var_dump($pdfContent);
        } else {
            echo "Error updating PDF: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Missing data.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
