<?php

/*
 * Copyright (C) 2015 Fabio Colella
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
 * All controllers should inherit from it.
 * 
 * @author Fabio Colella.
 */
abstract class Controller 
{
    /**
     * @var string the title of the page
     */
    private $title;
    
    /**
     * @var string the base of the title
     */
    private $baseTitle = "Fisherman's Friend Locator";
    
    /**
     * @var array the $_REQUEST array.
     */
    public $request;
    
    /**
     * @var string the username of the logged user.
     */
    protected $username;
    
    /**
     * @var string the name of the current page.
     */
    protected $page;
    
    
    /**
     * The constructor.
     * 
     * @param mixed[] $request the $_REQUEST array.
     */
    public function __construct($request) 
    {
        if(isset($_SESSION["username"]))
        {
            $this->username = $this->safeInput($_SESSION["username"]);
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
     * Getter method for the basetitle.
     * 
     * @return string the basetitle.
     */
    public function baseTitle()
    {
        return $this->baseTitle;
    }
    
    /**
     * Composes the page title.
     * 
     * @param string $page the page name.
     * @return string the complete page title.
     */
    public function pageTitle($page) 
    {
        return ucfirst($page) . " - " . $this->baseTitle();
    }
    
    /**
     * Set the title of the page.
     * 
     * @param string $page the page name.
     */
    public function setTitle($page)
    {
        $this->title = $this->pageTitle($page);
    }
    
    /**
     * Sets a custom title.
     * 
     * @param string $title the title to use.
     */
    public function setCustomTitle($title)
     {
         $title = ucfirst($title) . " - " . $this->baseTitle();
         $this->setTitle($title);
     }
    
    /**
     * Getter method for the title.
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Getter method for the page.
     * 
     * @return string.
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Returns true if there's an active login session.
     * 
     * @return boolean true if is logged in.
     */
    protected function isLoggedIn() 
    {
        return isset($_SESSION) && array_key_exists("username", $_SESSION);
    }
    
    protected function isAdmin()
    {
        return isset($_SESSION) && array_key_exists("isAdmin", $_SESSION) && $_SESSION["isAdmin"];
    }

    /**
     * Destroys the active session.
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
     * 
     * @param $input string
     * @return string
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
        return "";
    }
    
    /**
     *  Getter method for the username.
     * 
     * @return string username.
     */
    public function getSessionUsername() 
    {
        return $this->username;
    }
    
    /**
     * Returns a list of links.
     * 
     * The links are in the form (page name, page link).
     * 
     * @return array[] list of links.
     */
    public function getLinks()
    {
        $linklist = array();
        
        if ($this->isLoggedIn())
        {
            if ($this->isAdmin())
            {
                $linklist[] = array("ADMIN", "profile");
            }
            else
            {
                $linklist[] = array("PROFILE", "profile");
            }

            $linklist[] = array("logout","logout");
            $linklist[] = array("add a shop", "addshop");
        }
        else
        {
            $linklist[] = array("LOGIN", "login");
            $linklist[] = array("signup", "signup");
        }
                
        return $linklist;
    }
}
