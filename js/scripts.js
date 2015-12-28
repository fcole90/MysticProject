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
 * Enables a menu to display additional links.
 * 
 * @returns {void}
 */
function showlinks()
{
    if ($("#droplist").css("position") !== "static")
    {
      $("#droplist").slideToggle("fast");
    }
}

/**
 * Populates the table to display the results from the json data.
 * 
 * @param {type} json json data.
 * @param {type} rem_button switch to add an additional column of buttons.
 * @returns {undefined}
 */
function populateSearchTable(json, rem_button)
{
    var data = JSON.parse(json);
    list = "";
    //Reset the content of the table and repopulate it
    for	(index = 0; index < data.length; index++) 
    {
        list += "<tr><td class='field'>" 
              + data[index]['shop_name'] + "</td>"
              + "<td>" + data[index]["address"] 
              + ", " + data[index]["city"] + "</td></tr>";
        if (rem_button)
        {
            list += "<tr class='tr-removal'><td><form action='removeShop'>"
              + "<input type='hidden' name='shop_name' value='" + data[index]['shop_name'] + "'>"
              + "<input type='hidden' name='id' value='" + data[index]['id'] + "'>"
              + "<input type='submit' value='Remove'></form></td></tr>";
        }

    }
    if (data.length === 0)
    {
        list += "<tr><td class='field'>"
              + "<h3 class='warning'>Sorry, no result found</h3>"
              + "</td></tr>";
    }
    $('#search-table').html(list);
}

/**
 * Uses ajax to retrieve data from the search text.
 * 
 * On error it alerts the user with a popup, otherwise it loads the data.
 * 
 * @param {type} searchtext the text to search.
 * @param {type} rem_button switch to add an additional column of buttons in the called function.
 * @returns {undefined}
 */
function search(searchtext, rem_button)
{
    $.ajax({url: "ajaxSearchShop", 
            data: {
                searchstring: searchtext,
                format: 'json'
            },
            success: function(data) {populateSearchTable(data, rem_button);},
            error: function() {alert("Sorry, something didn't work and we couldn't make the search.");}
        });
}

/**
 * Shortcut to trigger the search.
 * @returns {undefined}
 */
function actionsearch()
{
    search($("#search-box").val(), false);
}

/**
 * Shortcut to trigger the admin search.
 * @returns {undefined}
 */
function actionsearchAdmin()
{
    search($("#search-box-admin").val(), true);
}

/**
 * The main function that get's called when the page is ready.
 */
$(document).ready(function(){
    
    /**
     * Event to make a funny 404 page.
     */
    $("#err404").animate({left: '+=50000px'}, 75000, "swing");
    
    /**
     * @type Boolean mutex for the searches when typing.
     */
    var timer_search_lock = true;
    
    /**
     * Unlocks the mutex after 50ms to prevent sending too many requests to the 
     * database while typing.
     * 
     * @returns {undefined}
     */
    function unlock()
    {
        if (!timer_search_lock)
        {
            setTimeout(function() {timer_search_lock = true;}, 50);                
        }
    }
    
    /**
     * Event to trigger a search while typing in the search box.
     */
    $( "#search-box" ).keypress(function() {
          
        if(timer_search_lock)
        {
            search($("#search-box").val(), false);
            timer_search_lock = false;
        }
        else
        {
            unlock();
        }     
    });
    
    /**
     * Event to trigger a search while typing in the search box of an admin.
     */
    $( "#search-box-admin" ).keypress(function() {
          
        if(timer_search_lock)
        {
            search($("#search-box-admin").val(), true);
            timer_search_lock = false;
        }
        else
        {
            unlock();
        }
         
    });
    
    /**
     * Prevents reloading the page and triggers the search.
     */
    $( "#search-box" ).on('search', function (e) {
            e.preventDefault();
            actionsearch();
    });
    
    /**
     * Prevents reloading the page and triggers the search of an admin.
     */
    $( "#search-box-admin" ).on('search', function (e) {
            e.preventDefault();
            actionsearchAdmin();
    });

});
