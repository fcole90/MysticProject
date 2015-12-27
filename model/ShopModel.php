<?php
relRequire("model/DBModel.php");
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
 * Class to interact with the shop table in the database.
 *
 * @author Fabio Colella
 */
class ShopModel extends DBModel
{
    /**
     * The constructor.
     */
    public function __construct() 
    {
        parent::__construct();
    }    
    
    /**
     * Adds a shop to the database.
     * 
     * @param array $data associative array where each key is a field.
     * @return boolean true if the operation succedeed.
     */
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
        
        /** Used to bind params in ssss asstrings **/
        $sFields = "";
        
        /** Dynamically create the query statement **/
        $text = "INSERT INTO shop (";
        
        /** Associate the fields **/
        foreach ($data as $field => $value)
        {
            $text .= "$field, ";
            $sFields .= "s";
        }
        
        /** Remove last comma **/
        $text = rtrim($text, ", "); 
        
        $text .= ") ";
        $text .= "values (";
        
        /** Associate the values **/
        foreach ($data as $field)
        {
            $text .= "?, ";
        }
        $text = rtrim($text, ", "); //remove last comma
        $text .= ") ";
        /** Query statement completed **/
        
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
     * Retrieve a list of shop data for the shops that match the searched criteria.
     * 
     * @param string $search the searched term.
     * @return array[] the data list of the shops.
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
            $text = "SELECT * FROM shop;";
            if (!$stmt = $mysqli->prepare($text))
            {
            $this->error[] = "Error: could not prepare statement: $mysqli->error";
            return false;
            }
        }
        else
        {
            $text = "SELECT * FROM shop WHERE city like ? OR shop_name like ? OR address like ?;";
            $search .= "%";
            if (!$stmt = $mysqli->prepare($text))
            {
                $this->error[] = "Error: could not prepare statement: $mysqli->error";

                return false;
            }
            if (!$stmt->bind_param("sss", $search, $search, $search))
            {
                $this->error[] = "DB Error: could not bind parameters.";
                return false;
            }
        }
        
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
        
        /** Create the data array **/
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
    
    /**
     * Delete a shop from the database.
     * 
     * This function uses SQL transactions.
     * 
     * @param string $shop_name the name of shop to remove.
     * @param integer $id the id of the shop to remove.
     * @return boolean true if the operation succeeded.
     */
    public function removeShop($shop_name, $id)
    {        
        try
        {
            $mysqli = $this->connect();
        } 
        catch (Exception $e) 
        {
            $this->error[] = $e->getMessage();
        }
        
        $text = "DELETE FROM shop WHERE shop_name = ? AND id = ?";
        

        if (!$stmt = $mysqli->prepare($text))
        {
            $this->error[] = "Error: could not prepare statement: $mysqli->error";

            return false;
        }
        if (!$stmt->bind_param("si", $shop_name, $id))
        {
            $this->error[] = "DB Error: could not bind parameters.";
            $this->error[] = "shop_name: $shop_name";
            $this->error[] = "id: $id";
            
            return false;
        }
        
        //Start transaction
        $mysqli->autocommit(false);
     
        if (!$stmt->execute())
        {
            $this->error[] = "DB Error: could not execute the statement.";
            $mysqli->autocommit(true);
            return false;
        }
        
        if($stmt->affected_rows != 1)
        {
            $this->error[] = "Exactly one row should be affected, $stmt->affected_rows intead!";
            $this->error[] = "The system has been rollbacked due to this error, no data has been lost.";
            $mysqli->rollback();
            $mysqli->autocommit(true);
            return false;
        }
        
        $mysqli->commit();
        $mysqli->autocommit(true);
        //End of transaction
        
        $stmt->close();
        $mysqli->close();
        
        return true;
    }
}
