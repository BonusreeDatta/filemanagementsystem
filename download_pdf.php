<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['path']) && isset($_POST['name'])) {
        $filePath = $_POST['path'];
        $fileName = $_POST['name'];

        // Sanitize the file name for security
        $fileName = basename($fileName);

        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            echo "File not found.";
        }
    } else {
        echo "Invalid request.";
    }
} else {
    echo "Invalid request method.";
}
?>
