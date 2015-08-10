<?php
define('__ROOT__', dirname(__FILE__)); 
require_once __ROOT__ . '/controller/Controller.php';
require_once __ROOT__ . '/controller/HomeController.php';

/**
 * Require once from the root dir.
 * 
 * @param string $path
 */
function relRequire($path)
{
    require_once __ROOT__ . "/" . $path;
}

FrontController::page($_REQUEST);

/**
 * Handles the pages.
 */
class FrontController
{
    /*
     * Select the correct controller according to the page.
     */
    function page(&$request)
    {
        if (isset($request["page"]))
        {
            //Chose the page
            switch ($request["page"])
            {
                default: //page not found
                    //call 404
            }
        }
        else //home
        {
            self::callController(new HomeController(), $request);
        }
    }
    
    /*
     * Call the controller to handle the process.
     */
    function callController(Controller $controller, &$request)
    {
        $controller->run($request);
    }
}
