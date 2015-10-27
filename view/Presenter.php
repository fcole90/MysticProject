<?php

relRequire('view/Head.php');
relRequire('view/Header.php');
relRequire('view/Nav.php');
relRequire('view/Footer.php');

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
 * Describes the whole view and renders the page.
 *
 * @author fabio
 */
class Presenter {
    
    /**
     * @var string
     */
    private $title;
    
    /**
     * @var string
     */
    private $session;
    /**
     * HTML for the content.
     * 
     * @var string
     */
    private $content;
    
    private $header;


    public function __construct($title, $content = "") 
    {
        $this->title = $title;
        $this->content = $content;        
    }
    
    public function setCustomHeader($header)
    {
        $this->header = $header;
    }

    
    /**
     * Prints the HTML indented correctly.
     * 
     * @param string $content multiline
     * @param string $indentation spaces indentation
     */
    public function printContent($content, $indentation = '      ')
    {
        echo "\n";
        foreach(explode(PHP_EOL, $content) as $line)
        {
            echo $indentation . $line;
            echo "\n";
        }
    }
    
    public function getContent()
    {
        return $this->content;
    }
    
    public function setContent($content)
    {
        $this->content = $content;
    }

        /**
     * Render the page
     */
    
    public function render()
    {
        if (isset($this->header))
        {
            header($this->header);
        }
        
        echo $this->session;
        
        echo "<!DOCTYPE html>\n";
        echo "<html>\n"; ///////// Start render
        
        new Head($this->title);
        
        echo "\n  <body>\n"; //////// Start body
        
        new Header();
        $nav = new Nav();
        $nav->render();
        unset($nav);
        
        echo "    <hr class=\"breakline\">\n";
        
        echo "\n\n    <!-- Main Content -->";
        echo "\n    <main>";
        //Here goes the main content
        $this->printContent($this->getContent());
        echo "    </main>\n\n";
        
        new Footer();
        
        echo "\n  </body>\n";//////// End body        
        
        echo "</html>"; /////// End render
    }
    
    
    
}
