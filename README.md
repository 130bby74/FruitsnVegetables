# 🍎🥕 Fruits and Vegetables

## 🎯 Goal
This repository contains my answer to the following task:
We want to build a service which will take a `request.json` and:
* Process the file and create two separate collections for `Fruits` and `Vegetables`
* Each collection has methods like `add()`, `remove()`, `list()`;
* Units have to be stored as grams;
* Store the collections in a storage engine of your choice. (e.g. Database, In-memory)
* Provide an API endpoint to query the collections. As a bonus, this endpoint can accept filters to be applied to the returning collection.
* Provide another API endpoint to add new items to the collections (i.e., your storage engine).
* As a bonus you might:
  * consider giving an option to decide which units are returned (kilograms/grams);
  * how to implement `search()` method collections;
  * use latest version of Symfony's to embed your logic 

## 🚀 Requirements
You will need Sqlite installed on your machine (or in the docker) to run this project.
To install :
```bash
sudo apt-get install sqlite3
``` 

This project was built using Symfony 7.2 and PHP 8.3

## 🏃‍How to run
Run the following commands in order :
```bash 
composer install
php bin/console doctrine:schema:create
php bin/console app:load-food-fixtures
symfony server:start
```

And you are good to go !

## API
You can check the API at the following URL:

### GET : 
```bash
http://127.0.0.1:8000/api/foods/{type}
```
valid types are `fruit` and `vegetable` and `all` to get all the items.

you can additionnally filter by name in the following way:
```bash
http://127.0.0.1:8000/api/foods/{type}?name={name}
```  


### POST : 
```bash 
http://127.0.0.1:8000/api/foods/fruit/add
```
with a payload looking like : 
```json
{
    "name": "Mango",
    "quantity": 2,
    "unit": "kg"
}
```

## 🧪 Testing

To run the tests, you can use the following command:
```bash
php bin/phpunit
```

The HTML Coverage will be generated in the directory `tests/Coverage`

## 📝 Addiotional Notes

I changed some data in the `request.json` file to make it more realistic ( Lettuce -> vegetable and Tomatoes -> fruit)
I added the .env to the commited files to make it easier to run the project. In a real world scenario, I would have added it to the .gitignore file.
