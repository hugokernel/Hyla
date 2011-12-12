
<!-- BEGIN pagination -->
<div class="pagination">
	<!-- BEGIN previous_page -->
		<span id="previous">
			<a href="{PREV_PATH}" title="Objet précédent"> &laquo; <img src="{PREV_FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Icone" />{OBJ_PREV}</a>
		</span>
	<!-- END previous_page -->
	<!-- BEGIN next_page -->
		<span id="next">
			<a href="{NEXT_PATH}" title="Objet suivant"><img src="{NEXT_FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Icone" />{OBJ_NEXT} &raquo; </a>
		</span>
	<!-- END next_page -->
</div>
<!-- END pagination -->

<h4>{DESCRIPTION}</h4>

<!--{ERROR}-->

<!-- BEGIN tree -->
<div id="tree">
	{TREE_ELEM}
</div>
<div id="content">
	{CONTENT}
</div>
<!-- END tree -->

<!-- BEGIN no_tree -->
<div id="content-no-tree">
	{CONTENT}
</div>
<!-- END no_tree -->

<!-- BEGIN dir_pagination -->
<div class="pagination" style="clear: both;">
	<!-- BEGIN dir_previous_page -->
		<span id="previous">
			<a href="{PREV_PATH}" title="Page précédente"> &laquo; Page précédente</a>
		</span>
	<!-- END dir_previous_page -->
	<!-- BEGIN dir_next_page -->
		<span id="next">
			<a href="{NEXT_PATH}" title="Page suivante">Page suivante &raquo; </a>
		</span>
	<!-- END dir_next_page -->
</div>
<!-- END dir_pagination -->
