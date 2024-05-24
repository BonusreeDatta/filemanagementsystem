<?php
include "connection.php";

// Check if ID is provided
if(isset($_GET['id']) && !empty(trim($_GET['id']))) {
    // Sanitize the ID to prevent SQL injection
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch the PDF details from the database
    $sql = "SELECT name, path FROM pdf_files WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pdfName = $row['name'];
        $pdfPath = $row['path'];
    } else {
        echo "PDF not found.";
        exit;
    }
} else {
    echo "Invalid PDF ID.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pdfName, ENT_QUOTES, 'UTF-8'); ?></title>
    <!-- Include PDF.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
</head>
<body>
    <h1><?php echo htmlspecialchars($pdfName, ENT_QUOTES, 'UTF-8'); ?></h1>
    <!-- PDF Viewer Canvas -->
    <canvas id="pdfViewer" style="border:1px solid black;"></canvas>

    <!-- Form to submit PDF -->
    <form action="download_pdf.php" method="post">
        <input type="hidden" name="path" value="<?php echo htmlspecialchars($pdfPath, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($pdfName, ENT_QUOTES, 'UTF-8'); ?>">
        <button id="createUnderlineAnnotation" type="submit">Save PDF</button>
    </form>

    <script>
        // URL of the PDF file
        const pdfUrl = "<?php echo htmlspecialchars($pdfPath, ENT_QUOTES, 'UTF-8'); ?>";

        // Initialize PDF.js
        pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
            // Fetch the first page of the PDF
            return pdf.getPage(1);
        }).then(page => {
            // Set canvas element
            const canvas = document.getElementById('pdfViewer');
            const context = canvas.getContext('2d');

            // Set viewport
            const viewport = page.getViewport({ scale: 1.5 });

            // Set canvas size
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            // Render PDF page into canvas
            page.render({
                canvasContext: context,
                viewport: viewport
            });
        }).catch(error => {
            console.error('Error loading PDF:', error);
        });
    </script>
</body>
</html>
