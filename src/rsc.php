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

function error404() {
    header('HTTP/1.1 404 Not Found');
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();    
}
function sendResource ($path, $filename, $contentType, $charset) {
    $file = $path . $filename;
    if (file_exists($file)) {
        exitIfNotModifiedSince(setLastModified($file));
        header('HTTP/1.1 203');                                            //send a success header
        header('Content-type: ' . $contentType . '; charset=' . $charset); //send the content-type
        include $file;                                                     //simply drop the content
    } else {                                                               //otherwise
        error404();                                                        //not found
    }
}
//thanks to http://www.justsoftwaresolutions.co.uk/webdesign/provide-last-modified-headers-and-handle-if-modified-since-in-php.html
function setLastModified($file)
{
    $lastModified=filemtime($file);

    header('Last-Modified: ' . date("r",$lastModified));
    return $lastModified;
}
function exitIfNotModifiedSince($lastModified)
{
    if(array_key_exists("HTTP_IF_MODIFIED_SINCE",$_SERVER))
    {
        $ifModifiedSince=strtotime(preg_replace('/;.*$/','',$_SERVER["HTTP_IF_MODIFIED_SINCE"]));
        if($ifModifiedSince >= $lastModified)
        {
            header("HTTP/1.1 304 Not Modified");
            exit();
        }
    }
}

$pathJS = '../js/';                                                     //path to js/ directory
$pathCSS = '../css/';                                                   //path to css/directory
$charset = 'UTF-8';

//taken from 
//http://www.sean-o.com/blog/index.php/2009/08/11/tutorial-how-to-create-your-own-url-shortener/
$expectedURL = trim($_SERVER['REQUEST_URI']);

$split = preg_split("{/[{rsc}\/]*/}",$expectedURL);                     //I'm a joke at regular expressions...
$file = htmlspecialchars($split[1]);

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();

if (preg_match('/.js$/',$file) > 0) {                                   //javascript file

    sendResource($pathJS, $file, 'text/javascript', $charset);
    
} else if (preg_match('/.css$/',$file) > 0) {                           //css file

    sendResource($pathCSS, $file, 'text/css', $charset);

} else {
    error404();
}

?>
