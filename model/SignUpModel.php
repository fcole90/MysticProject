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
        
            
            
        
        
        
        
        $page->render();
    }
    
    public function setFields()
    {
        $this->user = new User();
        foreach ($this->user->fieldList() as $field)
        {
            if (isset($this->request[$field]))
            {
                $this->user->set($field, $this->safeInput($this->request[$field]));
            }            
        }
    }
    
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
    
    public function databaseInsert() 
    {
        return true;
    }


    public function setWarning($field)
    {
        return $field . '" class="warning';
    }
    
    public function checkFields()
    {
        $isValid = true;
        
        $current = "username";
        if (!$this->checkCharDigit($current, $this->user->get($current)))
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
        if (!$this->checkEmail($current, $this->user->get($current)))
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
    
    public function checkCharDigit($field, $value)
    {
        if (!preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/",$value)) 
        {
            $this->error[] = "Only letters and numbers are allowed in $field. (Numbers not at beginning).";
            return false;
        }
        return true;
    }
    
    public function checkPassword($field, $value)
    {
        if (!preg_match("/[0-9]+/",$value) || !preg_match("/[A-Z]+/",$value) 
          || !preg_match("/[a-z]+/",$value) || !(strlen($value)>=  $this->passwordMinLength)) 
        {
            $this->error[] = ucfirst($field) . " must be at least "
              . "$this->passwordMinLength chars long and have at least: 1 digit, "
              . "1 upper case letter "
              . "and 1 lower case letter.";
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
            $this->error[] = "The $field is not valid or does not correspond to the required date format: "
              . "YYYY-MM-DD.";
            return false;
        }
        return true;
    }
    
      
}
