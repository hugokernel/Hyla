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

class plugin_ws_fs extends plugin_ws {

    var $_object_table;

    /**
     *  Constructor
     */
    function plugin_ws_fs() {
        parent::plugin_ws();

        $this->_object_table = TABLE_OBJECT;
    }

    /**
     *  Rename an object
     *  @param  string  $path       Object
     *  @param  string  $new_name   New name
     */
    function rename($path, $new_name) {

        $object = $this->obj->getInfo($path, false, false);

        // Unable to rename archived file
        if (!$object->type == TYPE_ARCHIVED) {
            return new tError(__('Unable to rename an archived file !'), $this);
        }

        // Unable to rename root
        if ($object->file == '/') {
            return new tError(__('Impossible to rename the root'), $this);
        }

        // No banned char
        if (string::test($new_name, UNAUTHORIZED_CHAR)) {
            return new tError(__('There are an invalid char in the file name, unauthorized char are : %s', UNAUTHORIZED_CHAR), $this);
        }

        // File already exists ?
        $new_path = ($object->type == TYPE_FILE) ? $object->path.$new_name : file::downPath($object->path).$new_name;
        if (file_exists($this->obj->getRoot().$new_path) && $object->realpath != $this->obj->getRoot().$new_path.'/') {
            return new tError(is_dir($this->obj->getRoot().$new_path) ? __('The dir already exists !') : __('The file already exists !'), $this);
        }

        // Rename
        if (is_file($this->obj->getRoot().$path)) {
            $ret = rename($this->obj->getRoot().$path, $this->obj->getRoot().$new_path);
            if ($ret) {
                $sql = "UPDATE {$this->_object_table} SET obj_file = '".obj::format($new_path)."' WHERE obj_file = '".obj::format($path)."'";
                if (!$var = $this->bdd->execQuery($sql)) {
                    return new tError(__('Error while renaming object "%s" into database !', $new_path), $this);
                }
            }
        } else if ($this->obj->getRoot().$path) {
            $ret = rename($this->obj->getRoot().$path, $this->obj->getRoot().$new_path);
            if ($ret) {
                $new_path .= '/';
                $sql = "UPDATE {$this->_object_table} SET obj_file = REPLACE(obj_file, '".obj::format($path)."', '".obj::format($new_path)."') WHERE obj_file LIKE '".obj::format($path)."%'";
                if (!$var = $this->bdd->execQuery($sql)) {
                    return new tError(__('Error while renaming object "%s" into database !', $new_path), $this);
                }
            }
        }

        if (!$ret) {
            return new tError(__('An error occured during rename !'), $this);
        }

        return $new_name;
    }
    
    /**
     *  Copy an object
     *  @param  mixed   $file           Object
     *  @param  string  $destination    Destination path
     */
    function copy($file, $destination) {

        $file = $this->obj->getInfo($file, false, false);
        $destination = $this->obj->getInfo($destination, false, false);
        
        // Test acl
        $right = $this->obj->getCUserRights4Path($destination->file);
        if (!($right & AC_COPY)) {
            return new tError(__('You don\'t have copy right for destination dir !'), $this);
        }

        switch ($file->type) {
            case TYPE_ARCHIVED:
            case TYPE_FILE:
                if (!copy($file->realpath, $destination->realpath.$file->name)) {
                    return new tError(__('Error while copying "%s" !', $file->file), $this);
                }

                break;

            case TYPE_DIR:

                // Test dir
                if ($destination->file == $file->file) {
                    return new tError(__('Impossible to copy dir on him !'), $this);
                }
            
                // Create dir
                if (!mkdir($destination->realpath.$file->name, $this->conf->get('dir_chmod'))) {
                    return new tError(__('Unable to create "%s" !', $destination->file.$file->name), $this);
                }
                
                // Copy
                if (!file::copyDir($file->realpath, $destination->realpath.$file->name, $this->conf->get('dir_chmod'))) {
                    return new tError(__('Error while copying dir !'), $this);   
                }
                
                break;
        }
        
        return true;
    }

