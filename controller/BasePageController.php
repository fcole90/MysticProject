<?php
relRequire('controller/Controller.php');

relRequire('view/Presenter.php');
relRequire("view/Form.php");
relRequire('view/GenericViews.php');

relRequire('model/HomeModel.php');
relRequire('model/ShopModel.php');
relRequire("model/User.php");
relRequire('model/UserAccessModel.php');
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
    const USER_MIN_LEN = 5;
    
    /**
     * Array of errors to be reported.
     * 
     * @var string[]
     */
    private $error;
    
    /**
     * @var Presenter View of the page.
     */
    private $presenter;
    
    public function __construct(&$request)
    {
        parent::__construct($request);
        $this->error = array();
        $this->presenter = new Presenter($this->getTitle(), $this->getLinks());
    }
    
    
    /***********************************************
     * Page handling functions.                    *
     ***********************************************/
    
    
    /**
     * Renders the home page.
     * 
     * @param array $request
     */
    public function loadPageHome() 
    {               
        $model = new ShopModel();
        $data = $model->getData();
        if ($data)
        {
            $content = (new GenericView)->getHomeContent($data);
            $this->presenter->setContent($content);
        }
        else
        {
            $this->error[] = "There has been an error retrieving data.";
        }
        
        $this->concatErrorArray($model->getError());
        $this->presenter->setError($this->error);
        $this->presenter->render();
    }
    
    /**
     * Handles the signup process.
     */
    public function loadPageSignup()
    {
        
        if($this->isLoggedIn())
        {
            $this->error[] = "You're already logged in and signed up!";
            $this->presenter->setContent((new Form())->getLoginConfirmation($this->username));
            $this->presenter->setError($this->error);
            $this->presenter->setRedir();
            $this->presenter->render();
            return;
        }
        
        /* Setup the fields to be used and initialize a User object */
        $this->setSignupFields();
        
        $model = new UserAccessModel();
        
        /* Check if the fields follow the necessary rules */
        if (!$this->checkFieldsSignUp($model))
        {
            /* Add the errors of the model to the errors of the controller */
            $this->error = array_merge($this->error, $model->getError());
            $this->presenter->setContent((new Form())->getSignupForm($this->user, $this->error));
        }
        else if($model->addUserToDatabase($this->user))
        {
            $this->presenter->setContent((new Form())->getSignupConfirmation($this->user));
            $this->presenter->setRedir();
        }
        else
        {
            /* Add the errors of the model to the errors of the controller */
            $this->error = array_merge($this->error, $model->getError());
            $this->presenter->setContent((new Form())->getSignupForm($this->user, $this->error));
        }
                
        $this->presenter->render();
        
    }
    
    public function loadPageLogin()
    {
                
        if($this->isLoggedIn())
        {
            $this->error[] = "You're already logged in!";
            $this->presenter->setContent((new Form())->getLoginConfirmation($this->username));
            $this->presenter->setError($this->error);
            $this->presenter->setRedir();
            $this->presenter->render();
            return;
        }
        
        /* No field is set, probably coming here from for first time */
        if(!isset($this->request["username"]) && !isset($this->request["password"]))
        {
            $this->presenter->setContent((new Form())->getLoginForm("", $this->error));
            $this->presenter->render();
            return;
        }
        
        /* Only username or password have not been set, warning. */
        if (!isset($this->request["username"]) || !isset($this->request["password"]))
        {
            $this->error[] = "Please, mind that every field is mandatory.";
            $this->presenter->setContent((new Form())->getLoginForm("", $this->error));
            $this->presenter->render();
            return;
        }
        
        $username = $this->safeInput($this->request["username"]);
        $password = $this->safeInput($this->request["password"]);
        $model = new UserAccessModel();
        
        /* The user filled the fields but either the username or the password is wrong */
        if(!$model->checkLoginData($username, $password))
        {
            $this->concatErrorArray($model->getError());
            $this->presenter->setContent((new Form())->getLoginForm($username, $this->error));
            $this->presenter->render();
        }
        else /* The user logged correctly and a session gets opened. */
        {
            $_SESSION["username"] = $username;
            $this->presenter->setContent((new Form())->getLoginConfirmation($username));
            $this->presenter->setRedir();
            $this->presenter->render();
        }        
    }
    
    /**
     * Lets a user log out.
     */
    public function logout() 
    {
         
        if($this->isLoggedIn())
        {

            $this->presenter->setContent((new Form())->getLogout($this->getSessionUsername()));
            $this->closeSession();
            $this->presenter->setRedir();
            $this->presenter->render();
        }
        else
        {
            $this->error[] = "You're not logged in yet.";
            $this->loadPageLogin();
        }
    }
    
    
    /**
     * Page to add a new fisherman shop.
     */
    public function loadPageAddshop() 
    {
        if(!$this->isLoggedIn())
        {
            $this->error[] = "You need to log in to visit this page!";
            $this->loadPageLogin();
            return;
        }
        
        $this->presenter->setTitle("Add a shop");
        
        $data = $this->setAddshopData();

        
        if(isset($this->request["address"]))
        {
            $model = new ShopModel();
            if($this->checkFieldsAddshop($data) && $model->addShopToDatabase($data))
            {
                $this->presenter->setContent((new Form)->getAddshopConfirmation($data));
                $this->presenter->setRedir();
                $this->presenter->render();
                return;
            }
            else
            {
                $this->concatErrorArray($model->getError());
                $this->presenter->setError(array("Something went wrong.."));
                $this->presenter->setContent((new Form)->getAddshopForm($data, $this->error));
                $this->presenter->render();
                return;
            }
            
        }
        else
        {
            $this->presenter->setContent((new Form)->getAddshopForm($data, $this->error));
            $this->presenter->render();
        }
    }
    
    public function loadPageHelp() 
    {    
        phpinfo();
    }
    
    /**
     * Handles the profile page.
     */
    public function loadPageProfile()
    {
        if (!$this->isLoggedIn())
        {
            $this->error[] = "You're not logged in!";
            $this->loadPageLogin();
        }
        else
        {
            $model = new UserAccessModel;
            $user = $model->getUser($this->username);
            $this->concatErrorArray($model->getError());
            
            if($user)
            {
                $this->presenter->setContent((new GenericView)->getProfileView($user));
            }
            else
            {
                $this->error[] = "Something naughty happened retrieving the data!";
            }
            
            $this->presenter->setError($this->error);
            $this->presenter->render();            
        }
    }
    
    
    public function loadPageAjaxSearchShop()
    {
        $model = new ShopModel();
        if (isset($this->request["searchstring"]))
        {
            $search = $this->safeInput($this->request["searchstring"]);
            $data = $model->getData($search);
        }
        else
        {
            $data = $model->getData();
        }
        $json = json_encode($data);
        $this->presenter->setContent($json);
        $this->presenter->json();
    }
    
    /**
     * Handles the 404 error
     * @param request $request
     */
    public function loadPageErr404()
    {
        $title = "Error 404 - Page not found";
        $message = "Sorry, the page you're looking for "
          . "does not exist or has been moved.";
        $this->presenter->setError(array($message));
        $this->presenter->setCustomHeader("HTTP/1.0 404 Not Found");
        $this->presenter->setContent("<img id='err404'src='https://media3.giphy.com/media/tj2MwoqitZLtm/giphy.gif'>");
        $this->presenter->setRedir("index", 10);
        $this->presenter->render();
    }
    
    /**
     * Handles the 403 error.
     * @param request $request
     */
    public function loadPageErr403()
    {
        $title = "Error 403 - Forbidden";
        $message = "You're attempting to access an unauthorized "
          . "area. If you think you should be able to access this area "
          . "contact your administrator.";
        $this->presenter->setError(array($message));
        $this->presenter->setCustomHeader("HTTP/1.0 403 Forbidden");
        $this->presenter->render();
    }
    
    /***********************************
     * Helper functions.               *
     ***********************************/

    /**
     * Setup the user variable feeding it with the form fields.
     */
    public function setSignupFields()
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
    public function checkFieldsSignUp(UserAccessModel $model)
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
        if (!$this->checkCharDigit($current, $this->user->get($current), BasePageController::USER_MIN_LEN) ||
          !$model->checkFieldNotExists($current, $this->user->get($current)))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
        
        
        
        $current = "firstname";
        if (!$this->checkCharSpaces($current, $this->user->get($current)))
        {
            $this->user->set($current, $this->setWarning($this->user->get($current)));
            $isValid = false;
        }
        
        $current = "secondname";
        if (!$this->checkCharSpaces($current, $this->user->get($current)))
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
    public function checkCharDigit($field, $value, $length = -1)
    {
        $flag = true;
          
        if (!preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/",$value)) 
        {
            $this->error[] = "Only letters and numbers are allowed in $field."
              . " (Numbers not at beginning).";
            $flag = false;
        }
        
        if ($length != -1 && !(strlen($value) >= $length))
        {
            $this->error[] = "The $field field should be longer than $length.";
            $flag = false;
        }
        
        return $flag;
    }
        
    /**
     * Helper function to check if strings contain only chars and spaces.
     */
    public function checkCharSpaces($field, $value)
    {
        if (!preg_match("/^[a-zA-Z][a-zA-Z0-9 ]*$/",$value)) 
        {
            $this->error[] = "Only letters and spaces are allowed in $field.";
            return false;
        }
        return true;
    }
    
    /**
     * Helper function to check if strings contain only decimal digits.
     */
    public function checkDecimal($field, $value)
    {
        if (!preg_match("/^[0-9]*[.]+[0-9]*$/", $value)) 
        {
            $this->error[] = "You should write a decimal number in $field.";
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
    
    /**
     * Checks that the date is in an appropriate format (YYYY-MM-DD).
     * 
     * @param string $field
     * @param string $value
     * @return boolean
     */
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
    
    
    /**
     * Merges the passed error array with the current error array.
     * 
     * @param string[] $error
     */
    public function concatErrorArray($error)
    {
        $this->error = array_merge($this->error, $error);
    }
    
    
    /**
     * Returns an associative array of strings containing 
     * data if it has been passed.
     * 
     * @return string[]
     */
    public function setAddshopData() 
    {
        $data = array();
        $data["address"] = "";
        $data["shop_name"] = "";
        $data["city"] = "";
        //$data["typeOfShop"] = "";
        $data["VATNumber"] = "";
        $data["latitude"] = "";
        $data["longitude"] = "";
        $data["owner"] = "";
        
        foreach ($data as $key => $value) 
        {
            if (isset($this->request[$key]))
            {
                $data[$key] = $this->safeInput($this->request[$key]);
            }
        }
        
        return $data;
    }
    
    public function checkFieldsAddshop($data)
    {
        $isValid = true;
                
        $current = "address";
        if ($current == "")
        {
           $this->error[] = "Field $current cannot be empty.";
           $data[$current] = $this->setWarning($data[$current]);
           $isValid = false;
        }
        else if (!$this->checkCharSpaces($current, $data[$current]))
        {
           $data[$current] = $this->setWarning($data[$current]);
           $isValid = false;
        }
        
        $current = "city";
        if ($current == "")
        {
           $this->error[] = "Field $current cannot be empty.";
           $data[$current] = $this->setWarning($data[$current]);
           $isValid = false;
        }
        else if (!$this->checkCharSpaces($current, $data[$current]))
        {
           $data[$current] = $this->setWarning($data[$current]);
           $isValid = false;
        }
        
        $current = "shop_name";
        if ($current == "")
        {
           $this->error[] = "Field $current cannot be empty.";
           $data[$current] = $this->setWarning($data[$current]);
           $isValid = false;
        }
        
        $current = "VATNumber";
        if ($data[$current] != "" && !$this->checkCharSpaces($current, $data[$current]))
        {
           $this->error[] = "Field $current is not correct.";
           $data[$current] = $this->setWarning($data[$current]);
           $isValid = false;
        }
        
        $current = "latitude";
        if ($data[$current] != "" && !$this->checkDecimal($current, $data[$current]))
        {
           $this->error[] = "Field $current is not a decimal value.";
           $data[$current] = $this->setWarning($data[$current]);
           $isValid = false;
        }
        
        $current = "longitude";
        if ($data[$current] != "" && !$this->checkDecimal($current, $data[$current]))
        {
           $this->error[] = "Field $current is not a decimal value.";
           $data[$current] = $this->setWarning($data[$current]);
           $isValid = false;
        }
        
        return $isValid;
        
    }
    
}
