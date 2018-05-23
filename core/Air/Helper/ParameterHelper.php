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

namespace Air\Helper;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class ParameterHelper
{

    public static function getParam($filePath, $param, $directory = '/Resources/')
    {
        $aParam = [];
        if ($file = @file_get_contents($_SERVER['DOCUMENT_ROOT'].$directory.$filePath.'.yml')) {
            try {
                $aParam = Yaml::parse($file);
            } catch (ParseException $e) {
                printf("Unable to parse the YAML string: %s", $e->getMessage());
                die;
            }
        }

        return isset($aParam[$param]) ? $aParam[$param] : false;
    }

}