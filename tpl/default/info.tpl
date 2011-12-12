
<fieldset>
	<legend>{LANG:General}</legend>

	<img src="{ICON}" alt="Icone" />

		<p>
			Nom : {NAME}
		</p>
		<!-- BEGIN aff_size -->
		<p>
			Taille : {SIZE}
		</p>
		<!-- END aff_size -->
		<p>
			Type : {TYPE}
		</p>
		<!-- BEGIN aff_gen_file -->
		<!-- BEGIN aff_mime -->
		<p>
			Type mime : {MIME}
		</p>
		<!-- END aff_mime -->
		<!-- BEGIN aff_md5 -->
		<p>
			Md5 (<a href="{URL_MD5_CALCULATE}">Calculer</a>) {MD5}
		</p>
		<!-- END aff_md5 -->
		<!-- END aff_gen_file -->

</fieldset>

<fieldset>
	<legend>{LANG:Syndication}</legend>

	<!-- BEGIN aff_file -->
	<ul>
		<li><a href="rss.php?p=obj,{OBJECT}&amp;type=comment" title="Rss 1.0"><img src="img/rss.png" alt="rss" /> {LANG:Rss feed of comments of this file.}</a></li>
	</ul>
	<!-- END aff_file -->

	<!-- BEGIN aff_dir -->
	<ul>
		<li><a href="rss.php?p=obj,{OBJECT}" title="Rss 1.0"><img src="img/rss.png" alt="rss" /> {LANG:Rss feed of file in this dir.}</a></li>
		<li><a href="rss.php?p=obj,{OBJECT}&amp;type=comment" title="Rss 1.0"><img src="img/rss.png" alt="rss" /> {LANG:Rss feed of comment in this dir.}</a></li>
	</ul>
	<!-- END aff_dir -->

	<p class="info">
		{LANG:Remain permanently connected to activity of the site with rss.}
	</p>

</fieldset>
