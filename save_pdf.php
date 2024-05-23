<?php
include 'connection.php'
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['annotations'])) {
    $id = intval($_POST['pdf_id']);
    $annotations = file_get_contents($_FILES['annotations']['tmp_name']);

    // Debugging output
    echo "<pre>";
    var_dump($id);
    var_dump($annotations);
    echo "</pre>";
    exit();

    // Retrieve the original PDF
    $stmt = $conn->prepare("SELECT content FROM pdf_files WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($content);
    $stmt->fetch();
    $stmt->close();

    // Merge the original PDF with annotations
    $pdf = new \setasign\Fpdi\Fpdi();
    $pageCount = $pdf->setSourceFile(StreamReader::createByString($content));
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $templateId = $pdf->importPage($pageNo);
        $pdf->AddPage();
        $pdf->useTemplate($templateId, ['adjustPageSize' => true]);

        if ($pageNo == 1) { // Assume annotations are for the first page
            $pdf->Image($annotations, 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
        }
    }

    $newContent = $pdf->Output('S');

    // Save the modified PDF back to the database
    $stmt = $conn->prepare("UPDATE pdf_files SET content = ? WHERE id = ?");
    $stmt->bind_param("bi", $newContent, $id);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'PDF saved successfully']);
    } else {
        echo json_encode(['message' => 'Failed to save PDF']);
    }

    $stmt->close();
} else {
    echo json_encode(['message' => 'No annotations provided']);
}

$conn->close();
?>
