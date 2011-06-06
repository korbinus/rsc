<?php
/* ReSource Compression, a script for transparently compressing CSS and JS files
 * Copyright (C) 2011 Mickaël Graf <korbinus at gmail.com>
 * 
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU Affero General Public License as
 *   published by the Free Software Foundation, either version 3 of the
 *   License, or (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Affero General Public License for more details.
 *
 *   You should have received a copy of the GNU Affero General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/* @todo: write a function for avoiding to have the same code twice
 * @todo: deal with if-modified-since
 */

function error404() {
    header('HTTP/1.1 404 Not Found');
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();    
}

$pathJS = '../js/';                                                     //path to js/ directory
$pathCSS = '../css/';                                                   //path to css/directory

//taken from 
//http://www.sean-o.com/blog/index.php/2009/08/11/tutorial-how-to-create-your-own-url-shortener/
$expectedURL = trim($_SERVER['REQUEST_URI']);

$split = preg_split("{/[{rsc}\/]*/}",$expectedURL);                     //I'm a joke at regular expressions...
$file = htmlspecialchars($split[1]);

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
if (preg_match('/.js$/',$file) > 0) {                                   //javascript file
    if(file_exists($pathJS . $file)) {                                  //check that the file exists
        header('HTTP/1.1 203');                                         //send a success header
        header('Content-type: text/javascript; charset=UTF-8');         //send the content-type
        include $pathJS . $file;                                        //simply drop the content
    } else {
        error404();
    }
} else if (preg_match('/.css$/',$file) > 0) {                           //css file
    if(file_exists($pathCSS . $file)) {                                 //check that the file exists
        header('HTTP/1.1 203');                                         //send a success header
        header('Content-type: text/css; charset=UTF-8');                //send the content-type
        include $pathCSS . $file;                                       //simply drop the content
    } else {
        error404();
    }
} else {
    error404();
}

?>
