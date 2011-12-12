
<div style="padding: 10px;">

    <h2>Administration v2</h2>

    <ul style="float: left; width: 15%; padding: 0; list-style-type: none;">
        <!-- BEGIN plugin -->
        <li>
            <a href="{URL_PLUGIN}" title="{PLUGIN_DESCRIPTION}" <!-- BEGIN plugin_current -->id="current"<!-- END plugin_current -->>
            <img src="{PLUGIN_ICON}" align="middle" width="32" height="32" alt="Maison" />
            {PLUGIN_NAME}
            </a>
        </li>
        <!-- END plugin -->
    </ul>


    <!--
        <a href="{ADMIN_PAGE}"><img src="{DIR_IMAGE}/home.png" align="middle" width="32" height="32" alt="Maison" /> Accueil</a>
        <a href="{ADMIN_PAGE_CONF}" title="Configuration générale de Hyla"><img src="{DIR_IMAGE}/config-gen.png" align="middle" width="32" height="32" alt="Outils" /> Configuration</a>
        <a href="{ADMIN_PAGE_USERS}" title="Ajout, modification, suppression des utilisateurs"><img src="{DIR_IMAGE}/users.png" align="middle" width="32" height="32" alt="Silhouettes" /> Utilisateurs</a>
        <a href="{ADMIN_PAGE_GROUPS}" title="Ajout, modification, suppression des groupes"><img src="{DIR_IMAGE}/groups.png" align="middle" width="32" height="32" alt="Valises" /> Groupes</a>
        <a href="{ADMIN_PAGE_RIGHTS}" title="Gestion des droits"><img src="{DIR_IMAGE}/rights.png" align="middle" width="32" height="32" alt="Les droits" /> Droits</a>
        <a href="{ADMIN_PAGE_COMMENT}" title="Visualisation des commentaires"><img src="{DIR_IMAGE}/comment.png" align="middle" width="32" height="32" alt="Commentaires" /> Commentaires</a>
        <a href="{ADMIN_PAGE_ANON}" title="Gestion des fichiers envoyés de manière anonymes"><img src="{DIR_IMAGE}/anonymous-file.png" align="middle" width="32" height="32" alt="Poubelle" />&nbsp;Fichiers&nbsp;anonymes</a>
        <a href="{ADMIN_PAGE_MAINTENANCE}" title="Maintenance générale de Hyla"><img src="{DIR_IMAGE}/maintenance.png" align="middle" width="32" height="32" alt="Truelle" /> Maintenance</a>
        <a href="{ADMIN_PAGE_PLUGINS}" title="Maintenance des Plugins de Hyla"><img src="{DIR_IMAGE}/plugins.png" align="middle" width="32" height="32" alt="plugins" /> Plugins</a>
    -->
    
    <div style="float: left; width: 80%;">
    
        {CURRENT_DESCRIPTION}

        {MSG_ERROR}

        {CONTENT}
        
    </div>
    
</div>
