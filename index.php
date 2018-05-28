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

require_once("vendor/autoload.php");

session_start();
$_SESSION['locale'] = 'en';

/*
    Instantiate the bootstrap (Router) with your own namespace depending on your file structure
    i.e. namespace App stands for App folder on base dir
*/
//$bootstrap = new Air\Bootstrap\Bootstrap('src\App');
//$bootstrap = new Air\Bootstrap\Bootstrap('Microservice');
$bootstrap = new Air\Bootstrap\Bootstrap('AppNamespace');
