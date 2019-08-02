<?php
/*
Plugin Name: Make Filename Lowercase
Plugin URI: http://www.redbridgenet.com/make-filename-lowercase/
Description: Sets uploaded media filename to lowercase.
Version: 1.0.2
Author: Ed Reckers (Red Bridge Internet)
Author URI: http://www.redbridgenet.com
License: GPL2
*/

/*  Copyright 2014 Ed Reckers (email : ed@redbridgenet.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Filter {@see sanitize_file_name()} and return the lower case.
 *
 * @param string $filename
 * @return string
 */
function mfl_make_filename_lowercase($filename) {
    $info = pathinfo($filename);
    $ext  = empty($info['extension']) ? '' : '.' . $info['extension'];
    $name = basename($filename, $ext);
    return strtolower($name) . $ext;
}
add_filter('sanitize_file_name', 'mfl_make_filename_lowercase', 10);

?>
