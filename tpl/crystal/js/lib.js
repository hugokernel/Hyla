/*
    This file is part of Hyla
    Copyright (c) 2004-2012 Charles Rincheval.
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
    $("*.jhidden").fadeOut("slow");
    show_layer_from_hash();

    // Si on clique sur une ligne dans un tableau
    $(".tab .click").click(
        function() {
            $(this).toggleClass("selected");
        }
    )

    // Table sort
	$(".sortable").tableSorter({
		sortClassAsc: 'ascending',
		sortClassDesc: 'descending',
		headerClass: 'header',
    });
});

$(window).unload(function() {
    if (window.createCookie) {
        createCookie("style", getActiveStyleSheet(), 365);
    }
})

/*  Affiche ou cache un calque
 */
function swap_layer(id) {
    var a = (navigator.appName == "Konqueror") ? null : "slow";
    var layer = $(document.getElementById(id));
    if (layer.is(':visible')) {
        layer.hide(a);
    } else {
        layer.show(a);
    }
}

/*  Affiche tous les calques demandés selon les param passé par l'url (après #)
 */
function show_layer_from_hash() {
    var prm = new Array();
    var tmp = unescape(window.location.hash).substr(1).split(",");
    var a = (navigator.appName == "Konqueror") ? null : "slow";
    var inter;
    for (i = 0; i < tmp.length; i++) {
        if (tmp[i]) {
            var layer = $(document.getElementById(tmp[i]));
            layer.show(a);
        }
    }
    return i;
}

/*  Créé un popup
 */
function popup(url, option) {
    option = option ? option : 'alwaysRaised=yes,dependent=yes,toolbar=no,height=420,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no';
    window.open(url, 'Popup', option);
}

/*  Désactive un élément en fonction de l'état d'une case à cocher
 */
function test(check, id0, id1) {
    elem0 = document.getElementById(check);
    elem1 = document.getElementById(id0);
    if (id1) {
        elem2 = document.getElementById(id1);
        elem2.disabled = (elem1.checked) ? false : true;

        if (elem0.checked) {
            elem1.disabled = false;
        } else {
            elem1.disabled = true;
            elem2.disabled = true;
        }
    } else {
        elem1.disabled = (elem0.checked) ? false : true;
    }
}

/* On récupère l'ID de l'objet
 */
function getID(id) {
    var_nav = (document.getElementById && document.all) ? 'document.all.' + id : 'document.getElementById(\'' + id + '\')';
    return var_nav;
}


/*  Affiche un calque
 */
/*
function show_layer(id) {
    var layer = $(document.getElementById(id));
    var a = (navigator.appName == "Konqueror") ? null : "slow";
    if (!layer.is(':visible'))
        layer.show(a);
}
*/
/*  Cache un calque
 */
/*
function hide_layer(id) {
    var layer = $(document.getElementById(id));
    var a = (navigator.appName == "Konqueror") ? null : "slow";
    if (layer.is(':visible'))
        layer.hide(a);
}
*/

/*
function switchStylestyle(styleName) {
    $('link[@rel*=style][@title]').each(function(i) {
        this.disabled = true;
        if (this.getAttribute('title') == styleName) {
            this.disabled = false;
        }
    });
    createCookie('style', styleName, 365);
}
*/
