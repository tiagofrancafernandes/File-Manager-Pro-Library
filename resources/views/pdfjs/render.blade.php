@php
// https://github.com/mozilla/pdf.js/blob/master/examples/learning/helloworld64.html#L42
$bgImage = asset('images/teacher.png');
$canvaRandomId = 'uni_' . uniqid();
$readerUrl = route('reader.render', $hashedid);
// $encodedFileUrl = route('wip.base64_file');
$encodedFileUrl = route('reader.b64_encoded_pdf', $hashedid);
$encodedFileUrl = 'https://files.tiagofranca.com/reader/render/WVYmxkazYeJ0PoX/bdata';
@endphp
<!DOCTYPE html>
<html lang="en"  class="nodoc">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher das Estrelas - Apostila</title>
    {{-- <!-- <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@2.7.570/build/pdf.min.js"></script> --> --}}
    {{-- <!-- <script src="./node_modules/pdfjs-dist/build/pdf.mjs" type="module"></script> --> --}}
    {{-- <script src="./node_modules/pdfjs-dist/build/pdf.mjs" type="module"></script> --}}

    @vite([
        'resources/js/doc-reader/doc-reader.js',
        'resources/js/doc-reader/doc-reader-worker.js',
    ])

    <script>
        window.DEBUG = {{ config('app.debug', false) ? 'true' : 'false' }};
        window.canvaBaseID = '{{ $canvaRandomId }}';
        window.EFURL = '{{ $encodedFileUrl }}';
    </script>

    <style>
        *, body {
            margin: 0;
            padding: 0;
            user-select: none;
        }

        canvas#{{ $canvaRandomId }} {
            user-select: none;
            background: #000000cf;
        }

        body {
            user-select: none;
            background: #050505;
            margin: 0;
            padding: 0;
            opacity: 1;
        }

        .nodoc body {
            background-image: url('{{ $bgImage }}');
            background-size: cover;
            opacity: 0; /* Ajuste a opacidade aqui */
        }

        .nodoc body {
            opacity: 0;
        }
    </style>

    <script>
        window.numericOr = (value, defaultValue = null) => {
            if (value === null) {
              return defaultValue;
            }

            value = value -0;

            return !isNaN(value) ? value : defaultValue;
        }

        function getPageByHash(defaultValue = null) {
            let pattern = new RegExp('(#page=)([0-9]*)', 'i');
            let page = (location.hash.match(pattern) ?? [])[2] || null;

            page = numericOr(page, null);
            defaultValue = numericOr(defaultValue, null);

            return page > 0 ? page : defaultValue;
        }
    </script>

    <style>
        body {
            display: flex;
            justify-content: center; /* Centraliza os itens horizontalmente */
            align-items: center; /* Centraliza os itens verticalmente */
            height: 100vh; /* Faz a altura do body ocupar toda a tela */
            margin: 0; /* Remove margens padrão */
        }

        [data-id="nav-container"] {
            display: flex;
            width: 100%; /* Faz o container ocupar toda a largura */
            justify-content: space-between; /* Espaça os botões para as extremidades */
            padding: 0 20px; /* Adiciona um pouco de espaço nas laterais */
            /* position: absolute; */
            /* bottom: 50vh; */

            position: fixed;
            bottom: 50%;
        }

        /* Estilo dos botões Prev/Next */
        [data-id="nav-container"] button {
            padding: 10px 20px;
            font-size: 1.2rem;
            background: #8080805c;
            color: white;
            border: none;
            border-radius: 1.5rem;
            opacity: 0.5;
            cursor: pointer;
        }

        [data-id="nav-container"] button:hover,
        [data-id="nav-container"] button:active {
            opacity: 1;
        }

        .nodoc [data-id="nav-container"] {
            display: none;
        }
    </style>
    <style class="adjust-screen-brightness" media="screen">
        html.nodoc::before {
            content: " ";
            z-index: 2147483647;
            pointer-events: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.88);
        }
    </style>
