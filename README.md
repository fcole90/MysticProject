Fisherman Locator
=================
*Find your lozenges around the globe.*

This website helps you fo find the famous Fisherman's Friends (R) Lozenges.

### Aim of the project
The final Aim of the project is to develop a website that allows its users to get informed about which shops sell the Fisherman's Friends (R) Lozenges,
which are the best equipped and allow them to rate the shops and add new 
ones. While this is the final aim, in this scope the requisites where far less ambitious, so the website presents some bare minimum functionalities and should be considered as a proof of concept.

### Structure of the website
The website implements a classic MVC pattern, as asked in the requirements. The structure can be seen in the doxygen documentation attached in the project itself.

### Current level of functionality
In the current state (version 0.9) the website has the following capabilities:

- Signup system: a user can sign up;
- Login system: a registered user can log in;
- Shops reporting: a logged user can add a new shop to the database;
- Shops research: a user can search shops (match by shop name or city);
- Profile view: a logged user can see its profile informatons;
- Shops removal: an admin can remove shops.

### Project requirements

|---------------------|:------------------------------------------------------|
| HTML | *YES* |
| CSS | *YES* |
| PHP | *YES* |
| MySQL | *YES* |
| two roles at least | *Yes: admin, registered user and not registered user* |
| transactions | *Yes, ShopModel::removeShop()* |
| Ajax | *Yes: BasePageController::loadPageAjaxSearchShop(), Presenter::json()* |
| Log in credentials | *Admin -> username: admin, password: admin* |

