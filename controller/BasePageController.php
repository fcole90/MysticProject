<?php
relRequire('controller/Controller.php');

relRequire('view/Presenter.php');
relRequire("view/SignUpForm.php");

relRequire('model/HomeModel.php');
relRequire('model/SignUpModel.php');
relRequire("model/User.php");
relRequire('model/LoginModel.php');
relRequire("model/ErrorModel.php");
relRequire("model/GenericModel.php");
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
 * Handles the pages.
 *
 * @author fabio
 */
class BasePageController extends Controller
{
    /**
     * The minimum length for the password
     * 
     * @var string
     */
    const PASSWORD_MIN_LEN = 6;
    
    /**
     * Array of errors to be reported.
     * 
     * @var string[]
     */
    private $error;
    
    public function __construct(&$request)
    {
        parent::__construct($request);
        $this->error = array();
    }
    
    
    /***********************************************
     * Page handling functions.                    *
     ***********************************************/
    
    
    /**
     * Renders the home page.
     * 
     * @param array $request
     */
    public function home() 
    {       
        $model = new HomeModel();
        
        $page = new Presenter($this->getTitle());
        
        /* Temporary HTML */
        $content = <<<HTML
<h2>Find your lozenges in "Fleetwood"</h2>
<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.openstreetmap.org/export/embed.html?bbox=-3.1280136108398438%2C53.86346094359846%2C-2.8873443603515625%2C53.985568980647656&amp;layer=mapnik&amp;marker=53.924650964860085%2C-3.007637200000005" style="border: 1px solid black"></iframe><br/><small><a href="http://www.openstreetmap.org/?mlat=53.9247&amp;mlon=-3.0076#map=12/53.9247/-3.0076">View Larger Map</a></small>
HTML;
        $page->setContent($content);
        $page->render();
    }
    
    /**
     * Handles the signup process.
     */
    public function signup()
    {
        $page = new Presenter($this->getTitle());
        
        /* Setup the fields to be used and initialize a User object */
        $this->setFields();
        
        $model = new SignUpModel();
        
        /* Check if the fields follow the necessary rules */
        if (!$this->checkFieldsSignUp($model))
        {
            /* Add the errors of the model to the errors of the controller */
            $this->error = array_merge($this->error, $model->getError());
            $page->setContent((new SignUpForm())->getForm($this->user, $this->error));
        }
        else if($model->addUserToDatabase($this->user))
        {
            $page->setContent((new SignUpForm())->getConfirmation($this->user));
            $page->setRedir();
        }
        else
        {
            /* Add the errors of the model to the errors of the controller */
            $this->error = array_merge($this->error, $model->getError());
            $page->setContent((new SignUpForm())->getForm($this->user, $this->error));
        }
                
        $page->render();
        
    }
    
    public function login(&$request)
    {
        $model = new LoginModel($request);
        $model->show();
    }
    
    public function logout($request) {
       $model = new LoginModel($request);
       $model->logout();
    }
    
    public function help($request) {
        $model = new GenericModel($request);
        $model->showHelpPage();
    }
    
    /**
     * Handles the 404 error
     * @param request $request
     */
    public function err404()
    {
        $title = "Error 404 - Page not found";
        $message = "Sorry, the page you're looking for "
          . "does not exist or has been moved.";
        $model = new ErrorModel($title, $message);
        $model->setHeader("HTTP/1.0 404 Not Found");
        $model->show();
    }
    
    /**
     * Handles the 403 error.
     * @param request $request
     */
    public function err403(&$request)
    {
        $title = "Error 403 - Forbidden";
        $message = "You're attempting to access an unauthorized "
          . "area. If you think you should be able to access this area "
          . "contact your administrator.";
        $model = new ErrorModel($request, $title, $message);
        $model->setHeader("HTTP/1.0 403 Forbidden");
        $model->show();
    }
    
    /***********************************
     * Helper functions.               *
     ***********************************/

