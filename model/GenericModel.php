<?php
relRequire("parsedown/Parsedown.php");

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
 * This is a generic model class.
 * Extend it if you're doing very simple pages that don't require
 * a specific model.
 */
class GenericModel extends Model
{
    
    private $error;
    
    public function __construct($request) {
        parent::__construct($request);
        $this->error = array();
    }
    
    /**
     * This function is required by the model.
     */
    public function show()
    {}
    
    /**
     * Handles the help page.
     */
    public function showHelpPage() {
        $filepath = __ROOT__ . "/README.md";
        
        if (!file_exists($filepath))
        {
            $this->error[] = "File not found: $filepath."
              . " Please contact the administrator.";
        }
            
        if(!($readme = fopen($filepath, "r")))
        {
            $this->error[] = "Could not open file: please contact the administrator.";
        }
        
        if(!($readme_text = fread($readme, filesize($filepath))))
        {
            $this->error[] = "Could not read file: please contact the administrator.";
        }
        
        $mark = new Parsedown();
        $content = $mark->text($readme_text);
        
        $page = new Presenter($this->getTitle());
        $page->setContent($content);
        $page->setError($this->error);
        $page->render();
        
        
        if ($readme)
        {
            fclose($readme);
        }
    }
}

