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
              + ", " + data[index]["city"] + "</td>";
        if (rem_button)
        {
            list += "<td><input type='button' value='remove' id='removeShop'</td>";
        }
        list += "</tr>";
    }
    if (data.length === 0)
    {
        list += "<tr><td class='field'>"
              + "<h3 class='warning'>Sorry, no result found</h3>"
              + "</td></tr>";
    }
    $('#search-table').html(list);
}

function search(searchtext, rem_button)
{
    $.ajax({url: "ajaxSearchShop", 
            data: {
                searchstring: searchtext,
                format: 'json'
            },
            success: function(data) {populateSearchTable(data, rem_button);},
            error: function() {alert("Ajax error!");}
        });
}

function actionsearch()
{
    search($("#search-box").val(), false);
}

function actionsearchAdmin()
{
    search($("#search-box-admin").val(), true);
}


$(document).ready(function(){
    
    //var map = L.map('map').setView([51.505, -0.09], 13);
    $("#err404").animate({left: '+=50000px'}, 75000, "swing");
      
    var timer_search_lock = true;
    
    //unlocks the time lock
    function unlock()
    {
        if (!timer_search_lock)
        {
            setTimeout(function() {timer_search_lock = true;}, 50);                
        }
    }
      
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
      
    $( "#search-box" ).on('search', function (e) {
            e.preventDefault();
            actionsearch();
    });
    
    $( "#search-box-admin" ).on('search', function (e) {
            e.preventDefault();
            actionsearchAdmin();
    });

});
