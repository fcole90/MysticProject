<?php

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

class GenericView
{
    
    /**
     * Get the HTML to render the profile.
     * 
     * @param profile string[]
     * @return string HTML view
     */
    public function getProfileView($user)
    {
        $text = "<table>\n\t";
        
        foreach ($user->fieldlist() as $item)
        {
            $value = $user->get($item);
            if (isset($value) && $item != "password")
            {
                $text .= "<tr><td class='field'>$item</td><td>$value</td></tr>";
            }
        }        
        $text .= "\n</table>";
        
        return $text;
    }
    
    public function getHomeContent($data)
    {
        $text = "<form class='search'><input type='search' id='search-box'>"
          . "<input type='submit' value='Search' id='search-button'></form>";
        $text .= "<table id='search-table'>\n\t";
        
        foreach ($data as $item)
        {
            $text .= "<tr><td class='field'>". $item['shop_name'] . "</td>"
              . "<td>in " . $item["address"] . " a " . $item["city"] . "</td></tr>";
        }        
        $text .= "\n</table>";
        
        return $text;
    }
}

