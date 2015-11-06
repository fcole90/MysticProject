<?php
relRequire('controller/Controller.php');
relRequire('view/Presenter.php');
relRequire('model/HomeModel.php');
relRequire('model/SignUpModel.php');
relRequire('model/LoginModel.php');
relRequire("model/ErrorModel.php");
relRequire("model/GenericModel.php");
/*
 * Copyright (C) 2015 fabio
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * Handles the pages.
 *
 * @author fabio
 */
class BasePageController extends Controller
{
    
    public function __construct(&$request)
    {
        parent::__construct($request);
    }
    
    
    /***********************************************
     * Page handling functions.                    *
     ***********************************************/
    
    
    /**
     * Renders the home page.
     * 
     * @param array $request
     */
    public function home() 
    {       
        $model = new HomeModel();
        
        $page = new Presenter($this->getTitle());
        
        /* Temporary HTML */
        $content = <<<HTML
<h2>Find your lozenges in "Fleetwood"</h2>
<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.openstreetmap.org/export/embed.html?bbox=-3.1280136108398438%2C53.86346094359846%2C-2.8873443603515625%2C53.985568980647656&amp;layer=mapnik&amp;marker=53.924650964860085%2C-3.007637200000005" style="border: 1px solid black"></iframe><br/><small><a href="http://www.openstreetmap.org/?mlat=53.9247&amp;mlon=-3.0076#map=12/53.9247/-3.0076">View Larger Map</a></small>
HTML;
        $page->setContent($content);
        $page->render();
    }
    
    /**
     * Handles the signup.
     * @param request $request
     */
    public function signup(&$request)
    {
        $model = new SignUpModel($request);
        $model->show();
    }
    
    public function login(&$request)
    {
        $model = new LoginModel($request);
        $model->show();
    }
    
    public function logout($request) {
       $model = new LoginModel($request);
       $model->logout();
    }
    
    public function help($request) {
        $model = new GenericModel($request);
        $model->showHelpPage();
    }
    
    /**
     * Handles the 404 error
     * @param request $request
     */
    public function err404(&$request)
    {
        $title = "Error 404 - Page not found";
        $message = "Sorry, the page you're looking for "
          . "does not exist or has been moved.";
        $model = new ErrorModel($request, $title, $message);
        $model->setHeader("HTTP/1.0 404 Not Found");
        $model->show();
    }
    
    /**
     * Handles the 403 error.
     * @param request $request
     */
    public function err403(&$request)
    {
        $title = "Error 403 - Forbidden";
        $message = "You're attempting to access an unauthorized "
          . "area. If you think you should be able to access this area "
          . "contact your administrator.";
        $model = new ErrorModel($request, $title, $message);
        $model->setHeader("HTTP/1.0 403 Forbidden");
        $model->show();
    }
    
    /***********************************
     * Helper functions.               *
     ***********************************/

}