    /**
     *  Move
     *  @param  mixed   $file           Object
     *  @param  string  $destination    Destination path
     */
    function move($file, $destination) {

        $file = $this->obj->getInfo($file, false, false);
        $destination = $this->obj->getInfo($destination, false, false);
        
        // Test acl
        $right = $this->obj->getCUserRights4Path($destination->file);
        if (!($right & AC_MOVE)) {
            return new tError(__('You don\'t have move right for destination dir !'), $this);
        }

        switch ($file->type) {
            case TYPE_FILE:
                if (!rename($base.$copy, $base.$destination)) {
                    return new tError(__('Error while moving file !', $new_path), $this);
                }

                $sql = "UPDATE {$this->_object_table} SET obj_file = '".obj::format($destination)."' WHERE obj_file = '".obj::format($file)."'";
                if (!$var = $this->bdd->execQuery($sql)) {
                    return new tError(__('Error while moving object "%s" into database !', $new_path), $this);
                }

                break;

            case TYPE_DIR:
                if (!mkdir($base.$destination, $this->conf->get['dir_chmod'])) {
                    return new tError(__('Error while creating destination dir !', $new_path), $this);
                }

                if (!file::copyDir($base.$copy, $base.$destination, $this->conf->get['dir_chmod'])) {
                    return new tError(__('Error while copying file in destination dir !', $new_path), $this);
                }

                file::rmDirs($base.$copy);
                $sql = "UPDATE {$this->_object_table} SET obj_file = REPLACE(obj_file, '".obj::format($copy)."', '".obj::format($destination)."') WHERE obj_file LIKE '".obj::format($copy)."%'";
                if (!$var = $this->bdd->execQuery($sql)) {
                    system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
                }

                break;

            case TYPE_ARCHIVED:
            default:
                return new tError(__('Not implemented !'), $this);
        }

        return true;
    }

    /**
     *  Delete
     *  @param  mixed   $file   File or Dir
     */
    function delete($file) {

        $object = $this->obj->getInfo($file, false, false);

        // Unable to delete root dir
        if ($object->file == '/') {
            return new tError(__('Impossible to remove the root'), $this);
        }

        // Il faut avoir les droits !
        if (!is_writable($object->realpath)) {
            return new tError(__('Impossible to remove object, check permissions !'), $this);
        }

        switch ($object->type) {
            case TYPE_ARCHIVED:
                return new tError(__('Not implemented !'), $this);

            case TYPE_FILE:

                // Deleting file
                if (!unlink($object->realpath)) {
                    return new tError(__('Unknow error while deleting "%s" !', $object->file));
                }

                $id = $this->obj->getId($object->file, false);
                if ($id) {
                    $sql = "DELETE  obj, comment
                            FROM    {$this->_object_table} obj, {$this->_comment_table} comment
                            WHERE   obj.obj_id = comment.comment_obj_id AND (obj.obj_id = '$id' OR comment.comment_obj_id = '$id')";
                    if (!$ret = $this->bdd->execQuery($sql)) {
                        return new tError(__('Error while deleting object "%s" from database !', $object->file), $this);
                    }
                }
                break;

            case TYPE_DIR:
                $ret = file::rmDirs($obj->realpath);
                $id = $this->obj->getId($object->file);
                if ($id) {
                    $sql = "DELETE  obj, cnt
                            FROM    {$this->_object_table} obj, {$this->_comment_table} cnt
                            WHERE   obj.obj_id = cnt.comment_obj_id AND (cnt.comment_obj_id = '$id' OR obj.obj_file LIKE '".obj::format($object->file)."%')";
                    if (!$ret = $this->bdd->execQuery($sql)) {
                        return new tError(__('Error while deleting object "%s" from database !', $object->file), $this);
                    }
                    $this->obj->delRights();
                }
                break;
        }

        return true;
    }
}

?>
