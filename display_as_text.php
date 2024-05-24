<?php
include "connection.php";

// Function to decode base64 encoded content
function base64_decode_pdf($data) {
    return base64_decode($data);
}

// Retrieve the PDF file ID from the URL parameter
if(isset($_GET['id'])) {
    $pdf_id = $_GET['id'];
    
    // Fetch the encoded PDF content from the database
    $sql = "SELECT name, encoded_content FROM pdf_files WHERE id = $pdf_id";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $pdf_name = $row['name'];
            $encoded_content = $row['encoded_content'];
            
            // Decode the PDF content
            $decoded_content = base64_decode_pdf($encoded_content);
            
            if($decoded_content !== false) {
                // Display the decoded PDF content as text
                header("Content-type: text/plain");
                header("Content-Disposition: inline; filename=$pdf_name.txt");
                echo $decoded_content;
            } else {
                echo "Failed to decode PDF file.";
            }
        } else {
            echo "No PDF found with ID: $pdf_id";
        }
    } else {
        echo "Query failed: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
