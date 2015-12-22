<?php
relRequire("model/Link.php");
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
 * Description of Nav
 *
 * @author fabio
 */
class Nav 
{
    private $otherLinks;
    private $linkClass = "fastlinks";
    private $moreLinkClass = "morelinks";
    
    /**
     * Logged in flag.
     * 
     * @var boolean
     */
    private $loggedIn;
    
    public function __construct($otherLinks = null)
    {
        if (isset($otherLinks))
        {
            $this->otherLinks = $otherLinks;
        }
        else $this->otherLinks = array();
    }
    
    public function render()
    {
        $first_link = array_shift($this->otherLinks);
        $login_profile_text = $first_link[0];
        $login_profile_link = $first_link[1];

        
        $text = <<<HTML
        
    <nav id="mainnav">
      <ul id="mainlist">
        <li class="$this->linkClass">
          <a href="$login_profile_link">
            <img id="login" src="assets/login.png" alt="login"/>
            <p>$login_profile_text</p>
          </a>
        </li>
        <li class="$this->linkClass">
          <a href="help">
            <img id="help" src="assets/help.png" alt="help"/>
            <p>HELP</p>
          </a>
        </li>
        <li id="showlinks" class="$this->linkClass">
          <a href="javascript:showlinks()">
            <img id="links" src="assets/links.png" alt="other links"/>
            <p>LINKS</p>
          </a>
        </li>
      </ul>
      <ul id="droplist">
        

          
HTML;
        echo $text;
        if (isset($this->otherLinks))
        {
            foreach ($this->otherLinks as $item) {
                $p = ucfirst($item[0]);
                $link = $item[1];
                echo <<<HTML
            <li class="$this->moreLinkClass">
              <a href="$link">
                <p>$p</p>
              </a>
            </li>
                  
HTML;
                
            }
        }
        
        echo <<<HTML

      </ul>
    </nav>

HTML;
    }
}
