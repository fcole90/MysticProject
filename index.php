<?php
define('__ROOT__', dirname(__FILE__)); 
require_once __ROOT__ . '/controller/Controller.php';
require_once __ROOT__ . '/controller/BasePageController.php';
session_start();
                
/** Enable error reporting **/
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

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
    public static function page(&$request)
    {
        if (isset($request["page"]))
        {
            
            //Chose the page
            switch ($request["page"])
            {
                case "signup":
                    self::callController(new BasePageController(), $request);
                    break;
                
                case "login":
                    self::callController(new BasePageController(), $request);
                    break;
                
                case "logout":
                    self::callController(new BasePageController(), $request);
                    break;
                    
                default: //page not found
                    $controller = new BasePageController();
                    $controller->err404($request);
            }
        }
        else //home
        {
            self::callController(new BasePageController(), $request);
        }
    }
    
    /*
     * Call the controller to handle the process.
     */
    public static function callController(Controller $controller, &$request)
    {
        if (isset($request["page"]))
        {
            $method = $request["page"];
        }
        else
        {
            $method = "home";
        }
        
        $controller->$method($request);
    }
}
