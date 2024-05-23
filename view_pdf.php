<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View and Annotate PDF</title>
    <!-- Ensure these script sources are correct and accessible -->
    <script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.6.0/fabric.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        #pdf-container {
            position: relative;
        }
        #pdf-canvas {
            border: 1px solid black;
        }
        #annotation-layer {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div id="pdf-container">
        <canvas id="pdf-canvas"></canvas>
        <canvas id="annotation-layer"></canvas>
    </div>
    <button onclick="savePDF()">Save Annotations</button>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const pdfId = urlParams.get('id');
        const url = 'serve_pdf.php?id=' + pdfId;
        console.log("PDF URL: ", url); // Debugging output

        let pdfDoc = null;
        let pageNum = 1;
        const scale = 1.5;
        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');

        // Initialize the annotation canvas properly
        const annotationCanvas = new fabric.Canvas('annotation-layer', {
            isDrawingMode: true
        });

        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            console.log("PDF loaded: ", pdfDoc); // Debugging output
            renderPage(pageNum);
        }).catch(function(error) {
            console.error("Error loading PDF: ", error); // Debugging output
        });

        function renderPage(num) {
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                annotationCanvas.setWidth(viewport.width);
                annotationCanvas.setHeight(viewport.height);

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                page.render(renderContext).promise.then(function() {
                    console.log("Page rendered"); // Debugging output
                });
            }).catch(function(error) {
                console.error("Error rendering page: ", error); // Debugging output
            });
        }

        function savePDF() {
            annotationCanvas.deactivateAll().renderAll();
            const annotationDataUrl = annotationCanvas.toDataURL();
            console.log("Annotation Data URL: ", annotationDataUrl); // Debugging output

            fetch(annotationDataUrl)
                .then(response => response.blob())
                .then(annotationBlob => {
                    console.log("Annotation Blob: ", annotationBlob); // Debugging output
                    const formData = new FormData();
                    formData.append('pdf_id', pdfId); // Use the dynamic ID
                    formData.append('annotations', annotationBlob);

                    fetch('save_pdf.php', {
                        method: 'POST',
                        body: formData
                    }).then(response => response.json())
                      .then(result => {
                          console.log("Save response: ", result); // Debugging output
                          alert(result.message);
                      }).catch(error => {
                          console.error("Save error: ", error); // Debugging output
                      });
                }).catch(error => {
                    console.error("Blob error: ", error); // Debugging output
                });
        }
    </script>
</body>
</html>
