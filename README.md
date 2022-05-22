# Link shortener app

A simple php full rest-api base application designed in MVC platform.\
Users can register and create their **short links** and redirect their users into certain urls.\

# Get starterd 

## 1. Clone project into your computer

First open your `cmd` and move to your projects folder.\
`cd path/to/your/projects/folder`\

Then, simply can clone our GitHub repository with this command:\
`git clone https://github.com/sadrix/php-link-shortener.git`

## 2. Install composer
After cloning the project, move to created folder:\
`cd php-link-shortener`\

Now install composer packages:\
`composer install`

## 3. Config and import databse structure
Go to your localhost phpmyadmin page and create new databse name `link_shortener` or any other name you prefer as your project database name.\
Next, import `.sql` file located in root path of project into your new database in phpmyadmin.\
Finally, config your databse connection configs from json file located in this path:\
`configs/database.json`\

## 4. Config app global configs
Application url and name should be set as your localhost or virtual host domain name in `app.json` file.

## 5. Start test and using Application
Finally! you can start using application on your local app url.\
Also, you can use our **postman collection** and import it as new collection to your postman app and test or user application api very easily.

**Happy coding!**






