<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">

<head>

<title>{OBJECT_TITLE} {TITLE}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="index,follow" />

{HEADER}

{STYLESHEET_PLUGIN}

{STYLESHEET}

<link rel="icon" type="image/png" href="{DIR_ROOT}img/icon.png" />
<link rel="shortcut icon" href="{DIR_ROOT}img/icon.ico"/>

<!-- BEGIN rss_obj -->
<link rel="alternate" type="application/rss+xml" title="Fil rss de l'objet courant" href="{URL_RSS}" />
<!-- END rss_obj -->
<!-- BEGIN rss_comment -->
<link rel="alternate" type="application/rss+xml" title="Fil rss des commentaires" href="{URL_RSS_COMMENT}" />
<!-- END rss_comment -->

<script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/jquery.tablesorter.js"></script>
<script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/styleswitcher.js"></script>
<script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/tree.js"></script>
<script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/lib.js"></script>

</head>

<body>

<div id="current">
    <h3>
        {OBJECT_URL}
    </h3>
    <span id="download">{DOWNLOAD_COUNT}</span>
</div>

<div id="main">
    {CONTENT}
</div>

{TOOLBAR}

<div class="copyright">
    <p>
        <a href="#">{LANG:Top of page}</a> - <a href="{DIR_ROOT}doc/{L10N}/index.htm" onclick="popup(this.href); return false;">Documentation</a>
    </p>
    <p>
        {DEBUG} - <a href="http://www.hyla-project.org/">Hyla</a> {HYLA_VERSION},
        Copyright (c) 2004-2012 <a href="http://www.digitalspirit.org/">Charles Rincheval</a>
    </p>
<!--
    <p>
        <a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
    </p>
-->
</div>


</body>

</html>