</head>
<body>
    <canvas id="{{ $canvaRandomId }}" style="width: 100%; height: auto;"></canvas>
    <div data-id="nav-container">
        <button type="button" data-id="prev">Prev</button>
        <button type="button" data-id="next">Next</button>
    </div>
    <script>
        async function render(pageNumber = null) {
            document.querySelector('html')?.classList?.add('nodoc');
            if (!window.pdfjsLib) {
                return;
            }

            pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ \Vite::asset('resources/js/doc-reader.js') }}";

            {{-- const url = 'https://www.thecampusqdl.com/uploads/files/pdf_sample_2.pdf'; --}}
            const url = window?.EFURL;
            // const pdfData = url;

            const bdata = window._bdata || await (await fetch(url)).text();
            window._bdata = bdata;
            // const pdfData = atob('<?= $b64Data ?? '' ?>');
            const pdfData = window._pdfData || atob(bdata);
            window._pdfData = pdfData;

            pdfjsLib.getDocument({
                    data: pdfData,
                })
                .promise.then(async function(pdf) {
                    if (!window?.canvaBaseID) {
                        window?.debugLog(`Invalid "window.canvaBaseID" value`);
                        return;
                    }

                    const canvas = document.getElementById(window.canvaBaseID);
                    const context = canvas.getContext('2d');
                    const scale = 1.5; // Ajuste a escala conforme necessário
                    pageNumber = numericOr(pageNumber || getPageByHash(1), 1);

                    window._pdf = pdf;
                    const numPages = pdf.numPages;
                    pageNumber = numericOr(pageNumber >= numPages ? numPages : pageNumber, 1);

                    window.location.hash = ['page=', pageNumber].join('');
                    window.pageNumber = pageNumber;

                    canvas.height = pdf.getPage(pageNumber)
                    .then(function(page) {
                        const viewport = page.getViewport({ scale: scale });
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        const renderContext = {
                            canvasContext: context,
                            viewport: viewport
                        };
                        return page.render(renderContext);
                    }).catch(error => window?.debugLog(error));

                    document.querySelector('html')?.classList?.remove('nodoc');
                });
            // Carregar o PDF
        };

        document.addEventListener('DOMContentLoaded', async () => {
            document.querySelector('html')?.classList.add('shadow');
            setTimeout(async () => await render(), 500);

            document.querySelectorAll('[data-id="nav-container"] button')
                ?.forEach(button => {
                    let dataset = button?.dataset || {};
                    let buttonId = dataset?.id || null;

                    if (!['prev', 'next'].includes(buttonId)) {
                        return;
                    }

                    button.addEventListener('click', async (event) => {
                        event.preventDefault();
                        event.stopImmediatePropagation();

                        let target = event.target;
                        if (!target || !target?.dataset?.id) {
                            return;
                        }

                        window.pageNumber = window.pageNumber || null;
                        window.pageNumber = numericOr(window.pageNumber, null) === null ? 1 : window.pageNumber;

                        let toCalc = target?.dataset?.id === 'next' ? 1 : -1;

                        let newNumber = numericOr(window.pageNumber, 1) + toCalc;
                        newNumber = newNumber <= 0 ? 1 : newNumber;

                        if (window.pageNumber === newNumber) {
                            return;
                        }

                        await render(newNumber);
                    });
                });
        });

        // Desativar o menu de contexto
        document.addEventListener("contextmenu", function(event){
            event.preventDefault();
        }, false);

        // Desativar seleção de texto
        document.body.style.userSelect = 'none';
        document.body.style.msUserSelect = 'none';
        document.body.style.mozUserSelect = 'none';
        document.body.style.webkitUserSelect = 'none';
        document.addEventListener("copy", function(event){
            event.preventDefault();
            event.clipboardData.setData('text', '');
            event.clipboardData.setData('text/plain', '');
        }, false);

        document.addEventListener("cut", function(event){
            event.preventDefault();
            event.clipboardData.setData('text', '');
            event.clipboardData.setData('text/plain', '');
        }, false);

        // Exemplo de script para impedir o download e a impressão
        document.addEventListener("contextmenu", function(event){
            event.preventDefault();
        }, false);
    </script>
</body>
</html>
