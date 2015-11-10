<?php
define('__ROOT__', dirname(__FILE__)); 
require_once __ROOT__ . '/controller/Controller.php';
require_once __ROOT__ . '/controller/BasePageController.php';
session_start();
                
/** Enable error reporting **/
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

/**/

/**
 * Require once from the root dir.
 * 
 * @param string $path
 */
function relRequire($path)
{
    require_once __ROOT__ . "/" . $path;
}

/**
 * Handles the pages.
 */
class FrontController extends Controller
{
    /*
     * Select the correct controller according to the page.
     */
    public static function page(&$request)
    {
        $controller = new BasePageController($request);
        
        if (isset($controller->page))
        {
            if(is_callable(array($controller,$controller->page)))
            {
                $page = $controller->page;
                $controller->$page();
            }
            else
            {
                $controller->err404();
            }
        }
        else //home
        {
            $controller->home();
        }
    }
}

FrontController::page($_REQUEST);
