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
 * The head.
 *
 * @author Fabio Colella
 */
class Head 
{
    public function __construct($title, $fields = "") 
    {
        if (!isset($title))
            echo "Error, missing title.";
        echo <<<HTML
  <head>
    <!-- Visible data -->
    <title>$title</title>

    <!-- Meta -->
    <meta name="description" content="Find your fresh lozeges around the world.">
    <meta name="title" content="Fisherman's Friend Locator">
    <meta name="author" content="Fabio Colella">
    <meta name="image" content="https://fisherman-locator.herokuapp.com/assets/logo-text-stretched.png">
    <meta name="url" content="https://fisherman-locator.herokuapp.com/index.php">

    <!-- Metadata -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    $fields
    <link rel="shortcut icon" href="favicon.ico" /> 
    <link rel="stylesheet" type="text/css" href="css/fonts.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/scripts.js"></script>
  </head>      
HTML;
    }
}
