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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf_viewer.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf_viewer.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-annotate.js/2.1.0/pdf-annotate.min.js"></script>
    <style>
        #viewerContainer {
            width: 100%;
            height: 500px;
            overflow: auto;
        }
        #pdfViewer {
            width: 100%;
        }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($pdfName, ENT_QUOTES, 'UTF-8'); ?></h1>
    <div id="viewerContainer">
        <div id="pdfViewer"></div>
    </div>
    <button id="addTextAnnotation" type="button">Text Annotation</button>
    <button id="download" type="button">Save Annotations</button>
    <script>
        const pdfUrl = "<?php echo htmlspecialchars($pdfPath, ENT_QUOTES, 'UTF-8'); ?>";

        const initPDFViewer = async () => {
            const pdfjsLib = window['pdfjs-dist/build/pdf'];
            const pdfjsViewer = window['pdfjs-dist/web/pdf_viewer'];

            const loadingTask = pdfjsLib.getDocument(pdfUrl);
            const pdfDocument = await loadingTask.promise;

            const container = document.getElementById('viewerContainer');
            const eventBus = new pdfjsViewer.EventBus();
            const pdfViewer = new pdfjsViewer.PDFViewer({
                container: container,
                eventBus: eventBus
            });

            pdfViewer.setDocument(pdfDocument);

            eventBus.on('pagesinit', function() {
                pdfViewer.currentScaleValue = 'page-width';
            });

            pdfDocument.getData().then((data) => {
                pdfAnnotate.init({
                    documentId: 'pdfViewer',
                    pdfDocument: pdfDocument,
                    data: data
                });
            });
        };

        document.getElementById('addTextAnnotation').addEventListener('click', () => {
            pdfAnnotate.getStoreAdapter().addAnnotation(0, {
                type: 'textbox',
                page: 0,
                size: 12,
                color: '000000',
                content: 'Sample Text Annotation',
                position: { x: 100, y: 100 }
            });
        });

        document.getElementById('download').addEventListener('click', async () => {
            const data = await pdfAnnotate.getStoreAdapter().getDocument();
            const blob = new Blob([data], { type: 'application/pdf' });

            const formData = new FormData();
            formData.append('annotated_pdf', blob, 'annotated.pdf');

            fetch('upload_pdf.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => alert(result))
            .catch(error => console.error('Error:', error));
        });

        window.onload = initPDFViewer;
    </script>
</body>
</html>
