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
 * This class defines an object used to represent a user and its properties.
 *
 * @author Fabio Colella
 */
class User 
{
    /**
     * @var string the firstname.
     */
    private $firstname;
    
    /**
     * @var string the secondname.
     */
    private $secondname;
    
    /**
     * @var string the username.
     */
    private $username;
    
    /**
     * @var string the password.
     */
    private $password;
    
    /**
     * @var string the email.
     */
    private $email;
    
    /**
     * @var string the birthday in format YYYY-MM-DD.
     */
    private $birthdate;
    
    /**
     * @var bool true if is admin.
     */
    private $isAdmin;
    
    /**
     * @var bool true if is a confirmed user.
     */
    private $confirmed;
    
    /**
     * Returns a list of the fields.
     * 
     * @return array list of field names.
     */
    public function fieldList()
    {
        return ["firstname", "secondname", "username", "password", 
      "email", "birthdate", "isAdmin", "confirmed"];
    }
    
    /**
     * The constructor.
     * 
     * @param string $firstname the firstname.
     * @param string $secondname the secondname.
     * @param string $email the email.
     * @param string $username the username.
     * @param string $password the password.
     * @param string $birthdate the birthday in format YYYY-MM-DD.
     * @param boolean $isAdmin true if is admin.
     * @param boolean $confirmed true if is a confirmed user.
     */
    public function __construct($firstname = "",
                            $secondname = "",
                            $email = "",
                            $username = "",
                            $password = "",
                            $birthdate = "",
                            $isAdmin = false,
                            $confirmed = false)
    {
        foreach (($this->fieldList()) as $value)
        {
            $this->$value = ${$value}; //Get the variable of the variable
        }
    }
    
    /**
     * Get a field value.
     * 
     * @param string $field the field name.
     * @return mixed the value of the field.
     */
    public function get($field)
    {
        return $this->$field;
    }
    
    /**
     * Sets a value for a field.
     * 
     * @param string $field the field name.
     * @param mixed $value the value to set.
     */
    public function set($field, $value)
    {
        $this->$field = $value;
    }
}
