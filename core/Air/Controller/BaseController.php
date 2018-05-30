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

    public static function notFoundAction ()
    {
        die('404');
    }

}