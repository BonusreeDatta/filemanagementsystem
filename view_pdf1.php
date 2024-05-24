<?php
include "connection.php";

// Check if ID is provided
if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    // Sanitize the ID to prevent SQL injection
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch the PDF content from the database
    $sql = "SELECT name, content FROM pdf_files WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pdfName = $row['name'];
        $pdfContent = $row['content'];
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
    <title><?php echo htmlspecialchars($pdfName); ?></title>
    <script src="pdfjs-2.14.305-dist/build/pdf.js"></script>
    <script src="https://unpkg.com/pdf-lib@1.4.0"></script>
    <script src="https://unpkg.com/downloadjs@1.4.7"></script>
</head>
<body>
    <h1><?php echo htmlspecialchars($pdfName); ?></h1>
    <div>
       <canvas id="pdf-canvas" width="600" height="800"></canvas>
    </div>
    <button id="save-pdf-button">Save PDF</button>
    <button onclick="modifyPdf()">Modify PDF</button>
   
    <script>
        
        const { degrees, PDFDocument, rgb, StandardFonts } = PDFLib

    async function modifyPdf() {
      // Fetch an existing PDF document
      const url = 'atob('<?php echo base64_encode($pdfContent); ?>'); 
  		const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())

      // Load a PDFDocument from the existing PDF bytes
      const pdfDoc = await PDFDocument.load(existingPdfBytes)

      // Embed the Helvetica font
      const helveticaFont = await pdfDoc.embedFont(StandardFonts.Helvetica)

      // Get the first page of the document
      const pages = pdfDoc.getPages()
      const firstPage = pages[0]

      // Get the width and height of the first page
      const { width, height } = firstPage.getSize()

      // Draw a string of text diagonally across the first page
      firstPage.drawText('This text was added with JavaScript!', {
        x: 5,
        y: height / 2 + 300,
        size: 50,
        font: helveticaFont,
        color: rgb(0.95, 0.1, 0.1),
        rotate: degrees(-45),
      })

      // Serialize the PDFDocument to bytes (a Uint8Array)
      const pdfBytes = await pdfDoc.save()

			// Trigger the browser to download the PDF document
      download(pdfBytes, "pdf-lib_modification_example.pdf", "application/pdf");
    }
    </script>
</body>
</html>
