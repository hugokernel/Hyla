<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>{OBJECT} {TITLE}</title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="robots" content="index,nofollow" />

{STYLESHEET}

<style type="text/css">
@import "{PATH_2_PLUGIN}/default.css";
</style>

    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/styleswitcher.js"></script>

    <!-- BEGIN header_mode_auto -->
    <meta http-equiv="refresh" content="{TIMEOUT}; url={NEXT_IMAGE}">
    <!-- END header_mode_auto -->

    <script type="text/javascript">

document.onkeyup = keys;

// Pris du projet S5
function keys(key) {
    if (!key) {
        key = event;
        key.which = key.keyCode;
    }
    if (key.which == 84) {
        toggle();
        return;
    }

    switch (key.which) {
        case 10: // return
            break;

        case 13: // enter
        case 32: // spacebar
        case 34: // page down
        case 39: // rightkey
        case 40: // downkey
            var url_next = '{NEXT_IMAGE}';
            url_next = url_next.replace(/\&amp\;/g, "&");
            window.location.href = url_next;
            break;
        case 33: // page up
        case 37: // leftkey
        case 38: // upkey
            var url_prev = '{PREV_IMAGE}';
            url_prev = url_prev.replace(/\&amp\;/g, "&");
            window.location.href = url_prev;
            break;
        case 36: // home
                break;
        case 35: // end
            break;
        case 67: // c
            break;
    }

    return false;
}

    </script>

</head>

<body>

<p style="text-align: center">
    {MESSAGE}
</p>

<!-- BEGIN aff -->
<div style="text-align: center;">

    <h2><a href="{URL_DOWNLOAD}"><img src="{FILE_ICON}" class="icon" align="middle" alt="Infos" /> {NAME}</a></h2>

    <!-- BEGIN description -->
    <h4 class="description">{DESCRIPTION}</h4>
    <!-- END description -->

    <!-- BEGIN image_thumb -->
    <img src="{IMAGE}" id="mySlideshow" alt="{DESCRIPTION}" />
    <!-- END image_thumb -->

    <!-- BEGIN image_cache -->
    <img src="{IMAGE_CACHE}" style="display: none" alt="Image cachée" />
    <!-- END image_cache -->

</div>

<div id="pages">
    <div class="page">
        &nbsp;
    <!-- BEGIN previous_slide -->
        <a href="{PREV_IMAGE}" title="Allez à l'image précédente"> &laquo; Page précédente</a>
    <!-- END previous_slide -->
    </div>

    <div id="options">

        <a href="{URL_STOP}" onclick="javascript:window.close();" title="Arrêter le diaporama"><img src="{PATH_2_PLUGIN}/return.png" width="32" height="32" alt="Flèche retour" align="middle" /></a>

        <!-- BEGIN mode_manual -->
        <a href="{URL_AUTO}" title="Passer en mode automatique"><img src="{PATH_2_PLUGIN}/auto.png" alt="Horloge" align="middle" /></a>
        <a href="{URL_MANUAL}" title="Mode manuel"><img src="{PATH_2_PLUGIN}/manual.png" alt="Horloge" id="pcurrent" align="middle" /></a>
        <!-- END mode_manual -->

        <!-- BEGIN mode_auto -->
        <a href="{URL_AUTO}" title="Mode automatique"><img src="{PATH_2_PLUGIN}/auto.png" alt="Horloge" id="pcurrent" align="middle" /></a>
        <a href="{URL_MANUAL}" title="Passer en mode manuel"><img src="{PATH_2_PLUGIN}/manual.png" alt="Horloge" align="middle" /></a>
        <!-- END mode_auto -->
    </div>

    <div class="page">
    <!-- BEGIN next_slide -->
        <a href="{NEXT_IMAGE}" title="Allez à l'image suivante">Page suivante &raquo; </a>
    <!-- END next_slide -->
        &nbsp;
    </div>
</div>
<!-- END aff -->

</body>

</html>
