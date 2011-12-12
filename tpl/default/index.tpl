<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>{OBJECT} {TITLE}</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link rel="stylesheet" type="text/css" media="screen,projection" title="Défaut" href="{DIR_TEMPLATE}/default.css" />
<link rel="alternate stylesheet" type="text/css" media="screen" title="Cacher l'arborescence" href="{DIR_TEMPLATE}/no-tree.css" />

<link rel="shortcut icon" type="image/x-icon" href="img/icon.png" />
<link rel="icon" type="image/png" href="img/icon.png"/>

<script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/lib.js"></script>
<script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/styleswitcher.js"></script>

</head>
<body>

<div id="current">
	<h3>
		{OBJECT_URL}
	</h3>
	<span id="download">{DOWNLOAD_COUNT}</span>
</div>

<div id="main">
	{OBJ}
</div>

{TOOLBAR}

<div class="copyright">
	<p>
		{DEBUG} - <a href="http://www.digitalspirit.org/hyla/">Hyla</a> {HYLA_VERSION}, Copyright (c) 2004-2006 Charles Rincheval
	</p>
<!--
	<p>
		<a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
	</p>
-->
</div>


</body>

</html>
