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

/**
 * All controllers should inherit from here
 * 
 * @author fabio
 */
abstract class Controller {
    
    /**
     *
     * @var string
     */
    protected $baseTitle = "Fisherman's Friend Locator";
    
    /**
     * 
     * @return string
     */
    public function baseTitle()
    {
        return $this->baseTitle;
    }
    
    /**
     * 
     * @param array $request
     * @return string
     */
    public function pageTitle($page) 
    {
        return ucfirst($page) . " - " . $this->baseTitle();
    }
    
    /**
     * Handles the page.
     * 
     * @param $request array the _REQUEST array 
     */
    abstract function run(&$request);
}