/*
    Gestion de l'arborscence

    Auteur original :  Rui Nibau (RNB) <rui.nibau@omacronides.com> (http://www.omacronides.com/informatique/web/arbre-hierarchique-js)
    Repris par hugo pour Hyla
 */

/*  Création de l'arbre
 */
function arbre() {
    // Choix de la balise contenant le texte. <strong> par défaut.
    balise = "SPAN";

    // Présentation de l'arbre au départ : déployé ('yes') ou fermé ('no')
    extend = "no";

    // Récupération de tous les arbres de la page
    $("ul#arbre ul").each(function(i) {
        processULEL(this);
    });
}

/*  Analyse de l'arbre
 */
function processULEL(ul, pouf) {

    if (!ul.childNodes || ul.childNodes.length == 0)
        return;

    // Iterate LIs
    for (var itemi = 0; itemi < ul.childNodes.length; itemi++) {

        var item = ul.childNodes[itemi];

        if (item.nodeName == "LI") {

            // Contenu des balises LI
            var a;
            var subul;
            subul = "";
            for (var sitemi = 0; sitemi < item.childNodes.length ; sitemi++) {

                // Enfants des li : balise ou sous-ul
                var sitem = item.childNodes[sitemi];
                switch (sitem.nodeName) {
                    case balise:
                        a = sitem;
                        break;

                    case "UL":
                        subul = sitem;
                        if (extend != "yes" && sitem.className != "tree_current") {
                            sitem.className = 'hide';
                        }

                        a.className = (extend == "yes" || sitem.className == "tree_current") ? 'arbre-minus' : 'arbre-plus';
                        associateEL(a, subul);
                        break;
                }
            }
        }
    }
}

/*  Swicth des noeuds
 */
function associateEL(a, ul) {
    a.onclick = function () {
        this.className = (ul.className == 'hide') ? 'arbre-minus' : 'arbre-plus';
        ul.className = (ul.className == 'hide') ? '' : 'hide';
        return false;
    }
}

/*  Créer l'arbre
 */
window.onload = function() {
    arbre();
}

