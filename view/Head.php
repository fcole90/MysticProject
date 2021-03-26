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
        $meta_title = "Fisherman's Friend Locator";
        $meta_author = "Fabio Colella";
        $meta_description = "Find your fresh lozeges around the world.";
        $meta_url = "https://fisherman-locator.herokuapp.com";
        $meta_image = "$meta_url/assets/logo-text-stretched.png";
        $meta_image_alt = "Fisherman Locator Logo";


        if (!isset($title))
            echo "Error, missing title.";
        echo <<<HTML
  <head>
    <!-- Visible data -->
    <title>$title</title>
    <meta content=$meta_title>

    <!-- Meta -->
    <meta property="og:title" content="$meta_title">
    <meta property="og:description" content="$meta_description">
    <meta property="og:image" content="$meta_image">
    <meta property="og:url" content="$meta_url">
    <meta property="og:site_name" content="$meta_title">
    <meta name="twitter:card" content="$meta_image">
    <meta name="twitter:image:alt" content="$meta_image_alt">
    <meta name="twitter:site" content="@fcole90">

    <meta name="description" content="$meta_description">
    <meta name="title" content="$meta_title">
    <meta name="author" content="$meta_author">
    <meta name="image" content="$meta_image">
    <meta name="url" content="$meta_url">

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
