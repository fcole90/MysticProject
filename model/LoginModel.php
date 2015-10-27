<?php

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
 * Model to handle the login.
 *
 * @author fabio
 */
class LoginModel extends DBModel
{
    private $user;
    private $error;
    
    public function __construct(&$request) 
    {
        parent::__construct($request);
        $this->error = array();
    }
    
    public function show()
    {
        $page = new Presenter($this->getTitle(), "");
        
        
        /** Temporary Code **/
        if ($this->isLoggedIn())
        {
            $content = "<h2> You're already logged, " . $_SESSION["username"] . "!</h2>";
            session_unset();
            session_destroy();
        }
        else
        {
            $this->username = "Pippo";
            $content = "<h2>Come on, login!</h2>";
            $_SESSION["username"] = "Pippo";            
        }
        
        $page->setContent($content);
        $page->render();
    }
}
