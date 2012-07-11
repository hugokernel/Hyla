<?php
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

if (!defined('PAGE_HOME'))
    header('location: ../index.php');

require 'src/inc/string.class.php';
require 'src/inc/plugin_obj.class.php';
require 'src/inc/cache.class.php';


/*  Gestion des paramètres de visualisation, tri...
 */
$param = @$_REQUEST['param'];

$sort = (isset($_SESSION['sess_sort'])) ? $_SESSION['sess_sort'] : $conf['sort_config'];
$grp = (isset($_SESSION['sess_grp'])) ? $_SESSION['sess_grp'] : $conf['group_by_sort'];
$ffirst = (isset($_SESSION['sess_ffirst'])) ? $_SESSION['sess_ffirst'] : null;

if (isset($param)) {
    $tab = array(
            '-1'=> -1,
            '0' => SORT_DEFAULT,
            '1' => SORT_NAME_ALPHA,
            '2' => SORT_NAME_ALPHA_R,
            '3' => SORT_EXT_ALPHA,
            '4' => SORT_EXT_ALPHA_R,
            '5' => SORT_CAT_ALPHA,
            '6' => SORT_CAT_ALPHA_R,
            '7' => SORT_SIZE,
            '8' => SORT_SIZE_R,
            '9' => SORT_DATE,
            '10' => SORT_DATE_R,
            );
    $grp = $ffirst = $sort = 0;
    foreach ($param as $occ) {
        list($act, $value) = explode(':', $occ);
        if ($act == 'sort') {
            if ($value == -1)
                $sort = $conf['sort_config'];
            if ($value > 0)
                $sort = (isset($tab[$value]) ? $tab[$value] : $sort);
            continue;
        }

        if ($act == 'grp' && $value == 'ok') {
            $grp = 1;
            continue;
        }

        if ($act == 'ffirst' && $value == 'ok') {
            $ffirst = 1;
            continue;
        }
    }

    if ($ffirst)
        $sort |= SORT_FOLDER_FIRST;
}

$_SESSION['sess_sort'] = $sort;
$_SESSION['sess_grp'] = $grp;
$_SESSION['sess_ffirst'] = $ffirst;

/*  Gestion des actions
 */
