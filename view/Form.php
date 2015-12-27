<?php
relRequire("model/User.php");
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
 * Views for many kinds of forms.
 */
class Form
{   
    /**
     * Returns a form for the signup process.
     * 
     * @param User $user the user object holding the data of the fields.
     * @return string HTML of the form.
     */
    public function getSignupForm(User $user)
    {
        
        foreach ($user->fieldList() as $field)
        {
            $$field = $user->get($field); //Get the variable of the variable
        }
        
        /** Set the latest choices for the date fields */
        if (isset($birthdate) && $birthdate !="")
        {
            $date = explode("-", $birthdate);
            $day = $this->generateDaysOptions($date[2]);
            $month = $this->generateMonthsOptions($date[1]);
            $year = $this->generateYearsOptions($date[0]);
        }
        else
        {
            $day = $this->generateDaysOptions();
            $month = $this->generateMonthsOptions();
            $year = $this->generateYearsOptions();
        }

        $form = <<<HTML
<h2>Fill in your data:</h2>
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
    <br>
    <p>Birthday:</p>
    $day
    $month
    $year
    <br>
    <input type="submit" value="SignUp!">
    
</form>
HTML;
        return $form;
    }
    
    /**
     * Returns the HTML code of the confirmation.
     * 
     * @param User $user the user object containing the data.
     * @return string HTML code.
     */
    public function getSignupConfirmation(User $user)
    {
        foreach ($user->fieldList() as $field)
        {
            $$field = $user->get($field);
        }
        
        $confirm = "<h2>Congratulations, you're now registered.</h2>\n";
        $confirm .= "<p>You will be redirected to <a href='home'>the homepage</a>. "
          . "Please click <a href='home'>here</a> if you're not automatically "
          . "redirected.</p>\n\n";
        
        foreach ($user->fieldList() as $field)
        {
            if ($$field != false && $field!="password")
            {
                $confirm .= "<p>$field: " . $$field . "</p>\n";
            }
        }
        return $confirm;
    }
    
    /**
     * Get the HTML code of the error.
     */
    public function getSignupErrorDatabase()
    {
        $error = "<h2>Sorry, an error occurred in the signup process.</h2>\n";
        $error .= "<p>If this error happens again, please"
          . " contact the administrator.</p>";
    }
    
    
    /**
     * Returns a login form.
     * 
     * @param string $username the username.
     * @return string HTML.
     */
    public function getLoginForm($username)
    {
        
        $form = <<<HTML
<h2>Please, insert your username and password:</h2>
<form action="" method="post">
    <p>Username: </p>
    <input type="text" name="username" value="$username" required="true">
    <br>
    <p>Password: </p>
    <input type="password" name="password" value="" required="true">
    <br>
    <input type="submit" value="Login">
    
</form>
HTML;
        return $form;
    }
    
    /**
     * Returns a login confirmation.
     * 
     * @param string $username the username.
     * @return string HTML code.
     */
    public function getLoginConfirmation($username)
    {
        $form = <<<HTML
<h2>Welcome, $username.</h2>
<p>You're being redirected to <a href="home">the homepage</a>.
   Please <a href="home">click here</a> if you're not automatically redirected.</p>

HTML;
        return $form;
    }
    
    
    /**
     * Get the code to say goodbye.
     * 
     * @param string $username the username
     * @return string HTML code.
     */
    public function getLogout($username)
    {
        $form = <<<HTML
<h2>Goodbye, $username</h2>
<p>You're being redirected to <a href="home">the homepage</a>.
   Please <a href="home">click here</a> if you're not automatically redirected.</p>
          
HTML;
        return $form;
    }
    
    /**
     * Returns a shop form.
     * 
     * @param array[] $data associative array where the field name is the key.
     * @return string HTML code.
     */
    public function getAddshopForm($data)
    {
        
        foreach ($data as $key => $value)
        {
            $$key = $value; //Get the variable of the variable
        }
                
        $form = <<<HTML
<h2>Fill in your data:</h2>
<form action="" method="post">
    <p>Shop Name:</p>
    <input type="text" name="shop_name" value="$shop_name" required="true">
    <br>
    <p>City:</p>
    <input type="text" name="city" value="$city" required="true">
    <br>
    <p>Address:</p>
    <input type="text" name="address" value="$address" required="true">
    <br>

    <input type="submit" value="Add shop!">
    
</form>
HTML;

        return $form;
    }
    
    
    /**
     * Create a login confirmation.
     * 
     * @param string $username the username.
     * @return string HTML code.
     */
    public function getAddshopConfirmation()
    {
        $form = <<<HTML
<h2>Your shop has been succesfully registered.</h2>
<p>You're being redirected to <a href="home">the homepage</a>.
   Please <a href="home">click here</a> if you're not automatically redirected.</p>

HTML;
        return $form;
    }
    
    
    /************************************
     * Helper view functions            *
     ************************************/  
    
    /**
     * Generates a months dropdown menu.
     * @param $selected (optional) a default option to set.
     * @return string the HTML code of the dropdown.
     */
    public function generateMonthsOptions($selected = "") 
    {
        $monthOptions = "<select name='month'>\n";
        $months = ["January",
                    "February",
                    "March",
                    "April",
                    "May",
                    "June",
                    "July",
                    "August",
                    "September",
                    "October",
                    "November",
                    "December"];
        foreach($months as $month)
        {
            if (!isset($i)){$i = 1;}
            /* Define the default option */
            $val = $i < 10 ? "0".$i : $i;
            $sel = ($val == $selected) ? "selected" : "";
            $monthOptions .= "<option $sel value='$val'>$month</option>\n";
            ++$i;
        }
        $monthOptions .= "</select>\n";
        return $monthOptions;
    }
    
   /**
     * Generates a days dropdown menu.
      * @param $selected (optional) a default option to set.
     * @return string the HTML code of the dropdown.
     */
    public function generateDaysOptions($selected = "") 
    {
        $options = "<select name='day'>\n";
        
        for($i = 1; $i<=31; ++$i )
        {
            $val = $i < 10 ? "0".$i : $i;
            $sel = ($val == $selected) ? "selected" : "";
            $options .= "<option $sel value='$val'>$val</option>\n";
        }
        $options .= "</select>\n";
        return $options;
    }
    
    /**
     * Generates a years dropdown menu.
     * @param $selected (optional) a default option to set.
     * @return string the HTML code of the dropdown.
     */
    public function generateYearsOptions($selected = "") 
    {
        $options = "<select name='year'>\n";
        $currentYear = date("Y");
        for($i = 1900; $i<=$currentYear; ++$i)
        {
            $sel = ($i == $selected) ? "selected" : "";
            $options .= "<option $sel value='$i'>$i</option>\n";
        }
        $options .= "</select>\n";
        return $options;
    }
}


