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

class TranslationHelper
{

    public static $defaultLocale = 'en';

    /**
     * Get local from session if not exist return fr as default local
     *
     * @return string
     */
    public static function getLocale()
    {
        if (!isset($_SESSION['locale'])) {
            $_SESSION['locale'] = 'en';
        }

        return $_SESSION['locale'];
    }

    public static function loadTranslation($text)
    {
        $locale      = self::getLocale();
        $translation = ParameterHelper::getParam('translations/'.$locale, $text);

        return isset($translation) ? $translation : $text;
    }


}