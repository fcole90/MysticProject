<?php
relRequire("model/DBModel.php");
relRequire("model/User.php");
relRequire("view/LoginForm.php");
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
        $page = new Presenter($this->getTitle());
        $this->setFields();
        
        if(isset($_SESSION["username"]))
        {
            $this->error[] = "You're already logged in!";
            $page->setContent((new LoginForm())->loginConfirm());
            $page->setError($this->error);
            $page->setRedir();
        }
        else if(!$this->checkLoginData())
        {
            $page->setContent((new LoginForm())->getForm($this->user, $this->error));
        }
        else
        {
            $_SESSION["username"] = $this->user->get("username");
            $page->setContent((new LoginForm())->loginConfirm());
            $page->setRedir();
        }

        $page->render();
    }
    
    public function logout()
    {
        $page = new Presenter($this->getTitle());
        
        if(isset($_SESSION["username"]))
        {
            $page->setContent((new LoginForm())->logout());
            $this->closeSession();
            $page->setRedir();
        }
        else
        {
            $this->error[] = "You're not logged in yet.";
            $this->show();
        }


        $page->render();
    }

    
    /**
     * Checks username and password.
     * 
     * @return boolean
     */
    public function checkLoginData()
    {
        
        $username = $this->user->get("username");
        $password = $this->user->get("password");
        
        /**
         * Avoid calling the database if no data has been submitted.
         */
        if (!isset($username) || $username == "")
        {
            return false;
        }
        else if(!isset($password) || $password == "")
        {
           $this->error[] = "Sorry, the password cannot be empty.";
           return false;
        }
        
        try
        {
            $mysqli = $this->connect();
        } 
        catch (Exception $e) 
        {
            $this->error[] = $e->getMessage();
        }
        
        
        $text = "SELECT username, password FROM user WHERE username = ?;";
        
        if (!$stmt = $mysqli->prepare($text))
        {
            $this->error[] = "Error: could not prepare statement: $text";
            return false;
        }
        
        
        if (!$stmt->bind_param("s", $username))
        {
            $this->error[] = "DB Error: could not bind parameters.";
            return false;
        }
        
        if (!$stmt->execute())
        {
            $this->error[] = "DB Error: could not execute the statement.";
            return false;
        }
        
        if (!$stmt->bind_result($user, $hash))
        {
            $this->error[] = "DB Error: could bind results.";
            return false;
        }
        
        $stmt->fetch();
        
        if (isset($user) && isset($hash) && password_verify($password, $hash))
        {
            $stmt->close();
            $mysqli->close();
            $this->error[] = "Welcome, $user!";
            return true;
        }
        /* else */
        $this->error[] = "Sorry, username or password are incorrect.";
        $stmt->close();
        $mysqli->close();
        return false;
        
        
    }
    
    public function setFields()
    {
        $this->user = new User();
        $list = array("username", "password");
        foreach ($list as $field)
        {
            if (isset($this->request[$field]))
            {
                $this->user->set($field, $this->safeInput($this->request[$field]));
            }
            else
            {
                $this->user->set($field, null);
            }
        }
    }
}
