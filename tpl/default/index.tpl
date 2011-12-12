<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>{OBJECT} {NAVIG_TITLE}</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" media="screen,projection" title="Défault" href="{FOLDER_TEMPLATE}/default.css" />

<script language="javascript" type="text/javascript" src="{FOLDER_TEMPLATE}/lib.js"></script>

</head>
<body>

{OBJ}

{STRDBG}

<br />
<div class="footer">
	<fieldset>
		<legend>Actions</legend>

		<a href="?aff=lastcomment,{OBJECT}"><img src="{FOLDER_TEMPLATE}/img/comment.png" align="middle" width="32" height="32" alt="Commentaires" /> Derniers commentaires</a> |	
		<a href="?aff=search,{OBJECT}"><img src="{FOLDER_TEMPLATE}/img/Find-Files1.png" align="middle" width="32" height="32" alt="Disque dûr" /> Rechercher</a>
		
		<!-- BEGIN action_comment --><a href="?">Commenter</a><!-- END action_comment -->
		 | <a href="?aff=edit,{OBJECT}"><img src="{FOLDER_TEMPLATE}/img/Editor.png" align="middle" width="32" height="32" alt="Edition" /> Editer</a><!-- BEGIN action_edit --><!-- END action_edit -->
		 | <a href="?aff=upload,{PATH}"><img src="{FOLDER_TEMPLATE}/img/Floppy.png" align="middle" width="32" height="32" alt="Disquette" /> Ajouter un fichier</a><!-- BEGIN action_addfile --><!-- END action_addfile -->
		 | <a href="?act=del,{OBJECT}" onclick="return confirm('Voulez-vous vraiment supprimer l\'objet ?');"><img src="{FOLDER_TEMPLATE}/img/emblem-trash.png" align="middle" width="32" height="32" alt="Disquette" /> Supprimer</a><!-- BEGIN action_del --><!-- END action_del -->
		<!-- BEGIN action_mkdir --> | <a href="?aff=mkdir,{OBJECT}"> Créer un répertoire</a><!-- END action_mkdir -->
		
		<!-- BEGIN aff_login --><a href="?aff=login,{OBJECT}"><img src="{FOLDER_TEMPLATE}/img/Lock-Screen.png" align="middle" width="32" height="32" alt="S'authentifier" /> Se connecter</a><!-- END aff_login -->
		<!-- BEGIN aff_logout --> | <a href="?act=logout,{OBJECT}"><img src="{FOLDER_TEMPLATE}/img/Shutdown.png" align="middle" width="32" height="32" alt="Se déconnecter" /> Déconnecter <i>{CONNECTED_USER_NAME}</i></a><!-- END aff_logout -->
		<!-- BEGIN aff_admin --> | <a href="?aff=admin"><img src="{FOLDER_TEMPLATE}/img/GNOME-Laptop.png" align="middle" width="24" height="24" alt="Entrer dans l'admnistration" /> Administration</a><!-- END aff_admin -->
		
	</fieldset>
</div>


<div class="copyright">
	<p>
		{DEBUG} - <a href="http://www.digitalspirit.org/wiki/projets/ifile">iFile</a> {IFILE_VERSION}, Copyright (c) 2004-2006 Charles Rincheval
	</p>
</div>


</body>

</html>
