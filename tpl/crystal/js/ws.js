/*
    This file is part of Hyla
    Copyright (c) 2004-2007 Charles Rincheval.
    All rights reserved

    Hyla is free software; you can redistribute it and/or modify it
    under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License,
    or (at your option) any later version.

    Hyla is distributed in the hope that it will be useful, but
    WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hyla; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$(document).ready(function() {

//    $(".obj_container span[id^='" + file + "']").add(".obj_container span[idref='" + file + "']").find("img.icon").attr('src', 'paf');
//
//
    //$(".obj_container span[id^='hyla-obj--atoto-a+x']").find("img.icon").attr('src', 'paf');
//    $(".obj_container span[id^='hyla-obj--aaa--------yf-a-x']").find("img.icon").attr('src', 'paf');

    $(".file, .dir").draggable({helper: 'clone'});
    $(".dir").droppable({
        accept: ".file, .dir",
        activeClass: 'droppable-active',
        hoverClass: 'droppable-hover',
        
        drop: function(ev, ui) {
            //$(this).append("<br>Dropped!");
            console.warn(ui.draggable);
            $.jGrowl('Move from ' + $.hyla.decodeObj(ui.draggable.attr('id')) + ' to ' + $.hyla.decodeObj($(this).attr('id')), { header: _('Action') });

            // Start move !
            $.getJSON($.hyla.ws.url('hyla.fs.copy'), {
                'file' : $.hyla.decodeObj(ui.draggable.attr('id')),
                'destination' : $.hyla.decodeObj($(this).attr('id'))
            }, function (data) {
                if (!$.hyla.testIfError(data)) {
                    ui.draggable.parents().filter(".obj_container").remove();
                }
            });
        }
        
    });

    $(".edit-rename").editable($.hyla.ws.url('hyla.fs.rename'), {
        id        : 'path',
        name      : 'new_name',
        indicator : '<img src="' + _('DIR_TEMPLATE') + '/img/loading.gif" alt="' + _('Loading') + '">',
        tooltip   : _('Click to rename...'),
        style     : 'inherit',
        callback  : function(value, settings) {
            var stat = eval('(' + value + ')');
            if ($.hyla.testIfError(stat)) {
                this.reset();
                return;
            }

            var objt = jQuery.trim(stat.content);
            $(this).html(objt);
            window.location.href = '/trunk/index.php/obj/' + objt;   // Todo: dégagé l'url en dur...
        }
    });

    $(".edit-description").editable($.hyla.ws.url('hyla.obj.setDescription'), {
        loadurl   : $.hyla.ws.url('hyla.obj.getDescription'),
        loadcallback : function (value, settings, form) {
            var value = $.hyla.evalJSON(value);
            if ($.hyla.testIfError(value)) {
                return this.revert;
            }
            return (value.content) ? value.content : ' ';
        },
        id        : 'file',
        name      : 'description',
        indicator : _('Saving...'),
        tooltip   : _('Click to modify description...'),
        type      : 'autogrow',
        submit    : _('Ok'),
        cancel    : _('Cancel'),
        onblur    : 'ignore',
        autogrow  : {
           lineHeight : 16,
           minHeight  : 32
        },
        select    : true,
        indicator : '<img src="' + _('DIR_TEMPLATE') + '/img/loading.gif" alt="' + _('Loading') + '">',
        placeholder : '',
        callback  : function(value, settings) {
            var stat = eval('(' + value + ')');
            if ($.hyla.testIfError(stat)) {
                this.reset();
                return;
            }
            $(this).html(stat.content);
        }
    });

    $(".dir .icon, .dir.icon").dblclick(function(obj0) {
        $('#testing #destination').load($.hyla.ws.url('hyla.misc.getIcon', { 'format' : 'raw' }), function () {
            $(".plugin_act_misc").click(function(obj1) {
                var file, icon;
                icon = $(obj1.target).attr('src');
                file = $(obj0.target).parents('.obj_container').find('span').attr('id');

                $.getJSON($.hyla.ws.url('hyla.obj.setIcon'), {
                    'file' : $.hyla.decodeObj(file),
                    'icon' : icon
                    },
                function (data) {
                    if (!$.hyla.testIfError(data)) {
                        dbug(file);
                        $.hyla.select(file).find("img.icon").attr('src', icon);
                        $.modal.close();
                    }
                    $('#testing').css('display', 'none');
                });
            })
        }).modal({ close : true, closeTitle : _('Close')});

        $('#testing').css('display', 'block');
    });

    $("#current").dblclick(function() {
        $('#main').load($.hyla.ws.url('hyla.misc.getPluginContent', { 'format' : 'raw', 'file' : _('OBJECT') }), function () {
        });
        //}).modal({ close : true, closeTitle : 'Fermer'});
    });

/*
    $('#testing #destination').load(_('DIR_ROOT') + 'index.php?method=hyla.misc.getGui&format=raw&name=user', function () {
    }).modal({ close : true, closeTitle : 'Fermer'});


    $.getJSON(_('DIR_ROOT') + 'index.php?method=hyla.misc.getGui&format=raw', {
        'name' : 'user',
        },
    function (data) {
        console.warn(data);
    });
*/
});

/*
    - obj_container, comme son nom l'indique, il contient l'objet, c'est lui qui sera supprimé
      en cas de suppression de l'objet
    - le span est l'objet en lui même comprenant une image et son texte associé

    +--------------------------------+
    |         obj_container          |
    |   +------------------------+   |
    |   | span .class [dir|file] |   |
    |   |   +----------------+   |   |
    |   |   | <img> dir,file |   |   |
    |   |   +----------------+   |   |
    |   +------------------------+   |
    +--------------------------------+

    +--------------------------------+
    |    obj_container [dir|file]    |
    |   +------------------------+   |
    |   |           span         |   |
    |   |   +----------------+   |   |
    |   |   | <img> dir,file |   |   |
    |   |   +----------------+   |   |
    |   +------------------------+   |
    +--------------------------------+

 */
