<?php
namespace AppNamespace\Controller;

use Air\Controller\BaseController;

class AjaxController  extends BaseController {

    public function myFirstRouteAction($param1, $param2)
    {
        die(var_dump($param1, $param2));
    }
}
?>