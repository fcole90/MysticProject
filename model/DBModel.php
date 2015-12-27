<?php

relRequire('model/Model.php');

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
 * Defines the connection parameters of the database.
 * 
 * To allow the parameters to be visible only to children classes, they are 
 * hardcoded as return values of some methods. This choice has been done as 
 * a tradeoff between clean code and security, as the constants in PHP are
 * public.
 *
 * @author Fabio Colella
 */
abstract class DBModel extends Model
{
    /**
     * The constructor.
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * The hostname.
     * 
     * @return string hostname
     */
    protected function dbHostname()
    {
       return  "localhost";
    }
    
    /**
     * The database name.
     * 
     * @return string database
     */
    protected function dbDatabase()
    {
        return "amm15_colellaFabio";
    }
    
    /**
     * The username.
     * 
     * @return string username
     */
    protected function dbUsername()
    {
        return "colellaFabio";
    }
    
    /**
     * The password.
     * 
     * @return string password
     */
    protected function dbPassword()
    {
        return "nutria8058";
    }


    /**
     * Creates a connection.
     * 
     * If it fails connecting throws an exception.
     * 
     * @return mysqli mysqli 
     */    
    protected function connect()
    {
        $mysqli = new mysqli($this->dbHostname(), $this->dbUsername(), $this->dbPassword(), $this->DBdatabase());
        if ($mysqli->connect_error) 
        {
            throw new Exception($mysqli->connect_error);
        }
        else
        {
            return $mysqli;
        }
    }
}
