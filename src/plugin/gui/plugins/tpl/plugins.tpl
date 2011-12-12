
 <div id="tabs_gui_content_container">
 <blockquote class="info">
 <strong>{LANG:Plugin Action} :</strong>&nbsp;
 <img src="{LEGEND_URL}enable.png" alt="{LANG:Enable}" width="16" height="16" style="border:0px"/> {LANG:Enable}
 <img src="{LEGEND_URL}disable.png" alt="{LANG:Disable}" width="16" height="16" style="border:0px"/> {LANG:Disable}
 <img src="{LEGEND_URL}nodisable.png" alt="{LANG:Nodisable}" width="16" height="16" style="border:0px"/> {LANG:Nodisable}     
    </blockquote>
 <ul class="tabs_gui_content">
 <!-- BEGIN type_plugin -->
 <li><a href="{URL_PLUGIN}" title="{PLUGIN_DESCRIPTION}" <!-- BEGIN type_plugin_current -->class="active"<!-- END type_plugin_current -->>
 {PLUGIN_NAME}</a></li>
 <!-- END type_plugin -->
 </ul>


 <table class="tab" summary="{LANG:Plugin Lists}" style="border-collapse: collapse; border: 10px">
        <thead>
          
            <tr>
                <th width="5%"></th>
                <th width="70%">{LANG:Description}</th>
                <th width="10%">{LANG:Author}</th>
                <th width="10%">{LANG:Target}</th>  
                <th width="5%">{LANG:Action}</th>
            </tr>
            
        </thead>
        <tbody>
 
   <!-- BEGIN line_content -->
            <tr>
                <td align="center"><img src="{PLUGINS_ICON}" alt="{PLUGINS_NAME}" width="32" height="32" style="border:0px"/></td>
                <td align="left"><strong>&nbsp;{PLUGINS_NAME}</strong> <small><i>{LANG:Version} {PLUGINS_VERSION}</i></small><br />
                &nbsp;- <small>{PLUGINS_DESCRIPTION}</small> 
                </td>
                 <td align="center"><small> {PLUGINS_AUTHOR} </small></td>
                <td align="center"> {PLUGINS_TARGET} </td>
                <td align="center"> <img src="{PLUGINS_ICON_STATUS}" alt="{PLUGINS_ENABLED}" style="border:0px"/> </td>
            </tr>
   <!-- END line_content -->
           
  
    </tbody>
    </table>
         
    </div>

