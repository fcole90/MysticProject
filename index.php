<?php
define('__ROOT__', dirname(__FILE__)); 
require_once __ROOT__ . '/controller/Controller.php';
require_once __ROOT__ . '/controller/BasePageController.php';
session_start();
                
/** Enable error reporting **/
/** Enable debug mode **/
define('DBGMODE', true);
/** Comment the followign lines to disable error reporting **/
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
    /**
     * Select the correct controller function according to the page.
     * 
     * As a security feature the functions have their name starting with
     * "loadPagePAGENAME" to avoid the user exploiting them to call potentially 
     * dangerous functions.
     */
    public static function page(&$request)
    {
        $controller = new BasePageController($request);
        
        if (isset($controller->page))
        {
            $page = "loadPage" . ucfirst($controller->page); //Camel Case
            if(is_callable(array($controller,$page)))
            {
                $controller->$page();
            }
            else
            {
                $controller->loadPageErr404();
            }
        }
        else //home
        {
            $controller->loadPageHome();
        }
    }
}

FrontController::page($_REQUEST);
