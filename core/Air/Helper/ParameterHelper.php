<?php
/**
 * Air Framework
 * Copyright (C) 2018 Abderrahman Daif and Lionel Tordjman
 *
 * Permission to use, copy, modify, and/or distribute this software for any purpose
 * with or without fee is hereby granted.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS SOFTWARE INCLUDING ALL
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
 * INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS,
 * WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE
 * OR PERFORMANCE OF THIS SOFTWARE.
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