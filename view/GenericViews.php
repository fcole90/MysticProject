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
 * Some generic content views.
 */
class GenericView
{
    
    /**
     * Get the HTML to render the profile.
     * 
     * @param User $user user object.
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
    
    /**
     * Get the HTML to render the profile and admin panel.
     * 
     * @param User $user user object.
     * @param array $shoplist a list of shops and their data.
     * @return string HTML view.
     */
    public function getAdminView($user, $shoplist)
    {
        $text = $this->getProfileView($user); //Show the admin profile
        
        $text .= "<h3>List of shops</h3>";
        
        $text .= "<form class='search' action='javascript:actionsearchAdmin()' id='searchform'>"
          . "<input type='search' id='search-box-admin' autocomplete='off'>\n"
          . "\t<input type='submit' value='Search' id='search-button'>\n"
          . "</form>\n";
        $text .= "<table id='search-table'>\n\t";
        
        foreach ($shoplist as $item)
        {
            $text .= "<tr><td class='field'>". $item['shop_name'] . "</td>\n"
              . "<td>" . $item["address"] . ", " . $item["city"] . "</td>\n</tr>"
              . "<tr class='tr-removal'><td></td><td><form action='removeShop'>"
              . "<input type='hidden' name='shop_name' value='" . $item['shop_name'] . "'>"
              . "<input type='hidden' name='id' value='" . $item['id'] . "'>"
              . "<input type='submit' value='Remove'></form></td></tr>";
        }        

        $text .= "\n</table>";
        
        /*This is implemented here for coherence with the js equivalent*/
        if (!$shoplist)
        {
            $text .= "<tr><td class='field'>"
                  . "<h3 class='warning'>Sorry, no result found</h3>"
                  . "</td></tr>";
        }  
        
        return $text;
    }
    
    /**
     * Get the HTML to render the home and the search.
     * 
     * @param array $data associative array where the key is the field.
     * @return string HTML.
     */
    public function getHomeContent($data)
    {
        $text = "<form class='search' action='javascript:actionsearch();' id='searchform'>"
          . "<input type='search' id='search-box' autocomplete='off'>\n"
          . "\t<input type='submit' value='Search' id='search-button' onclick='javascript:actionsearch()'>\n"
          . "</form>\n";
        $text .= "<table id='search-table'>\n\t";
        
        foreach ($data as $item)
        {
            $text .= "<tr><td class='field'>". $item['shop_name'] . "</td>"
              . "<td>" . $item["address"] . ", " . $item["city"] . "</td></tr>";
        } 
        
        /*This is implemented here for coherence with the js equivalent*/
        if (!$data)
        {
            $text .= "<tr><td class='field'>"
                  . "<h3 class='warning'>Sorry, no result found</h3>"
                  . "</td></tr>";
        }   

        $text .= "\n</table>";
        
        return $text;
    }
    
    /**
     * Get the HTML of the confirmation.
     * 
     * @param string $shop_name the name of the shop
     * @return string HTML
     */
    public function getRemoveShopConfirmation($shop_name)
    {
        return "<h3>Congratulation, the shop $shop_name has been removed!</h3>";
    }
    
    /**
     * Get the HTML where asking for confirmation before removal.
     * 
     * @param string $shop_name the name of the shop
     * @param int $id the id of the shop
     * @return string HTML
     */
    public function getRemoveShopCertainty($shop_name, $id)
    {
        $text = "<h3>Are you sure to remove the following shop?</h3>"
              . "<form action='removeShop'>"
              . "<input type='text' name='shop_name' value='" . $shop_name . "' readonly>"
              . "<input type='hidden' name='id' value='" . $id . "'>"
              . "<input type='hidden' name='isSure' value='true'>"
              . "<input type='submit' value='Yes'>"
              . "</form>";
        $text .= "<form action='profile'><input type='submit' value='Cancel'></form>";
        
        return $text;
    }
    
    /**
     * Returns an informational message.
     * 
     * @return string HTML
     */
    public function getInfo()
    {
        $doxygen_link = "doxygen/html/index.html";
        $readme_link = "https://github.com/fcole90/fisherman-locator";
        return "<h3>Aim of the website</h3>\n"
        . "<p>This website has been developed in the scope of an achademic project "
          . "and has the aim of helping Fisherman Friend's enthusiasts "
          . "finding the best equipped resellers. This is done trough a search function "
          . "and a reporting function.</p>"
          . "<h3>Current state</h3>"
          . "<p>The current state has a minimum level of functionality that aims "
          . "to satisfy the project requirement but does not claim to be "
          . "a finished production ready product in any way.</p>"
          . "<h3>More infomation</h3>"
          . "<p>More information can be found in the "
          . "<a href='$readme_link' target='_blank'>"
          . "readme</a> of the project and in the "
          . "<a href='$doxygen_link' target='_blank'>Doxygen documentation</a>."
          . "</p>";
    }
}

