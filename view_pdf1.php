<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pdfName, ENT_QUOTES, 'UTF-8'); ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf_viewer.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf_viewer.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf.worker.min.js"></script>
    <script src="node_modules/annotpdf/_bundles/pdfAnnotate.js"></script> <!-- Adjust the path to where you have pdfAnnotate.js -->
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
    <button id="addTextAnnotation" type="button">Add Text Annotation</button>
    <button id="download" type="button">Download</button>
    <script>
        const pdfUrl = "<?php echo htmlspecialchars($pdfPath, ENT_QUOTES, 'UTF-8'); ?>";
        let pdfFactory;
        let pdfViewer;

        const initPDFViewer = async () => {
            const pdfjsLib = window['pdfjs-dist/build/pdf'];
            const pdfjsViewer = window['pdfjs-dist/web/pdf_viewer'];

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
