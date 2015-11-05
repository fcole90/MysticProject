<?php
relRequire('model/Model.php');
relRequire('view/Presenter.php');


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
 * Description of HomeModel
 *
 * @author fabio
 */
class HomeModel extends Model
{
    //invoke the db
    
    public function __construct($request) {
        parent::__construct($request);
    }
    
    //show the view
    function show()
    {
        $page = new Presenter($this->getTitle());
        
        /* Temporary HTML */
        $content = <<<HTML
<h2>Find your lozenges in "Fleetwood"</h2>
<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.openstreetmap.org/export/embed.html?bbox=-3.1280136108398438%2C53.86346094359846%2C-2.8873443603515625%2C53.985568980647656&amp;layer=mapnik&amp;marker=53.924650964860085%2C-3.007637200000005" style="border: 1px solid black"></iframe><br/><small><a href="http://www.openstreetmap.org/?mlat=53.9247&amp;mlon=-3.0076#map=12/53.9247/-3.0076">View Larger Map</a></small>
HTML;
        $page->setContent($content);
          
        
        $page->render();
    }
}
