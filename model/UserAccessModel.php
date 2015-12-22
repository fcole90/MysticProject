<?php
relRequire("model/DBModel.php");
relRequire("model/User.php");
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
 * Model class to handle the data for the signup process and add a new user.
 *
 * @author fabio
 */
class UserAccessModel extends DBModel
{
    private $user;
    private $passwordMinLength = 6;
    
    public function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * Set a user.
     */
    public function setUser($user) 
    {
        $this->user = $user;
    }
       
    
    /**
     * Add a user to the database.
     * 
     * @param User $user
     */
    public function addUserToDatabase(User $user) 
    {
        try
        {
            $mysqli = $this->connect();
        } 
        catch (Exception $e) 
        {
            $this->error[] = $e->getMessage();
            $this->error[] = "Could not connect to database.";
            return false;
        }
        
        /**
         * Make the password safe.
         */ 
        $hash = password_hash($user->get("password"), PASSWORD_DEFAULT);
        $user->set("password", $hash);
        
        $sFields = ""; // used to bind param in ssss like string
        $fieldList = $user->fieldList();
        // dynamically create the statement
        $text = "INSERT INTO user (";
        foreach ($fieldList as $field)
        {
            $text .= "$field, ";
            $sFields .= "s";
        }
        $text = rtrim($text, ", "); //remove last comma
        $text .= ") ";
        $text .= "values (";
        foreach ($fieldList as $field)
        {
            $text .= "?, ";
        }
        $text = rtrim($text, ", "); //remove last comma
        $text .= ") ";
        
        // Create the list of the params for bind_param
        $paramList[] = $sFields;
        foreach ($fieldList as $field)
        {
            $paramList[] = $user->get($field);
        }
        
        if (!$stmt = $mysqli->prepare($text))
        {
            $this->error[] = "Error: could not prepare statement: $text";
            return false;
        }
        
        if (!is_callable(array($stmt, 'bind_param')))
        {
             $this->error[] = "Error: stmt is not callable";
             return false;
        }
        
        //the params need to be passed by reference
        foreach ($paramList as &$param)
        {
            $refParamList[] = &$param;
        }
        //call bind param with a dynamic number of params
        if (!call_user_func_array(array($stmt, 'bind_param'), $refParamList))
        {
            $this->error[] = "DB Error: could not bind parameters.";
            return false;
        }
        
        if (!$stmt->execute())
        {
            $this->error[] = "DB Error: could not execute the statement.";
            return false;
        }
        $stmt->close();
        $mysqli->close();
        
        return true;
    }
    
    /**
     * Returns true if the a field does not exist in the DB,
     * returns false if the field already exists or there has 
     * been a connection error with the DB.
     * 
     * @param string $fieldname
     * @param string $fieldvalue
     * @return boolean
     */
    public function checkFieldNotExists($fieldname, $fieldvalue)
    {
        try
        {
            $mysqli = $this->connect();
        } 
        catch (Exception $e) 
        {
            $this->error[] = $e->getMessage();
        }
        
        $text = "SELECT $fieldname FROM user WHERE $fieldname = ?;";
        
        if (!$stmt = $mysqli->prepare($text))
        {
            $this->error[] = "Error: could not prepare statement: $text";
            return false;
        }
        
        if (!$stmt->bind_param("s", $fieldvalue))
        {
            $this->error[] = "DB Error: could not bind parameters.";
            return false;
        }
        
        if (!$stmt->execute())
        {
            $this->error[] = "DB Error: could not execute the statement.";
            return false;
        }
        
        if (!$stmt->bind_result($resField))
        {
            $this->error[] = "DB Error: could not bind results.";
            return false;
        }
        
        $stmt->fetch();
        
        if (isset($resField))
        {
            $stmt->close();
            $mysqli->close();
            $this->error[] = "This $fieldname already exists, please "
              . "use a different one.";
            return false;
        }
        $stmt->close();
        $mysqli->close();
        return true;
        
        
    }   
    
    
    /**
     * Checks username and password.
     * 
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function checkLoginData($username, $password)
    {        
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
    
    /**
     * Get a user from the database
     * 
     * @param string $username
     * @return user or FALSE if it fails
     */
    public function getUser($username)
    {        
        $this->user = new User;
        
        try
        {
            $mysqli = $this->connect();
        } 
        catch (Exception $e) 
        {
            $this->error[] = $e->getMessage();
        }
        
        
        $text = "SELECT * FROM user WHERE username = ?;";
        
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
        
        if (!$result = $stmt->get_result())
        {
            $this->error[] = "DB Error: could not get results: $username";
            return false;
        }
        
        $row = $result->fetch_assoc();
        
        if (isset($row))
        {
            foreach ($this->user->fieldlist() as $field)
            {
                if($field != "password")
                {
                    $this->user->set($field, $row[$field]); 
                }
            }
            $stmt->close();
            $mysqli->close();
            return $this->user;
        }

        /* else */
        
        $this->error[] = "Sorry, could not retrieve any information.";
        $stmt->close();
        $mysqli->close();
        return false;
       
    }
      
}
