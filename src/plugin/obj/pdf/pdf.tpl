
<script type="text/javascript" src="{PATH_2_PLUGIN}pdf.js"></script>
<script type="text/javascript">

'use strict';

PDFJS.workerSrc = "{PATH_2_PLUGIN}pdf.js";

var pdf = null;
var page = null;
var canvas = null;
var context = null;
var current_page = 1;

function redraw(page_num) {
    canvas.width = canvas.width;
    page = pdf.getPage(page_num);
    page.startRendering(context, function() {
        current_page = page_num;
        $('#pdf-page-number').html(current_page);
    });
}

function pagination(page) {

    if (!page) {
        var hash = document.location.hash.substring(1).split('=');
        if (hash[0] == 'page') {
            if (hash[1] && hash[1] < pdf.numPages) {
                redraw(hash[1]);
                $('#pdf-page-number').html(current_page);
                return;
            }
        }
    }

    $('#pdf-page-previous, #pdf-page-next').removeClass('disabled');
    if (current_page == 1) {
        $('#pdf-page-previous').addClass('disabled');
    }

    if (pdf && current_page == pdf.numPages) {
        $('#pdf-page-next').addClass('disabled');
    }

    document.location.hash = 'page=' + current_page;
}

$(document).ready(function() {

    $('#with-js').show();
    $('#without-js').hide();

    PDFJS.getPdf('{URL_CURRENT_OBJ}', function(data) {
        pdf = new PDFJS.PDFDoc(data);
        page = pdf.getPage(current_page);
        var scale = 1.5;

        canvas = document.getElementById('pdf-render');
        context = canvas.getContext('2d');
        canvas.height = page.height * scale;
        canvas.width = page.width * scale;

        page.startRendering(context, function() {
            pagination();
        });

        $('#pdf-page-total').html(pdf.numPages);
    });

    $(window).bind('hashchange', function() {
        pagination();
    });

    $('#pdf-page-previous').click(function() {
        if (current_page > 1) {
            redraw(--current_page);
        }

        pagination(current_page);

        return false;
    });

    $('#pdf-page-next').click(function() {
        if (current_page < pdf.numPages) {
            redraw(++current_page);
        }

        pagination(current_page);

        return false;
    });
});

</script>

<div class="plugin">

    <div id="with-js" style="display: none;">
        <div class="pdf-page">
            <a id="pdf-page-previous" href="#">
                Page précédente
            </a>

            Page
            <span id="pdf-page-number">
                1
            </span>
            sur
            <span id="pdf-page-total">
                {PAGE_TOTAL}
            </span>

            <a id="pdf-page-next" href="#">
                Page suivante
            </a>
        </div>

        <br />

        <canvas id="pdf-render" style="border:1px solid black;"/>
    </div>

    <div id="without-js">
        Pour utiliser ce plugin, vous devez avoir le javascript activé !
    </div>

</div>

