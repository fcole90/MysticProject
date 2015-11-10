<?php

relRequire('model/Model.php');

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
 * Description of DBModel.
 * 
 * $dbhost = "localhost";
 * $dbname = "amm15_colellaFabio";
 * $dbuser = "colellaFabio";
 * $dbpass = "nutria8058";
 *
 * @author fabio
 */
abstract class DBModel extends Model
{
    
    public function __construct() {
        parent::__construct();
    }
    /**
     * 
     * @return string hostname
     */
    protected function dbHostname()
    {
       return  "localhost";
    }
    
    /**
     * 
     * @return string database
     */
    protected function dbDatabase()
    {
        return "amm15_colellaFabio";
    }
    
    /**
     * 
     * @return string username
     */
    protected function dbUsername()
    {
        return "colellaFabio";
    }
    
    /**
     * 
     * @return string password
     */
    protected function dbPassword()
    {
        return "nutria8058";
    }


    /**
     * Create a connection.
     * 
     * If it fails connecting thrown an exception.
     * 
     * @return mysqli mysqli
     * 
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
