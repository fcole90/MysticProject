<?php
relRequire("model/DBModel.php");
relRequire("model/User.php");
relRequire("view/SignUpForm.php");
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
 * Description of SignUpModel
 *
 * @author fabio
 */
class SignUpModel extends DBModel
{
    private $user;
    private $error;
    private $passwordMinLength = 6;
    
    public function __construct(&$request) {
        parent::__construct($request);
        $this->error = array();
    }
    
    /**
     * This method is called by the controller and contains most
     * of the logic.
     * 
     * Creates a new renderer for the page, then checks the fields.
     * If the fields are not validated it calls the same page again, otherwise 
     * it tries to add the user to the database and shows a confirmation page. 
     * If there's a database error it prints an error message.
     */
    public function show()
    {
        $page = new Presenter($this->getTitle());
        $this->setFields();
        if (!$this->checkFields())
        {
            $page->setContent((new SignUpForm())->getForm($this->user, $this->error));
        }
        else if($this->addUserToDatabase())
        {
            $page->setContent((new SignUpForm())->getConfirmation($this->user));
        }
        else
        {
            $page->setContent((new SignUpForm())->getForm($this->user, $this->error));
        }
                
        $page->render();
    }
    
    /**
     * Set a user.
     */
    public function setUser($user) 
    {
        $this->user = $user;
    }
    /**
     * Setup the user variable feeding it with the form fields.
     */
    public function setFields()
    {
        $this->user = new User();
        foreach ($this->user->fieldList() as $field)
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
    
    /**
     * Makes the input safe!.
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
    
    /**
     * Add a user to the database (MOCKUP).
     */
    public function addUserToDatabase() 
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
        
        $sFields = ""; // used to bind param in ssss like string
        $fieldList = $this->user->fieldList();
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
            $paramList[] = $this->user->get($field);
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
     * Useful to mark the fields wich require attention.
     */
    public function setWarning($field)
    {
        return $field . '" class="warning';
    }
    
    /**
     * Here is where the fields get checked.
     */
    public function checkFields()
    {
        /**First check is the fields exist**/
        $isValid = false;
        
        foreach ($this->user->fieldList() as $field)
        {
            if (null !== $this->user->get($field))
            {
                $isValid = true;
            }
        }
        if (!$isValid) return false;
        /** Skips other checks if none of the fields is set**/
        /**Then if they're valid**/
        $current = "username";
        if (!$this->checkCharDigit($current, $this->user->get($current)) ||
          !$this->checkFieldNotExists($current))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
        
        
        
        $current = "firstname";
        if (!$this->checkCharDigit($current, $this->user->get($current)))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
        
        $current = "secondname";
        if (!$this->checkCharDigit($current, $this->user->get($current)))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
        
        $current = "password";
        if (!$this->checkPassword($current, $this->user->get($current)))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
        
        $current = "email";
        if (!$this->checkEmail($current, $this->user->get($current)) ||
          !$this->checkFieldNotExists($current))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
        
        $current = "birthdate";
        if (!$this->checkDate($current, $this->user->get($current)))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
     
        return $isValid;
    }
    
    /**
     * Helper function to check is strings contain only chars and numbers.
     */
    public function checkCharDigit($field, $value)
    {
        if (!preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/",$value)) 
        {
            $this->error[] = "Only letters and numbers are allowed in $field."
              . " (Numbers not at beginning).";
            return false;
        }
        return true;
    }
    
    /**
     * Helper function to check if the string is strong to be used as password.
     */
    public function checkPassword($field, $value)
    {
        if (!preg_match("/[0-9]+/",$value) || !preg_match("/[A-Z]+/",$value) 
          || !preg_match("/[a-z]+/",$value) || !(strlen($value)>=  $this->passwordMinLength)) 
        {
            $this->error[] = ucfirst($field) . " must be at least "
              . "$this->passwordMinLength chars long and have at least: "
              . "one digit, "
              . "one upper case letter "
              . "and one lower case letter.";
            return false;
        }
        return true;
    }
    
    public function checkEmail($field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            $this->error[] = "The $field is not valid.";
            return false;
        }
        return true;
    }
    
    public function checkDate($field, $value)
    {
        $date = explode("-", $value);
        if (!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $value) ||
          !checkdate($date[1], $date[2], $date[0]))
        {
            $this->error[] = "The $field is not valid or does not correspond "
              . "to the required date format: "
              . "YYYY-MM-DD.";
            return false;
        }
        return true;
    }
    
    /**
     * Returns true if the a field does not exist in the DB,
     * returns false if the field already exists or there has 
     * been a connection error with the DB.
     */
    public function checkFieldNotExists($field)
    {
        try
        {
            $mysqli = $this->connect();
        } 
        catch (Exception $e) 
        {
            $this->error[] = $e->getMessage();
        }
        
        $current = $field;
        $value = $this->user->get($current);
        $stmt = $mysqli->prepare("SELECT $field FROM user WHERE $field = ?;");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $stmt->bind_result($resField);
        $stmt->fetch();
        
        if (isset($resField))
        {
            $stmt->close();
            $mysqli->close();
            $this->error[] = "This $current already exists, please "
              . "use a different one.";
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            return false;
        }
        return true;
        
        
    }
    
      
}