switch ($url->getParam('act', 0)) {

    //  On se déloggue !
    case 'logout':
        $auth->logout();

        // Redirection vers le bon endroit
        if ($obj->getUserRights4Path($cobj->path, ANONYMOUS_ID) & AC_VIEW) {
            $new_url = $url->linkToCurrentObj();
        } else {
            $new_url = ($obj->getUserRights4Path('/', ANONYMOUS_ID) & AC_VIEW) ? $url->linkToObj('/') : $url->linkToPage('login');
        }
        redirect('', $new_url, __('You are now disconnected'));
        system::end();
        break;

    //  Password modification
    case 'changepassword':
        acl_test(ADMINISTRATOR_ONLY);

        if ($cuser->id != ANONYMOUS_ID) {
            if (empty($_POST['user_password']) || empty($_POST['user_password_bis'])) {
                $msg = view_error(__('All the fields must be filled'));
            } else if ($_POST['user_password'] != $_POST['user_password_bis']) {
                $msg = view_error(__('Passwords are different'));
            } else if (strlen($_POST['user_password']) < MIN_PASSWORD_SIZE) {
                $msg = view_error(__('Password must have at least %s characters !', MIN_PASSWORD_SIZE));
            } else {
                // Ok, password modification !
                $usr = new users();
                if ($usr->setPassword($cuser->id, $_POST['user_password'])) {
                    $msg = view_status(__('Password changed !'));
                }
                unset($usr);
            }

        }
        break;

    //  Un commentaire à été envoyé
    case 'addcomment':
        acl_test(AC_ADD_COMMENT);

        $_POST['cm_author'] = strip_tags($_POST['cm_author']);
        $_POST['cm_author'] = ($cuser->id != ANONYMOUS_ID) ? $cuser->name : $_POST['cm_author'];

        $val = null;

        $usr = new users();
        $ret = $usr->testLogin($_POST['cm_author']);

        if ($ret == -1) {
            $msg_error = __('The name is invalid !');
        } else if (!$ret && $cuser->id == ANONYMOUS_ID) {
            $msg_error = __('User already exists !');
        } else {
            $val = verif_value(array(
                    $_POST['cm_author']     =>  __('The author field is required'),
                    $_POST['cm_content']    =>  __('The message field is required')), $msg_error);
        }

        unset($usr);

        // Les champs indispensables sont remplis ?
        if ($val) {
            if ($_POST['cm_site'] == 'http://')
                $_POST['cm_site'] = null;

            $_POST['cm_mail'] = string::format($_POST['cm_mail'], false);
            $_POST['cm_site'] = string::format($_POST['cm_site'], false);
            $_POST['cm_content'] = string::format($_POST['cm_content']);

            $id = $obj->addComment($_POST['cm_author'], $_POST['cm_mail'], $_POST['cm_site'], $_POST['cm_content']);

            // On reste en phase avec l'objet courant !
            $csize = sizeof($cobj->info->comment);
            $cobj->info->nbr_comment++;
            $cobj->info->comment[$csize] = new tComment;
            $cobj->info->comment[$csize]->id = $id;
            $cobj->info->comment[$csize]->author = $_POST['cm_author'];
            $cobj->info->comment[$csize]->mail = $_POST['cm_mail'];
            $cobj->info->comment[$csize]->url = $_POST['cm_site'];
            $cobj->info->comment[$csize]->content = stripslashes($_POST['cm_content']);
            $cobj->info->comment[$csize]->date = system::time();

            $id = null;
            $_POST['cm_content'] = null;
        }
        break;

    //  Modification description
    case 'setdescription':
        acl_test(AC_EDIT_DESCRIPTION);

        $description = string::format($_POST['description'], true, true);
        $obj->setDescription($description);

        $cobj->info->description = stripslashes($description);

        if (isset($_POST['redirect']) && $_POST['redirect'] == '1') {
            redirect($cobj->file, $url->linkToCurrentObj(), __('You will be redirected towards the object !'));
            system::end();
        }
        break;

    //  Modification plugin courant
    case 'setplugin':
        acl_test(AC_EDIT_PLUGIN);

        if ($cobj->type = TYPE_DIR) {
            $plugin_name = $_POST['pg_name'];
            $plugin_name = ($plugin_name == 'default') ? null : $plugin_name;

            if ($obj->setPlugin($plugin_name)) {
                $cobj->info->plugin = $plugin_name;
                if ($cobj->type == TYPE_DIR) {
                    if (!$cobj->icon) {
                        echo $cobj->icon,'<br>';
                        $icon = DIR_PLUGINS_OBJ.$plugin_name.'/'.DIR_ICON;
                        $cobj->icon = REAL_ROOT_URL.(($plugin_name) ? (file_exists($icon) ? $icon : get_icon('.')) : DIR_PLUGINS_OBJ.$conf['plugin_default_dir'].'/'.DIR_ICON);
                        echo $cobj->icon;
                    }
                }
            }

            if (isset($_POST['redirect']) && $_POST['redirect'] == '1') {
                redirect($cobj->file, $url->linkToCurrentObj(), __('You will be redirected towards the object !'));
                system::end();
            }
        }
        break;

    //  Modification image courante
    case 'seticon':
        acl_test(AC_EDIT_ICON);

        if ($cobj->type = TYPE_DIR) {
            $icon_name = ($_POST['icon_name'] == 'default') ? null : htmlentities($_POST['icon_name'], ENT_QUOTES);
            $icon_name = file::getRealFile($icon_name, DIR_ROOT.DIR_IMG_PERSO);

            $icon_name = ($icon_name) ? DIR_IMG_PERSO.$icon_name : null;
            $cobj->icon = $obj->setIcon($icon_name);

            if (isset($_POST['redirect']) && $_POST['redirect'] == '1') {
                redirect($cobj->file, $url->linkToCurrentObj(), __('You will be redirected towards the object !'));
                system::end();
            }
        }
        break;

    //  Ajoute un droit
    case 'addrights':
        acl_test(ADMINISTRATOR_ONLY);

        $right = $obj->calculateRights($_POST['rgt_value']);

        if (isset($_POST['rgt_users'])) {
            foreach ($_POST['rgt_users'] as $user) {
                $obj->addRight($cobj->file, $user, $right);
            }
        }
        break;

    //  Édite un droit
    case 'editrights':
        acl_test(ADMINISTRATOR_ONLY);

        $right = 0;
        if (isset($_POST['rgt_value'])) {
            foreach ($_POST['rgt_value'] as $perm) {
                $right |= $perm;
            }
        }
        $obj->setRight($cobj->file, $_POST['rgt_user'], $right);
        break;

    //  Supprime un droit
    case 'delrights':
        acl_test(ADMINISTRATOR_ONLY);

        if (isset($_POST['rgt_user'])) {
            foreach ($_POST['rgt_user'] as $user_id) {
                $obj->delRight($cobj->file, $user_id);
            }
        }
        break;

    //  Renommage
    case 'rename':
        acl_test(AC_RENAME);

        $new_name = stripslashes($_POST['rn_newname']);
        $new_name = get_2_fs_charset($new_name);

        // Le Renommage d'une archive n'est pas possible
        if ($cobj->type == TYPE_ARCHIVED) {
            redirect($cobj->file, $url->linkToCurrentObj(), __('Not implemented !'));
            system::end();
        }

        // On ne peut pas renommer la racine
        if ($cobj->file == '/') {
            redirect(__('Error'), $url->linkToCurrentObj(), __('Impossible to rename the root'));
            system::end();
        }

        // Il ne doit pas y avoir de caractère interdit !
        if (string::test($new_name, UNAUTHORIZED_CHAR)) {
            $msg_error = __('There are an invalid char in the file name, unauthorized char are : %s', UNAUTHORIZED_CHAR);
            $url->setParam('aff', 1, 'rename');
            break;
        }

        // On vérifie tout d'abord si l'objet de destination existe déjà
        $newname = ($cobj->type == TYPE_FILE) ? $cobj->path.$new_name : file::downPath($cobj->path).$new_name;
        if (file_exists(FOLDER_ROOT.$newname) && $cobj->realpath != FOLDER_ROOT.$newname.'/') {
            $msg_error = is_dir(FOLDER_ROOT.$new_name) ? __('The dir already exists !') : __('The file already exists !');
            $url->setParam('aff', 1, 'rename');
            break;
        }

        if ($obj->rename($cobj->file, $new_name)) {
            $msg = view_status(__('The objet was renamed !'));
            if (isset($_POST['rn_redirect']) && $_POST['rn_redirect'] == '1') {
                $var = $newname;
            } else {
                $var = ($cobj->type == TYPE_DIR) ? file::downPath($cobj->path) : $cobj->path;
            }
        } else {
            $msg = view_error(__('An error occured during rename !'));
            $var = $cobj->file;
        }

        redirect($cobj->file, $url->linkToObj($var), $msg);
        system::end();
        break;

    //  Copie
    case 'copy':
        acl_test(AC_COPY);

        //  Si le répertoire de destination n'est pas valable
        $dest_dir = stripslashes($_POST['cp_destination']);
        $dest_dir = get_2_fs_charset($dest_dir);
        $dest_dir = file::getRealDir($dest_dir, FOLDER_ROOT);
        if (!$dest_dir) {
            redirect(__('Error'), $url->linkToCurrentObj(), __('An error occured during copy !'));
            system::end();
        }

        // On vérifie que l'on essaie pas de copier le répertoire sur lui même !
        if ($cobj->type == TYPE_DIR && $dest_dir.'/' == $cobj->file) {
            $msg_error = __('Impossible to copy dir on him !');
            $url->setParam('aff', 1, 'copy');
            break;
        }

        // On vérifie tout d'abord si l'objet de destination existe déjà
        if ($cobj->type == TYPE_FILE) {
            $file = $cobj->file;
            $base = FOLDER_ROOT;
            $dest = $dest_dir.$cobj->name;
        } else if ($cobj->type == TYPE_DIR) {
            $file = $cobj->path;
            $base = FOLDER_ROOT;
            $dest = $dest_dir.$cobj->name;
        } else if ($cobj->type == TYPE_ARCHIVED) {
            $file = basename($cobj->target);
            $base = file::dirName(get_real_directory());
            $dest = $dest_dir.$cobj->target;
        }

        // L'utilisateur a-t-il le droit de copier dans le dossier de destination ?
        $right = $obj->getCUserRights4Path($dest);
        if (!($right & AC_COPY)) {
            $msg_error = __('You don\'t have copy right for destination dir !');
            $url->setParam('aff', 1, 'copy');
            break;
        }

        // On test si l'objet final existe déjà ou non
        if (file_exists(FOLDER_ROOT.$dest)) {
            $msg_error = is_dir(FOLDER_ROOT.$dest) ? __('The dir already exists !') : __('The file already exists !');
            $url->setParam('aff', 1, 'copy');
            break;
        }

        // Sinon, on copie !
        if ($ret = $obj->copy($file, $dest, $base)) {
            $msg = __('%s objets was copied !', $ret);
            $msg = view_status($msg);

            if (isset($_POST['cp_redirect']) && $_POST['cp_redirect'] == '1') {
                $var = $dest;
            } else {
                $var = $cobj->path;
            }
        } else {
            $msg = __('An error occured during copy !');
            $msg = view_error($msg);
            $var = $cobj->file;
        }

        redirect($cobj->file, $url->linkToObj($var), $msg);
        system::end();
        break;

    //  Déplacement
    case 'move':
        acl_test(AC_MOVE);

        // Le déplacement d'une archive n'est pas possible
        if ($cobj->type == TYPE_ARCHIVED) {
            redirect($cobj->file, $url->linkToCurrentObj(), __('Not implemented !'));
            system::end();
        }

        // On ne peut pas déplacer la racine
        if ($cobj->file == '/') {
            redirect(__('Error'), $url->linkToCurrentObj(), __('Impossible to move the root'));
            system::end();
        }

        //  Si le répertoire de destination n'est pas valable
        $dest_dir = stripslashes($_POST['mv_destination']);
        $dest_dir = get_2_fs_charset($dest_dir);
        $dest_dir = file::getRealDir($dest_dir, FOLDER_ROOT);
        if (!$dest_dir) {
            redirect(__('Error'), $url->linkToCurrentObj(), __('An error occured during move !'));
            system::end();
        }

        // L'utilisateur a-t-il le droit de copier dans la dossier de destination ?
        $right = $obj->getCUserRights4Path($dest_dir);
        if (!($right & AC_MOVE)) {
            $msg_error = __('You don\'t have move right for destination dir !');
            $url->setParam('aff', 1, 'move');
            break;
        }

        // On vérifie tout d'abord si l'objet de destination existe déjà
        $dest_dir = ($cobj->type == TYPE_FILE) ? $dest_dir.$cobj->name : $dest_dir.$cobj->name.'/';

        // On test si l'objet final existe déjà ou non
        if (file_exists(FOLDER_ROOT.$dest_dir)) {
            $msg_error = is_dir(FOLDER_ROOT.$dest_dir) ? __('The dir already exists !') : __('The file already exists !');
            $url->setParam('aff', 1, 'move');
            break;
        }

        // On vérifie que l'on essaie pas de copier le répertoire sur lui même !
        if ($cobj->type == TYPE_DIR && ($dest_dir.'/' == $cobj->file || file::isInPath($dest_dir, $cobj->path))) {
            $msg_error = __('Impossible to move dir on him !');
            $url->setParam('aff', 1, 'move');
            break;
        }

        // Sinon, on déplace !
        if ($ret = $obj->move($cobj->file, $dest_dir, FOLDER_ROOT)) {
            $msg = __('%s objets was moved !', $ret);
            $msg = view_status($msg);
            if (isset($_POST['mv_redirect']) && $_POST['mv_redirect'] == '1') {
                $var = $dest_dir;
            } else {
                $var = ($cobj->type == TYPE_DIR) ? file::downPath($cobj->path) : $cobj->path;
            }
        } else {
            $msg = __('An error occured during move !');
            $msg = view_error($msg);
            $var = ($cobj->type == TYPE_DIR) ? file::downPath($cobj->path) : $cobj->file;
        }

        redirect($cobj->file, $url->linkToObj($var), $msg);
        system::end();
        break;

    //  Suppression d'un objet
    case 'del':
        acl_test(AC_DEL_FILE);

        // La suppression d'une archive n'est pas possible
        if ($cobj->type == TYPE_ARCHIVED) {
            redirect($cobj->file, $url->linkToCurrentObj(), __('Not implemented !'));
            system::end();
        }

        // On ne peut pas supprimer la racine
        if ($cobj->file == '/') {
            redirect(__('Error'), $url->linkToCurrentObj(), __('Unable to remove the root'));
            system::end();
        }

        // Il faut avoir les droits !
        if (!is_writable($cobj->realpath)) {
            redirect(__('Error'), $url->linkToCurrentObj(), view_error(__('Unable to remove object, check permissions !')));
            system::end();
        }

        // Suppression de l'objet
        if ($obj->delete($cobj)) {
            cache::del($cobj->file);
            $msg = __('Object was deleted');
            $msg = view_status($msg);
        } else {
            $msg = __('An error occured during delete !');
            $msg = view_error($msg);
        }

        /*  On redirige proprement :
            - Pour un fichier
                1. Existe-t-il un objet précédent
                2. Si il n'en existe pas, on prend le suivant
                3. Sinon, on redirige vers le dossier parent
            - Pour un dossier, on redirige vers le dossier parent
         */
        if ($cobj->type == TYPE_FILE) {
            if ($cobj->prev) {
                $var = $cobj->prev->file;
            } else if ($cobj->next) {
                $var = $cobj->next->file;
            } else {
                $var = $cobj->path;
            }
        } else {
            $var = file::downPath($cobj->path);
        }

        redirect($cobj->file, $url->linkToObj($var), $msg);
        system::end();

        break;

    //  Création d'un répertoire
    case 'mkdir':
        acl_test(AC_CREATE_DIR);

        $_POST['mk_name'] = stripslashes($_POST['mk_name']);

        // Il ne doit pas y avoir de caractère interdit !
        if (!trim($_POST['mk_name']) || string::test($_POST['mk_name'], UNAUTHORIZED_CHAR)) {
            $msg_error = __('There are an invalid char in the file name, unauthorized char are : %s', UNAUTHORIZED_CHAR);
            $url->setParam('aff', 1, 'mkdir');
            break;
        }

        // On vérifie tout d'abord si l'objet de destination existe déjà
        $dest = $cobj->path.$_POST['mk_name'];
        if (file_exists(FOLDER_ROOT.$dest)) {
            $msg_error = __('The dir already exists !');
            $url->setParam('aff', 1, 'mkdir');
            break;
        }

        // Création du dossier
        $dest = get_2_fs_charset($dest);
        if (mkdir(FOLDER_ROOT.$dest, $conf['dir_chmod'])) {
            $msg = __('Dir created !');
            $msg = view_status($msg);
        } else {
            $msg = __('An unknown error occured during dir creation !');
            $msg = view_error($msg);
        }

        // Redirection
        if (isset($_POST['mk_redirect'])) {
            switch ($_POST['mk_redirect']) {
                case 'new':
                    $var = $url->linkToObj($dest);
                    break;
                case 'edit':
                    $var = $url->linkToObj($dest, 'edit');
                    break;
                case 'parent':
                default:
                    $var = $url->linkToCurrentObj();
                    break;
            }
        }

        redirect($cobj->file, $var, $msg);
        system::end();

        break;
}

?>
