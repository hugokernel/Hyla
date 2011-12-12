
    <p>
        <a href="{DIR_ROOT}doc/{L10N}/administration.htm#comments" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /></a>
    </p>

    <div id="comment">

        <p>
            {MSG}
        </p>

        {MSG_ERROR}

        <form method="post" name="form_move" action="{FORM_COMMENT_DEL}">

            <!-- BEGIN block_comment_line -->
            <p class="comment_info">
                <input type="checkbox" value="{COMMENT_ID}" name="comment_id[]" />
                <a href="{ADMIN_DEL_COMMENT}" onclick="return confirm('{LANG:Are you sure ?}');" title="{LANG:Deleting a comment}">
                    <img src="{DIR_IMAGE}/delete.png" width="32" height="32" border="0" align="middle" alt="Icone" />
                </a>
                <a href="{PATH_INFO}"><img src="{FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Infos" /></a> {PATH_FORMAT}
                {DATE} - <a href="{MAIL}">{AUTHOR}</a> <a href="{URL}">{URL}</a>
            </p>
            <blockquote class="comment_content">
                <p>
                    {COMMENT}
                </p>
            </blockquote>
            <!-- END block_comment_line -->

            <p>
                <input type="submit" value="{LANG:Delete selected}" onclick="return confirm('{LANG:Are you sure ?}');" />
            </p>

        </form>
    </div>

