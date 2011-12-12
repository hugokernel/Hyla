
<a href="{DIR_ROOT}"><img src="{DIR_ROOT}img/icon.png" alt="{LANG:Return}" /></a>

<h2>{LANG:Web service information}</h2>

<p>
    {LANG:Hyla provide a lot of web service !}
</p>

<!-- BEGIN group -->
<h3 onclick="swap_layer('layer_{GROUP}');">{GROUP}</h3>

<ul class="jhidden" id="layer_{GROUP}">
<!-- BEGIN method -->
    <li>
        <strong onclick="swap_layer('layer_{GROUP}_{METHOD_NAME}');">{METHOD_NAME}</strong>

        <dl class="jhidden" id="layer_{GROUP}_{METHOD_NAME}">
        <!-- BEGIN param -->
            <dt><b><code>{PARAM_NAME}</code></b> ({PARAM_TYPE})</dt>
            <dd>{PARAM_DESC}</dd>
        <!-- END param -->
        </dl>
   </li>
<!-- END method -->
</ul>
<!-- END group -->

