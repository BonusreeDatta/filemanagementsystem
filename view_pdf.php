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
    <script src="node_modules/annotpdf/_bundles/pdfAnnotate.js"></script>
    <script src="node_modules/annotpdf/_bundles/pdfAnnotate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf_viewer.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf_viewer.min.css">
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
    <!-- <h1><?php echo htmlspecialchars($pdfName, ENT_QUOTES, 'UTF-8'); ?></h1>
    <embed src="<?php echo htmlspecialchars($pdfPath, ENT_QUOTES, 'UTF-8'); ?>" width="100%" height="500px" />

    <form action="download_pdf.php" method="post">
        <input type="hidden" name="path" value="<?php echo htmlspecialchars($pdfPath, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($pdfName, ENT_QUOTES, 'UTF-8'); ?>">
        <button id="createUnderlineAnnotation" type="submit">Save PDF</button>
    </form>
</body>


<script src="node_modules/annotpdf/bundles/pdfAnnotate.js"></script>
    <script>
        // Initialize annotation functionality after the PDF is rendered

        const pdfPath = "<?php echo htmlspecialchars($pdfPath, ENT_QUOTES, 'UTF-8'); ?>";
        AnnotationFactory.loadFile(pdfPath).then((factory) => {
            factory.createUnderlineAnnotation({
                page: 0,
                rect: [100, 100, 200, 200],
                contents: "Test123",
                author: "John",
                color: {r: 128, g: 128, b: 128},
                opacity: 0.5
            });
        });
    </script>  -->
    <embed src="<?php echo htmlspecialchars($pdfPath, ENT_QUOTES, 'UTF-8'); ?>" width="100%" height="500px" />
    <h1><?php echo htmlspecialchars($pdfName, ENT_QUOTES, 'UTF-8'); ?></h1>
    <div id="viewerContainer">
        <div id="pdfViewer"></div>
    </div>
    <button id="addTextAnnotation" type="button">Text Annotation</button>
    <button id="download" type="button">Download</button>
    <script>
        const pdfUrl = "<?php echo htmlspecialchars($pdfPath, ENT_QUOTES, 'UTF-8'); ?>";
        let pdfFactory;
        let pdfViewer;

        const initPDFViewer = async () => {
            const pdfjsLib = window['pdfjs-2.14.305-dist/build/pdf'];
            const pdfjsViewer = window['pdfjs-2.14.305-dist/web/pdf_viewer'];

            const loadingTask = pdfjsLib.getDocument(pdfUrl);
            const pdfDocument = await loadingTask.promise;

            const container = document.getElementById('viewerContainer');
            pdfViewer = new pdfjsViewer.PDFViewer({
                container: container
            });

            pdfViewer.setDocument(pdfDocument);

            pdfDocument.getData().then((data) => {
                pdfFactory = new pdfAnnotate.AnnotationFactory(data);
            });

            setTimeout(() => {
                pdfViewer.currentScaleValue = 'page-width';
            }, 1500);
        };

        document.getElementById('addTextAnnotation').addEventListener('click', () => {
            const parameters = {
                page: pdfViewer.currentPageNumber - 1,
                rect: [100, 100, 200, 200],
                contents: "Sample Text Annotation",
                author: "Author Name",
                color: { r: 0, g: 0, b: 0 },
                opacity: 1.0
            };
            pdfFactory.createTextAnnotation(parameters);
        });

        document.getElementById('download').addEventListener('click', () => {
            pdfFactory.download();
        });

        window.onload = initPDFViewer;
    </script>
</body>
</html>