    /**
     * Setup the user variable feeding it with the form fields.
     */
    public function setFields()
    {
        $this->user = new User();
        foreach ($this->user->fieldList() as $field)
        {
            if (isset($this->request[$field]))
            {
                $this->user->set($field, $this->safeInput($this->request[$field]));
            }
            else
            {
                $this->user->set($field, null);
            }
        }
        
        /** Additonal control to obtain the birthdate **/
        if(isset($this->request["year"]) && isset($this->request["month"]) && isset($this->request["day"]))
        {
            $birthdate = $this->getDate($this->safeInput($this->request["year"]), 
                                        $this->safeInput($this->request["month"]), 
                                        $this->safeInput($this->request["day"]));
        }
        else
        {
            $birthdate = null;
        }
        $this->user->set("birthdate", $birthdate);
    }
    
    /**
     * Marks the fields wich require attention.
     */
    public function setWarning($field)
    {
        return $field . '" class="warning';
    }
    
    /**
     * Checks the fields one by one.
     * 
     * If adding new fields this class needs to be edited.
     * @param Model $model
     * @return boolean checkPassed
     */
    public function checkFieldsSignUp(SignUpModel $model)
    {
        /**First check is the fields exist**/
        $isValid = false;
        
        foreach ($this->user->fieldList() as $field)
        {
            if (null !== $this->user->get($field))
            {
                $isValid = true;
            }
        }
        
        /** Skip other checks if none of the fields is set**/
        if (!$isValid) return false;
       
        /**Then if they're valid**/
        $current = "username";
        /** Usernames are lowercase only, but uppercase can be accepted and converted to lowercase. **/
        $this->user->set($current, strtolower($this->user->get($current)));
        if (!$this->checkCharDigit($current, $this->user->get($current)) ||
          !$model->checkFieldNotExists($current, $this->user->get($current)))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
        
        
        
        $current = "firstname";
        if (!$this->checkCharDigit($current, $this->user->get($current)))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
        
        $current = "secondname";
        if (!$this->checkCharDigit($current, $this->user->get($current)))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
        
        $current = "password";
        if (!$this->checkPassword($current, $this->user->get($current)))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
        
        $current = "email";
        if (!$this->checkEmail($current, $this->user->get($current)) ||
          !$model->checkFieldNotExists($current, $this->user->get($current)))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
        
        
        
        $current = "birthdate";
        if (!$this->checkDate($current, $this->user->get($current)))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
     
        return $isValid;
    }
    
    
    /**
     * Returns a standard date in the YYYY-MM-DD format.
     * 
     * @param type $year
     * @param type $month
     * @param type $day
     * @return string
     */
    function getDate($year="", $month="", $day="")
    {
        return "$year-$month-$day";
    }
    
    /**
     * Helper function to check if strings contain only chars and digits.
     */
    public function checkCharDigit($field, $value)
    {
        if (!preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/",$value)) 
        {
            $this->error[] = "Only letters and numbers are allowed in $field."
              . " (Numbers not at beginning).";
            return false;
        }
        return true;
    }
    
    /**
     * Helper function to check if the string is strong enough to be used as a password.
     */
    public function checkPassword($field, $value)
    {
        if (!preg_match("/[0-9]+/",$value) || !preg_match("/[A-Z]+/",$value) 
          || !preg_match("/[a-z]+/",$value) || !(strlen($value)>= BasePageController::PASSWORD_MIN_LEN)) 
        {
            $this->error[] = ucfirst($field) . " must be at least "
              . BasePageController::PASSWORD_MIN_LEN ." chars long and have at least: "
              . "one digit, "
              . "one upper case letter "
              . "and one lower case letter.";
            return false;
        }
        return true;
    }
    
    /**
     * Helper function to check if the string is a valid email address.
     */
    public function checkEmail($field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            $this->error[] = "The $field is not valid.";
            return false;
        }
        return true;
    }
    
    public function checkDate($field, $value)
    {
        $date = explode("-", $value);
        if (!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $value) ||
          !checkdate($date[1], $date[2], $date[0]))
        {
            $this->error[] = "The $field is not valid or does not correspond "
              . "to the required date format: "
              . "YYYY-MM-DD.";
            return false;
        }
        return true;
    }
    
}
