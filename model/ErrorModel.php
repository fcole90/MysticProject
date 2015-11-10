<?php
relRequire("model/Model.php");
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
 * Description of ErrorModel
 *
 * @author fabio
 */
class ErrorModel extends Model
{
    private $message;
    private $header;
    private $title;
    
    public function __construct($title, $message) {
        parent::__construct();
        $this->title = $title;
        $this->message = $message;
    }
    
    public function show()
    {
        $page = new Presenter( $this->title, $this->getContent());
        if (isset($this->header))
        {
            $page->setCustomHeader($this->header);
        }
        $page->render();
    }
    
    public function getMessage()
    {
        return $this->message;
    }

    public function getContent()
    {
        $message = $this->getMessage();
        $content = <<<HTML
<h2 class="error">Error</h2>
<p class="error">$message</p>
HTML;
        return $content;
    }
    
    public function setHeader($header)
    {
        $this->header = $header;
    }
    
    public function getHeader()
    {
        return $this->header;
    }
}
