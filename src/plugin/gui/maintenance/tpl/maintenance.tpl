
    <p>
        <a href="{DIR_ROOT}doc/{L10N}/administration.htm#maintenance" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /></a>
    </p>

    {MSG_STATUS}

    <div id="form_misc">
        <ul>
            <li>
                <a href="{URL_PAGE_MAINTENANCE_PURGE}">{LANG:Empty the cache}</a>
                <p class="help">
                    {LANG:Delete all cache file, useful if read error appear on archive.}
                </p>
                <p>
                    {PURGE_RAPPORT}
                </p>
            </li>
            <li>
                <a href="{URL_PAGE_MAINTENANCE_SYNC}">{LANG:Run sync}</a>
                <p class="help">
                    {LANG:If you delete file or dir without through Hyla, run sync.}
                </p>
                <p>
                    {SYNC_RAPPORT}
                </p>
            </li>
        </ul>

    </div>

