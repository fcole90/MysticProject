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
 * Form to sign up to the website.
 */
class SignUpForm
{   
    
    public function getForm(User $user, &$error)
    {
        foreach ($user->fieldList() as $field)
        {
            $$field = $user->get($field); //Get the variable of the variable
        }
        $warning = "";
        if (isset($error))
        {
            $warning = "\n";
            foreach ($error as $message)
            {
                $warning .= "<h3 class=\"warning\">$message</h3>\n";
            }
        }
        
        $form = <<<HTML
<h2>Fill in your data</h2>$warning
<form action="" method="post">
    <p>First name:</p>
    <input type="text" name="firstname" value="$firstname" required="true">
    <br>
    <p>Second name:</p>
    <input type="text" name="secondname" value="$secondname" required="true">
    <br>
    <p>Email:</p>
    <input type="email" name="email" value="$email" required="true">
    <br>
    <p>Username:</p>
    <input type="text" name="username" value="$username" required="true">
    <br>
    <p>Password:</p>
    <input type="password" name="password" value="$password" required="true">
    <p>Birthday:</p>
    <input type="date" name="birthdate" value="$birthdate" required="true" placeholder="YYYY-MM-DD">
    <br>
    <input type="submit">
    
</form>
HTML;
        return $form;
    }
    
    public function getConfirmation(User $user)
    {
        foreach ($user->fieldList() as $field)
        {
            $$field = $user->get($field);
        }
        
        $confirm = "<h2>Congratulations, you're now registered.</h2>\n";
        
        foreach ($user->fieldList() as $field)
        {
            if ($$field != false && $field!="password")
            {
                $confirm .= "<p>$field: " . $$field . "</p>\n";
            }
        }
        return $confirm;
    }
    
    public function getErrorDatabase()
    {
        $error = "<h2>Sorry, an error occurred in the signup process.</h2>\n";
        $error .= "<p>If this error happens again, please"
          . " contact the administrator.</p>";
    }
}


