# GenericLogin

A generic PHP login form and easy to setup.  You can add this code to your website(s), so you don't have to re-build a login process for each of your projects, over and over again.  My code works by itself: it doesn't depend on Composer or any other complex framework.  You take the code and it works right away.

## Requirements
- Use of MySQL Database
- PHP 7.1.19 (not tested with other versions)

## How to install

Easy, download all files to your server.  
Users will hit the page `index.php`, and on successful authentication, they will be redirected to the page you have setup in the config file. 
A PHP Session will be started.  Every data of the user, found in the MySQL Row, will be stored in a $_SESSION variable.

## Setup
First thing to do is edit the file `.login.config`.  Take the time to read each comments to understand what each item will do.

1. Change `Application/Name` to set the name of your application.
1. Change `Application/RedirectPage` to set where whe user must be redirected after a successful authentication.
1. Change `Database` accordingly to the comments.  You must set the authentication data to reach your MySQL databse.  Also, you have to set the name of the Table that contains your users, and the name of the column that contains Usercodes and Passwords.

### Enable Registration
(function not yet created)

### Enable Password Reset
(function not yet created)

## Built With
* [MysqliDb 2.9.3](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class) - Simple MySQLi wrapper and object mapper with prepared statements.
* [Semantic UI 2.4.2](https://semantic-ui.com/) - User Interface is the language of the web.
* [jQuery 3.5.1](https://jquery.com/) - Write less, do more.
