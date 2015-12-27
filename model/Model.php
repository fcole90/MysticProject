<?php
relRequire("view/Presenter.php");
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
 * Collects methods and properties useful in every model.
 * 
 * @author Fabio Colella
 */
abstract class Model 
{
    
    /**
     * @var mixed[] data to be sent back to the controller.
     */
    protected $data;
    
    /**
     * @var array error messages of the database.
     */
    protected $error;

    /**
     * The constructor.
     */
    public function __construct() 
    {
        $this->data = array();
        $this->error = array();
    }
    
    
    /**
     * Add data to the associtive array.
     * 
     * @param string $name
     * @param mixed $data
     * @return boolean taskSucceded
     */
    public function addData($name, $data) 
    {
        if (isset($this->data[$name]))
        {
            return false;
        }
        else
        {
            $this->data[$name] = $data;
        }
    }
    
    /**
     * Retrieve data from the associative array.
     * 
     * @param string $name
     * @return mixed if data[$name] is not present returns null.
     */
    public function getData($name)
    {
        if (!isset($this->data[$name]))
        {
            return null;
        }
        else
        {
            return $this->data[$name];
        }
    }
    
    /**
     * Returns the error array.
     * 
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }
}
