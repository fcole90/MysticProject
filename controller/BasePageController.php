<?php
relRequire('controller/Controller.php');
relRequire('model/HomeModel.php');
relRequire('model/SignUpModel.php');
relRequire("model/ErrorModel.php");
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
 * Handles the home page.
 *
 * @author fabio
 */
class BasePageController extends Controller
{
       
    /**
     * Renders the home page.
     * 
     * @param array $request
     */
    public function home(&$request) 
    {
        
        $model = new HomeModel($request);
        $model->show();
    }
    
    public function signup(&$request)
    {
        $model = new SignUpModel($request);
        $model->show();
    }
    
    public function err404(&$request)
    {
        $title = "Error 404 - Page not found";
        $message = "Sorry, the page you're looking for "
          . "does not exist or has been moved.";
        $model = new ErrorModel($request, $title, $message);
        $model->setHeader("HTTP/1.0 404 Not Found");
        $model->show();
    }
    
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

}
