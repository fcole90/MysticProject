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
 * All controllers should inherit from here
 * 
 * @author fabio
 */
abstract class Controller 
{
    /**
     * 
     * @var string $title
     */
    private $title;
    
    /**
     *
     * @var string
     */
    private $baseTitle = "Fisherman's Friend Locator";
    
    /**
     *
     * @var array
     */
    public $request;
    
    /**
     *
     * @var string
     */
    protected $username;
    
    /**
     *
     * @var string
     */
    protected $page;
    
    /**
     * 
     * @param array $request
     */
    public function __construct($request) 
    {
        if(isset($request["username"]))
        {
            $this->username = $this->safeInput($request["username"]);
        }
        else
        {
            $this->username = "";
        }
        if (isset($request["page"]))
        {
            $page = $this->safeInput($request["page"]);
            $this->setTitle($page);
            $this->page = $page;
        }
        else
        {
            $this->setTitle("home");
        }
        $this->request = $request;
    }
    
    /**
     * 
     * @return string
     */
    public function baseTitle()
    {
        return $this->baseTitle;
    }
    
    /**
     * 
     * @param array $request
     * @return string
     */
    public function pageTitle($page) 
    {
        return ucfirst($page) . " - " . $this->baseTitle();
    }
    
    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $this->pageTitle($title);
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getPage()
    {
        return $this->page;
    }


    protected function isLoggedIn() 
    {
        return isset($_SESSION) && array_key_exists("username", $_SESSION);
    }
    
    /**
     * Destroys the session.
     */
    protected function closeSession()
    {
        $_SESSION = array();
        
        if (session_id() != "" || isset($_COOKIE[session_name()]))
        {
            setcookie(session_name(), '', time() - 2592000, '/');
        }
        session_unset();
        session_destroy();
    }
    
    /**
     * Makes the input safe.
     */
    public function safeInput($input)
    {
        if (isset($input))
        {
            $input = trim($input);
            $input = stripslashes($input);
            $input = htmlentities($input);
            return $input;
        }
        return $input;
    }
    
    
    
}
