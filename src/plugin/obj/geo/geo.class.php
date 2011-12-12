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

class geo {

    /**
     *  Get data from file
     *  @param  string  $filename   File name
     *  @param  string  $ext        File format (extension)
     */
    function getData($filename, $ext = null) {
        $ret = null;
        
        switch ($ext) {
            case 'asc':
                $ret = geo::getDataFromAsc($filename);
                break;
            case 'ov2':
                $ret = geo::getDataFromOv2($filename);
                break;
        }
        
        return $ret;   
    }

    /**
     *  Write data to file
     *  @param  string  $filename   File name
     *  @param  string  $ext        File format
     *  @param  array   $data       Data
     */
    function writeData($filename, $ext = null, $data) {
        $ret = null;
        
        switch ($ext) {
            case 'asc':
                $ret = geo::writeDataToAsc($filename, $data);
                break;
            case 'ov2':
                $ret = geo::writeDataToOv2($filename, $data);
                break;
        }
        
        return $ret;   
    }

    /**
     *  Get data from asc file
     *  @param  string  $filename   File name
     */
    function getDataFromAsc($filename) {
        $ret = null;

        $quotes = array('"', "'");

        $data = file($filename);
        if ($data) {
            foreach ($data as $line) {

                $line = trim($line);

                // Comments
                if (!$line || $line{0} == ';') {
                    continue;
                }
                
                list($lon, $lat, $label) = explode(',', $line);
                
                $label = trim($label);
                foreach ($quotes as $quote) {
                    if ($label{0} == $quote && $label{strlen($label) - 1} == $quote) {
                        $label = substr($label, 1, strlen($label) - 2);
                    }
                }
                
                $ret[] = array(
                    'lon'   =>  $lon,
                    'lat'   =>  $lat,
                    'label' =>  $label,
                );
            }
        }

        return $ret;
    }

    /**
     *  Write data to asc file
     *  @param  string  $filename   File name
     *  @param  array   $data       Data
     */
    function writeDataToAsc($filename, $data) {

        $ret = false;
        $content = null;

        foreach ($data as $poi) {

            // Quote
            $poi['label'] = '"'.str_replace('"', '\"', $poi['label']).'"';

            $content .= implode(',', $poi);
            $content .= "\n";
        }

        if ($content) {
            $ret = file_put_contents($filename, $content); //, FILE_APPEND);
        }

        return $ret;
    }

    /**
     *  Get data from ov2 file
     *  @param  string  $filename   File name
     */
    function getDataFromOv2($filename) {
        $ret = null;

        $fp = fopen($filename, 'rb');
        if ($fp) {
            while (($data = fgetc($fp)) !== false) {
                $type = unpack('cchar', $data);
                switch ($type['char']) {
                    // Deleted
                    case '0':
                        $data = fread($fp, 4);
                        $c = unpack('Vsize', $data);
                        $data = fread($fp, $c['size'] - 5);
                        break;
                    // Skipper
                    case '1':
                        $data = fread($fp, 20);
                        $c = unpack('V/V/V/V/V', $data);
                        break;
                    // Simple and Extended
                    case '2':
                    case '3':
                        $data = fread($fp, 12);
                        $c = unpack('Vsize/Vx/Vy', $data);
    
                        $data = fread($fp, $c['size'] - 13);
                        $out = unpack('a*', $data);
    
                        $ret[] = array(
                            'lon'   =>  $c['x'] / 100000.0,
                            'lat'   =>  $c['y'] / 100000.0,
                            'label' =>  $out[1],
                        );
    
                        if ($type['char'] == '3') {
                            // Get unique id
                            while (($data = fgetc($fp)) != chr(0x00)) {
                                $c['id'] .= $data;
                            }
    
                            // Skip null char
                            while (($data = fgetc($fp)) != chr(0x00));
                        }
                        break;
                }
            }
        }

        return $ret;
    }

    /**
     *  Write data to ov2
     *  @param  string  $filename   File
     *  @param  array   $data       Data
     */
    function writeDataToOv2($filename, $data) {

        $ret = false;
        $content = null;

        foreach ($data as $poi) {
            $content .= pack('C', 0x02).
                        pack('V', strlen($poi['label']) + 14).
                        pack('V', (int)round($poi['lon'] * 100000.0)).
                        pack('V', (int)round($poi['lat'] * 100000.0)).
                        $poi['label'].
                        chr(0x00);
        }
        
        if ($content) {
            $ret = file_put_contents($filename, $content);  //, FILE_APPEND);
        }

        return $ret;
    }
}

?>
