<?php
// Receive annotations from client
$data = file_get_contents("php://input");
$annotations = json_decode($data, true);

// Save annotations to a file (optional)
$file = 'annotated_pdf_annotations.json';
file_put_contents($file, json_encode($annotations));

// Check if the user is logged in and has permission to save the PDF

// Process file upload
if (isset($_FILES['editedPdf']) && $_FILES['editedPdf']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $uploadedFilePath = $uploadDir . basename($_FILES['editedPdf']['name']);
    // Move uploaded file to the server
    if (move_uploaded_file($_FILES['editedPdf']['tmp_name'], $uploadedFilePath)) {
        // File uploaded successfully
        echo json_encode(['success' => true, 'file_path' => $uploadedFilePath]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or file upload failed']);
}
?>
