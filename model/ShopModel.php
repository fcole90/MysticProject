<?php
relRequire("model/DBModel.php");
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
 * Description of shopModel
 *
 * @author fabio
 */
class ShopModel extends DBModel
{
    
    public function __construct() 
    {
        parent::__construct();
    }    
    
    public function addShopToDatabase($data)
    {
        
        /** Remove unsetted values from submission **/
        foreach ($data as $field => $value)
        {
            if (!isset($value) || $value == "")
            {
                unset($data[$field]);
            }
        }
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
        // dynamically create the statement
        $text = "INSERT INTO shop (";
        foreach ($data as $field => $value)
        {
            $text .= "$field, ";
            $sFields .= "s";
        }
        $text = rtrim($text, ", "); //remove last comma
        $text .= ") ";
        $text .= "values (";
        foreach ($data as $field)
        {
            $text .= "?, ";
        }
        $text = rtrim($text, ", "); //remove last comma
        $text .= ") ";
        
        // Create the list of the params for bind_param
        $paramList[] = $sFields;
        foreach ($data as $key => $value)
        {
            $paramList[] = $value;
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
     * Retrieve the shop data from the database.
     * 
     * @param string $search
     * @return string[][]
     */
    public function getData($search = "")
    {        
        $data = array();
        try
        {
            $mysqli = $this->connect();
        } 
        catch (Exception $e) 
        {
            $this->error[] = $e->getMessage();
        }
        
        if ($search == "")
        {
            $text = "SELECT * FROM shop";
            if (!$stmt = $mysqli->prepare($text))
        {
            $this->error[] = "Error: could not prepare statement: $text";
            return false;
        }
        }
        else
        {
            $text = "SELECT * FROM shop WHERE city = ? OR name = ? OR address = ?";
            $search .= "%";
            if (!$stmt = $mysqli->prepare($text))
            {
                $this->error[] = "Error: could not prepare statement: $text";
                return false;
            }
            if (!$stmt->bind_param("ss", $search, $search))
            {
                $this->error[] = "DB Error: could not bind parameters.";
                return false;
            }
        }
        
        
        if (!$stmt = $mysqli->prepare($text))
        {
            $this->error[] = "Error: could not prepare statement: $text";
            return false;
        }
        
        /*
        if (!$stmt->bind_param("s", $username))
        {
            $this->error[] = "DB Error: could not bind parameters.";
            return false;
        }
        */
        
        if (!$stmt->execute())
        {
            $this->error[] = "DB Error: could not execute the statement.";
            return false;
        }
        
        if (!$result = $stmt->get_result())
        {
            $this->error[] = "DB Error: could not get results.";
            return false;
        }
        
        if (isset($result))
        {
            while($row = $result->fetch_assoc())
            {
                foreach($row as $item => $value)
                {
                    $temp[$item] = $value;
                }
                $data[] = $temp;
            }
            $stmt->close();
            $mysqli->close();
            return $data;
        }

        /* else */        
        $this->error[] = "Sorry, could not retrieve any information.";
        $stmt->close();
        $mysqli->close();
        return false;
    }
}
