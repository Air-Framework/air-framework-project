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

namespace Air\Controller;

use Air\Helper\TranslationHelper;
use Air\Extensions\ATwigExtension;


class BaseController
{

    protected $twig;

    public function render($template, $aTwigVars = [])
    {
        $twig = $this->twig;
        $twig->addExtension(new ATwigExtension());
        $aTwigVars['locale'] = TranslationHelper::getLocale();;

        if (!isset($aTwigVars['timeout'])) {
            $aTwigVars['timeout'] = false;
        }

        echo $twig->render($template, $aTwigVars);
        return true;
    }

    /**
     * Redirect to route
     *
     * @param $route
     */
    public function redirectTo($route)
    {
        header("Location: $route");
        exit(0);
    }

    public function __construct()
    {
        $loader     = new \Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'].'/Resources/views');
        $this->twig = new \Twig_Environment($loader);
    }

}