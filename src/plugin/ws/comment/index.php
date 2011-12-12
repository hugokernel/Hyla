<?php
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

class plugin_ws_comment extends plugin_ws {

    var $bdd;

    /**
     *  Constructor
     */
    function plugin_ws_comment() {
        parent::plugin_ws();

        $this->bdd = plugins::get(PLUGIN_TYPE_DB);
    }

    /**
     *  Add comment
     *  @param  string  $file   File
     *  @param  string  $author Author
     *  @param  string  $mail   Son email
     *  @param  string  $url    Son site
     *  @param  string  $content Le contenu
     */
    function add($file, $author, $mail, $url, $content) {

        $object = $this->obj->getInfo($file, false, false);

        // Test right
        if (!acl::ok($object, AC_EDIT_PLUGIN)) {
            return new tError(__('You do not have the sufficient rights !'), $this);
        }

        // Todo: Test field
/*        if () {

        }
*/
        $id = $this->obj->getId($object->file);
        $qry = "INSERT INTO ".TABLE_COMMENT."
                (comment_obj_id, comment_author, comment_mail, comment_url, comment_date, comment_content)
                VALUES
                ('$id', '$author', '$mail', '$url', '".system::time()."', '$content');";
        if (!$this->bdd->execQuery($qry)) {
            return new tError(__('Error while adding comment !'), $this);
        }

        return $this->bdd->getInsertID();
    }

    /**
     *  Delete one or more comments
     *  @param  int|array   $id Comment id
     */
    function del($id) {
        if (!is_array($id)) {
            $id = array($id);
        }

        $id = implode(',', $id);
        $id = "comment_id IN ($id)";

        $qry = "DELETE
                FROM    ".TABLE_COMMENT."
                WHERE   $id";
        if (!$this->bdd->execQuery($qry)) {
            return new tError(__('Error while remove comment !'), $this);
        }

        return true;
    }

    /*  Renvoie les derniers commentaires...
     */
    function getLast() {

        $tab = array();

        $qry = "SELECT  obj_file, obj_icon, obj_plugin,
                        comment_id, comment_author, comment_mail,
                        comment_url, comment_date, comment_content
                FROM    ".TABLE_OBJECT." INNER JOIN ".TABLE_COMMENT."
                ON      obj_id = comment_obj_id
                WHERE   obj_site_id = '{$this->site_id}'
                ORDER   BY comment_date DESC";
        if (!$var = $this->bdd->execQuery($qry)) {
            return new tError(__('Error while retrieving comment !'), $this);
        }

        for ($i = 0; $res = $this->bdd->nextTuple($var); $i++) {

            // Test acl
            if (!($this->obj->getCUserRights4Path($res['obj_file']) & AC_VIEW)) {
                continue;
            }

            $tab[$i] = new tComment;
            $tab[$i]->object = $res['obj_file'];
            $tab[$i]->icon = $res['obj_icon'];
            $tab[$i]->id = $res['comment_id'];
            $tab[$i]->author = $res['comment_author'];
            $tab[$i]->mail = $res['comment_mail'];
            $tab[$i]->url = $res['comment_url'];
            $tab[$i]->date = $res['comment_date'];
            $tab[$i]->content = $res['comment_content'];

            // Get icon !
            if (is_dir($this->obj->getRoot().$tab[$i]->object)) {
                if ($tab[$i]->object == '/') {
                    $tab[$i]->icon = DIR_IMAGE.'/home.png';
                } else {
                    $tab[$i]->icon = HYLA_ROOT_URL.DIR_PLUGINS_OBJ;
                    if ($res['obj_plugin']) {
                        $tab[$i]->icon .= $res['obj_plugin'];
                    } else {
                        $tab[$i]->icon .= strtolower($this->conf->get('plugin_default_dir'));
                    }
                    $tab[$i]->icon .= '/'.DIR_ICON;
                }
            } else {
                $tab[$i]->icon = get_icon(file::getExtension($tab[$i]->object));
            }
        }

        return $tab;
    }
}

?>
