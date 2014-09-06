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

require 'geshi.php';

class plugin_obj_geshi extends plugin_obj {

    public function __construct($cobj) {
        parent::__construct($cobj);

        $this->tpl->set_root($this->plugin_dir.'geshi');
        $this->tpl->set_file('geshi', 'geshi.tpl');
    }

    public function aff() {

        $geshi = new GeSHi(null, null);

        $geshi->load_from_file($this->real_file);
        $geshi->set_header_type(GESHI_HEADER_PRE);
        $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);

        $content = $geshi->parse_code();
        if ($geshi->error()) {
            $content = __('Geshi error !');
        }
        
        $this->tpl->set_var('CONTENT', $content);

        return $this->tpl->parse('OutPut', 'geshi');
    }
}
