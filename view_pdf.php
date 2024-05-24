<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pdfName, ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($pdfName, ENT_QUOTES, 'UTF-8'); ?></h1>
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
        const pdfViewer = document.getElementById('pdfViewer');
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
    </script> -->

     <!-- Your PDF viewer container -->
     <embed id="pdfViewer" src="<?php echo htmlspecialchars($pdfPath, ENT_QUOTES, 'UTF-8'); ?>" width="100%" height="500px" />
     <button id="addTextAnnotation" type="button">Text Annotation</button>
     <button id="download" type="button">Download</button>
<!-- Include the annotpdf library -->
<script src="node_modules/annotpdf/_bundles/pdfAnnotate.js"></script>
<script>

let pdfUrl = "<?php echo htmlspecialchars($pdfPath, ENT_QUOTES, 'UTF-8'); ?>" 
                        let once = false
                        let pdfFactory = undefined
                        let pdfViewer = undefined
                        let coordinates = []

                        let __x_1 = 0
                        let __y_1 = 0
                        let doCircle = false
                        let doSquare = false
                        document.getElementById("content").value = "Test Content"
                        document.getElementById("author").value = "Test Author"

                        let setStatus = function (value) {
                                document.getElementById("statusLine").innerHTML = " " + value + " "

                                document.getElementById("annotationCount").innerHTML = " " + (pdfFactory.getAnnotationCount() + 1) + " "
                        }

                        let updateCoordinates = function () {
                                let _str = coordinates.map((x) => Math.round(x)).join(",")
                                document.getElementById("coords").innerHTML = _str
                        }

                        document.querySelector('#addFreeTextAnnotation').addEventListener('click', (evt) => {
                                setStatus("Added FreeText Annotation")
                                let parameters = getParameters()

                                pdfFactory.createFreeTextAnnotation(pdfViewer.currentPageNumber - 1,[parameters[0], parameters[1], parameters[0] + 22, parameters[1] + 22], parameters[2], parameters[3])
                        })

    document.querySelector('#download').addEventListener('click', (evt) => {
                                setStatus("Download")
                                pdfFactory.download()
                        })
     document.querySelector('#addTextAnnotation').addEventListener('click', (evt) => {
                                setStatus("Added Text Annotation")
                                let parameters = getParameters()
                                pdfFactory.createTextAnnotation(pdfViewer.currentPageNumber - 1, [parameters[0], parameters[1], parameters[0] + 22, parameters[1] + 22], parameters[2], parameters[3])
                                coordinates = []
                                updateCoordinates()
                        })

                        let computePageOffset = function () {
                                let pageId = "page" + pdfViewer.currentPageNumber
                                let pg = document.getElementById(pageId)

                                var rect = pg.getBoundingClientRect(), bodyElt = document.body;
                                return {
                                        top: rect.top + bodyElt .scrollTop,
                                        left: rect.left + bodyElt .scrollLeft
                                }
                        }

                        window.onload =  function(){
                                let pdfContainer = document.getElementById('viewerContainer')

                                pdfViewer = new pdfjsViewer.PDFViewer({
                                        container : pdfContainer
                                })

                                pdfContainer.addEventListener('click', (evt) => {
                                        let ost = computePageOffset()
                                        let x = evt.pageX - ost.left
                                        let y = evt.pageY - ost.top

                                        let x_y = pdfViewer._pages[pdfViewer.currentPageNumber - 1].viewport.convertToPdfPoint(x, y)
                                        x = x_y[0]
                                        y = x_y[1]

                                        coordinates.push(x)
                                        coordinates.push(y)

                                        updateCoordinates()

                                        if (doCircle) {
                                                setStatus("Select the second point")

                                                if (coordinates.length == 4) {
                                                        setStatus("Added circle annotation")
                                                        doCircle = false
                                                        let parameters = getParameters()
                                                        pdfFactory.createCircleAnnotation(pdfViewer.currentPageNumber - 1, coordinates.slice(), parameters[2], parameters[3])

                                                        coordinates = []
                                                }
                                        }

                                        if (doSquare) {
                                                setStatus("Select the second point")

                                                if (coordinates.length == 4) {
                                                        setStatus("Added square annotation")
                                                        doCircle = false
                                                        let parameters = getParameters()
                                                        pdfFactory.createSquareAnnotation(pdfViewer.currentPageNumber - 1, coordinates.slice(), parameters[2], parameters[3])

                                                        coordinates = []
                                                }
                                        }
                                })

                                let loadingTask = pdfjsLib.getDocument({
                                        url : pdfUrl
                                })
                                loadingTask.promise.then((pdfDocument) => {
                                        pdfDocument.getData().then((data) => {
                                                pdfFactory = new pdfAnnotate.AnnotationFactory(data)
                                        })
                                        pdfViewer.setDocument(pdfDocument)
                                        setTimeout(() => {
                                                pdfViewer.currentScaleValue = 'page-width'
                                        }, 1500)
                                })
                        }
</script>
</body>


</html>
