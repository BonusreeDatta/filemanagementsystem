<?php
include 'connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT name, type, size, content FROM pdf_files WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($name, $type, $size, $content);
    $stmt->fetch();
    $stmt->close();

    header("Content-type: application/pdf");
    header("Content-Length: " . strlen($content));
    header("Content-Disposition: inline; filename=\"$name\"");
    echo $content;
} else {
    echo "No PDF ID provided.";
}

$conn->close();
?>
