<?php

relRequire('view/Head.php');
relRequire('view/Header.php');
relRequire('view/Nav.php');
relRequire('view/Footer.php');

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
 * Describes the whole view and renders the page.
 *
 * @author Fabio Colella
 */
class Presenter {
    
    /**
     * @var string title of the page.
     */
    private $title;
    
    /**
     * @var string redirect link.
     */
    private $redir;
    
    /**
     * @var int time before redirect.
     */
    private $redir_time;
    
    /**
     * @var array list of error messages.
     */
    private $error;

    /**
     * @var string HTML for the content.
     */
    private $content;
    
    /**
     * @var string custom HTML header.
     */
    private $header;
    
    
    /**
     * @var array[] links to display in the form (title, link).
     */
    private $links;


    /**
     * View constructor.
     * 
     * @param string $title of the page
     * @param array[] $links in the form (title, link)
     * @param string $content of the page
     */
    public function __construct($title, $links, $content = "") 
    {
        $this->title = $title;
        $this->content = $content;      
        $this->links = $links;
    }
    
    /**
     * Sets a custom header for the page.
     * 
     * @param string $header HTML header
     */
    public function setCustomHeader($header)
    {
        $this->header = $header;
    }

    
    /**
     * Prints the HTML indented correctly.
     * 
     * @param string $content multiline HTML content.
     * @param string $indentation a string containing spaces for the indentation.
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
    
    /**
     * Return some content to render.
     * 
     * @return string HTML content.
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Receive some content to render.
     * 
     * @param string $content HTML content.
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Render the page.
     */    
    public function render()
    {
        if (isset($this->header))
        {
            header($this->header);
        }
        
        
        echo "<!DOCTYPE html>\n";
        echo "<html>\n"; ///////// Start render
        
        new Head($this->title, $this->printRedir());
        
        echo "\n  <body>\n"; //////// Start body
        
        new Header();
        $nav = new Nav($this->links);
        $nav->render();
        unset($nav);
        
        echo "    <hr class=\"breakline\">\n";
        
        echo "\n\n    <!-- Main Content -->";
        echo "\n    <main>";
        
        //Error list
        echo $this->printErrorList();
        
        //Here goes the main content
        $this->printContent($this->getContent());
        echo "    </main>\n\n";
        
        new Footer();
        
        echo "\n  </body>\n";//////// End body        
        
        echo "</html>"; /////// End render
    }
    
    /**
     * Render a json formatted content
     */
    public function json()
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        echo $this->content;
    }
    
    /**
     * Sets the error list.
     * @param array $errorArray a list of error messages.
     */
    public function setError($errorArray = array()) 
    {
        $this->error = $errorArray;
    }
    
    /**
     * Returns the HTML code of the error list.
     * @return string HTML list of error messages.
     */
    public function printErrorList() 
    {
        $warning = "";
        if (isset($this->error) && !empty($this->error))
        {
            $warning = "\n";
            foreach ($this->error as $message)
            {
                $warning .= "<h3 class=\"warning\">$message</h3>\n";
            }
        }
        return $warning;
    }
    
    /**
     * Sets a redirect in the page.
     * 
     * @param string $link the link to which redirect.
     * @param type $sec (optional) the time to wait before redirect.
     */
    public function setRedir($link = "index", $sec = 6) 
    {
        $this->redir = $link;
        $this->redir_time = $sec;
    }
    
    /**
     * Returns the HTML code for the redirect.
     * 
     * @return string HTML of the redirect.
     */
    public function printRedir()
    {
        if (isset($this->redir))
        {
            $link = $this->redir;
            $time = $this->redir_time;
            $text = <<<HTML
<!-- HTML meta refresh URL redirection -->
<meta http-equiv="refresh"
content="$time; url=$link.php">
HTML;
        }
        else
        {
            return "";
        }
        return $text;

    }
    
    /**
     * Set the title of the page.
     * 
     * @param string $title
     */
    public function setTitle($title) 
    {
        $this->title = $title;
    }    
}
