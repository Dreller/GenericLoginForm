# GenericLogin

A generic PHP login form and easy to setup.  You can add this code to your website(s), so you don't have to re-build a login process for each of your projects, over and over again.  My code works by itself: it doesn't depend on Composer or any other complex framework.  You take the code and it works right away.

This tool will fits your needs, with many parameters you can control, and also, it will adapt to your existent database.  
Want more?  Just submit an issue, I will be happy to add more features!

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
Edit the `.login.config` file as follow:

1. Change the `Registration/Enabled` to `Y` (for Yeah!).
1. Customize the `Registration/Invite` and `Registration/Color` to something you like.

The basic registration form will ask for the user code and a password.  Those will be saved to the MySQL fields you have set in the `Database` section.

#### You want to ask more prompts ?

1. List all MySQL Column Names you want to ask, in the Registration form, in `Registration/Fields`.  Separated with a coma.  
1. For each fields in `Registration/Fields`, you want to set a proper user-friendly label, in `Registration/Labels`, separated with a coma too.
1. If a field should have a specific data type, set it in `Registration/Types`.  Still separated with a coma.   

**IMPORTANT: `Fields`, `Labels` and `Types` must have the same number of data**. They should by sync'd together, as you can see in this example:

```
[Registration]
Fields="userFirstName,userLastName,userEmail"
Labels="First Name,Last Name,Email Address"
Types="text,text,email"
``` 

You may want to control duplicate user accounts ?  Of course you want !  Simply list field names that should be unique, in `Registration/Uniques`.  You can set many fields, separated with a coma.
During the registration process, the engine will check each of those fields, and validate if the data already exists.
In this setting, you would set the User Email and/or User Code column name, to avoid multiple users have the same User Code and/or User Email.

When the registratin is completed, the user will be redirected, the same way a regular login would do.

### Enable Password Reset
Edit the `.login.config` file as follow:

1. Change the `PasswordReset/Enabled` to `Y` (for Yup!).
1. Customize the `PasswordReset/Invite` and `PasswordReset/Color` to something you like.
1. Set `PasswordReset/EmailField` to the MySQL Column of your Users Table, that contains the email address.  A temporary password will be send there.
1. Set `PasswordReset/ExpiredField` to the MySQL Column of your Users Table, set as TinyINT, default 0.  If set to 0, the account is OK.  If set to 1, the user will be forced to change its password at its next logon.

That's it!

### Validate the setup
To help validate the setup file, I made a small script that check every settings and report you any issues it may find.  This script will evolve with time.  You can get to it by browsing to `/cheker.php` and hit the Start check button.  Take the time to read the details to ensure a good setup and a working Login form for your users.

## Built With
* [MysqliDb 2.9.3](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class) - Simple MySQLi wrapper and object mapper with prepared statements.
* [Semantic UI 2.4.2](https://semantic-ui.com/) - User Interface is the language of the web.
* [jQuery 3.5.1](https://jquery.com/) - Write less, do more.
