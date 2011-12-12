
<!-- BEGIN pagination -->
<div class="pagination">
    <!-- BEGIN previous_page -->
        <span id="previous">
            <a href="{PREV_PATH}" title="Objet précédent"> &laquo; <img src="{PREV_FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Icone" /> {OBJ_PREV}</a>
        </span>
    <!-- END previous_page -->
    <!-- BEGIN next_page -->
        <span id="next">
            <a href="{NEXT_PATH}" title="Objet suivant"><img src="{NEXT_FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Icone" /> {OBJ_NEXT} &raquo; </a>
        </span>
    <!-- END next_page -->
</div>
<!-- END pagination -->

<!-- BEGIN description -->
<h4 class="description">{DESCRIPTION}</h4>
<!-- END description -->

<!--{ERROR}-->

<!-- BEGIN with_tree -->
<div id="tree">
    {TREE_ELEM}
</div>

<div id="content">
    {CONTENT}
</div>
<!-- END with_tree -->

<!-- BEGIN no_tree -->
<div id="content-no-tree">
    {CONTENT}
</div>
<!-- END no_tree -->


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
