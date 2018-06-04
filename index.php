<?php
/**
 * Air Framework
 * Copyright (C) 2018 Abderrahman Daif and Lionel Tordjman
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>. *
 */
error_reporting(E_ALL);
ini_set('display_errors' , 1);

$loader = require __DIR__ . '/vendor/autoload.php';
/* Add Your(s) working directories to permit classes autoload */
$loader->addPsr4('AppNamespace\\', __DIR__ . '/AppNamespace');

use Air\Bootstrap\Bootstrap;

session_start();
$_SESSION['locale'] = 'en';

/*
    Instantiate the bootstrap (Router) with your own namespace depending on your file structure
    i.e. namespace App stands for App folder on base dir
*/
$viewsPath = $_SERVER['DOCUMENT_ROOT'].'/Resources/views';
//$bootstrap = new Air\Bootstrap\Bootstrap('src\App', $viewsPath);
//$bootstrap = new Air\Bootstrap\Bootstrap('Microservice', $viewsPath);
try {
    $bootstrap = Bootstrap::getInstance('AppNamespace', $viewsPath);
} catch (Exception $e) {
    echo $e->getMessage();
}
