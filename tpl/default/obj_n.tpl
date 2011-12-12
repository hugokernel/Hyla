<!-- BEGIN html_header -->
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
<!-- END html_header -->


{TOOLBAR}

<!-- BEGIN title -->
<div id="current">
    <h3 class="obj_container">
        {OBJECT_TITLE}
    </h3>
<!--
    <hr /> 
    <select multiple size="5" style="width: 100%;">
        <option>/toto/a</option>
        <option>/toto/b</option>
        <option>/toto/zz</option>
    </select>
-->
    <span id="download">{DOWNLOAD_COUNT}</span>
</div>
<!-- END title -->

<!--
<div id="testing" style="display: none; width: auto; margin: 20%; background: white; border: 1px solid #CCC; margin: 5px; padding: 5px; position: fixed; top: 1px;">
    <div id="destination">
    </div>
</div>
-->

<!-- BEGIN main -->
<div id="main">
<!-- END main -->

<!-- BEGIN file_pagination -->
<div class="pagination">
    <!-- BEGIN file_previous_page -->
        <span id="previous">
            <a href="{PREV_PATH}" title="Objet précédent"> &laquo; <img src="{PREV_FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Icone" /> {OBJ_PREV}</a>
        </span>
    <!-- END file_previous_page -->
    <!-- BEGIN file_next_page -->
        <span id="next">
            <a href="{NEXT_PATH}" title="Objet suivant"><img src="{NEXT_FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Icone" /> {OBJ_NEXT} &raquo; </a>
        </span>
    <!-- END file_next_page -->
</div>
<!-- END file_pagination -->

<h4 class="edit-description description" id="{OBJECT}">{DESCRIPTION}</h4>

<!--{ERROR}-->


<!-- BEGIN tree -->
    <div id="tree">
        {TREE_ELEM}
    </div>
    <div id="content">

        <form method="post" action="{URL_ADD_BASKET}">
            {CONTENT}
<!-- END tree -->


<!-- BEGIN no_tree -->
    <div id="content-no-tree">

        <form method="post" action="{URL_ADD_BASKET}">
            {CONTENT}
<!-- END no_tree -->

            <!-- BEGIN actions -->
            <div id="action-basket">
                Sélection :
                <select name="action">
                    <!-- BEGIN action -->
                    <option value="{ACTION_VALUE}">{ACTION_NAME}</option>
                    <!-- END action -->
                </select>
                <input type="submit" value="Go !" />
                </div>
            <!-- END actions -->
        </form>

    <!-- BEGIN sort -->
    <div id="action-sort">
        <form method="post" name="dir_form" action="?">
            <select name="param[]"><!-- onchange="submit();"-->
                <option value="sort:-1" {SELECT_SORT}>&nbsp;&laquo; Ordre de la configuration &raquo;</option>
                <option value="sort:0" {SELECT_SORT_0}>&nbsp;Sans tri</option>
                <option value="sort:1" {SELECT_SORT_1}>&nbsp;Alphabétique A / Z</option>
                <option value="sort:2" {SELECT_SORT_2}>&nbsp;Alphabétique Z / A</option>
                <option value="sort:3" {SELECT_SORT_3}>&nbsp;Extensions   A / Z</option>
                <option value="sort:4" {SELECT_SORT_4}>&nbsp;Extensions   Z / A</option>
                <option value="sort:5" {SELECT_SORT_5}>&nbsp;Catégories   A / Z</option>
                <option value="sort:6" {SELECT_SORT_6}>&nbsp;Catégories   Z / A</option>
                <option value="sort:7" {SELECT_SORT_7}>&nbsp;Taille - / +</option>
                <option value="sort:8" {SELECT_SORT_8}>&nbsp;Taille + / -</option>
            </select>
            <label for="ffirst">
                <input type="checkbox" name="param[]" id="ffirst" value="ffirst:ok" {FFIRST_CHECKED} />
                Les dossiers en premier
            </label>
            <label for="grp">
                <input type="checkbox" name="param[]" id="grp" value="grp:ok" {GRP_CHECKED} />
                Grouper par critère de tri
            </label>
            <input type="submit" name="" value="Envoyer !" />
        </form>
    </div>
    <!-- END sort -->

</div>


<!-- BEGIN dir_pagination -->
<div class="pagination">
    <div id="previous">
        &nbsp;
        <!-- BEGIN dir_previous_page -->
        <a href="{PREV_PATH}" title="Page précédente"> &laquo; Page précédente</a>
        <!-- END dir_previous_page -->
    </div>

    <!-- BEGIN dir_page -->
    <div id="num_page">
        Pages :
        <!-- BEGIN dir_page_num -->
        <a href="{PAGE_URL}"<!-- BEGIN dir_page_num_cur --> id="current_page"<!-- END dir_page_num_cur -->>{PAGE_NUM}</a>
        <!-- END dir_page_num -->
    </div>
    <!-- END dir_page -->

    <div id="next">
        &nbsp;
        <!-- BEGIN dir_next_page -->
        <a href="{NEXT_PATH}" title="Page suivante">Page suivante &raquo; </a>
        <!-- END dir_next_page -->
    </div>
</div>
<!-- END dir_pagination -->

{COMMENTS}


<!-- BEGIN main_end -->
</div>
<!-- END main_end -->

<!-- BEGIN html_footer -->
</body>

</html>
<!-- END html_footer -->
