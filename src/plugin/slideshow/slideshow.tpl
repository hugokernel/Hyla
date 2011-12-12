<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>{OBJECT} {TITLE}</title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<link rel="stylesheet" type="text/css" media="screen,projection" title="Défault" href="{DIR_TEMPLATE}/default.css" />

	<!-- BEGIN header_mode_auto -->
	<meta http-equiv="refresh" content="5; url={NEXT_IMAGE}">
	<!-- END header_mode_auto -->

	<script type="text/javascript">

document.onkeyup = keys;

//document.onunload = alert('test');

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

<style type="text/css" media="all">

body {
	margin: 0;
	padding: 0;
}

#pages {
/*	background: #575c4c;*/
	position: absolute;
	bottom: 0px;
	width: 100%;
	margin: 0px;
}

#page {
	text-align: center;
	width: 20%;
	padding: 0;
	margin: 0;
	float: left;
	-moz-border-radius: 10px;
}

#options {
	width: 60%;
	float: left;
	text-align: center;
}

#pages #pcurrent {
	border: 2px solid #999;	
}

</style>

</head>

<body>

<div style="text-align: center;">

	<h2><img src="{FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Infos" /> {NAME}</h2>

	<p class="description">{DESCRIPTION}</p>

	<!-- BEGIN image_cache -->
	<img src="{IMAGE_CACHE}" style="display: none" />
	<!-- END image_cache -->

	<!-- BEGIN image_thumb -->
	<img src="{IMAGE}" id="mySlideshow" />
	<!-- END image_thumb -->

</div>

<br />


<div id="pages">
	<div id="page">
		&nbsp;
	<!-- BEGIN previous_slide -->
		<a href="{PREV_IMAGE}" title="Allez à l'image précédente"> &laquo; Page précédente</a>
	<!-- END previous_slide -->
	</div>

	<div id="options">

		<a href="{URL_STOP}" onclick="javascript:window.close();" title="Arrêter le diaporama"><img src="{DIR_TEMPLATE}/img/edit-undo.png" width="32" height="32" alt="Flèche retour" align="center" /></a>

	<!-- BEGIN mode_manual -->
		<a href="{URL_AUTO}" title="Passer en mode automatique"><img src="src/plugin/slideshow/auto.png" alt="Horloge" align="center" /></a>
		<a href="{URL_MANUAL}" title="Mode manuel"><img src="src/plugin/slideshow/manual.png" alt="Horloge" id="pcurrent" align="center" /></a>
	<!-- END mode_manual -->

	<!-- BEGIN mode_auto -->
		<a href="{URL_AUTO}" title="Mode automatique"><img src="src/plugin/slideshow/auto.png" alt="Horloge" id="pcurrent" align="center" /></a>
		<a href="{URL_MANUAL}" title="Passer en mode manuel"><img src="src/plugin/slideshow/manual.png" alt="Horloge" align="center" /></a>
	<!-- END mode_auto -->
	</div>

	<div id="page">
	<!-- BEGIN next_slide -->
		<a href="{NEXT_IMAGE}" title="Allez à l'image suivante">Page suivante &raquo; </a>
	<!-- END next_slide -->
		&nbsp;
	</div>
</div>
</body>

</html>
