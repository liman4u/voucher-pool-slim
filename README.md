 **Technology**

- PHP 7.1.3

- Slim Framework 3.0

- Data was persisted with MySql 5.6 
    - Connection details:
        - port: `3306`
        - MYSQL_DATABASE: `voucher_pool_db`
        - MYSQL_USER: `voucher_pool`
        - MYSQL_PASSWORD: `voucher_pool`

- Testing was done with PhpUnit 

 **Packages**

- This can be found in the composer.json in the root directory of the project

-PhpUnit 7.0 was used for testing , am more familiar with this than others like Codeception and Behat


 **How to run**
- Clone for Github
```bash
git clone git@github.com:liman4u/voucher-pool-slim.git

cd voucher-pool-slim
```


- To start the application server and run migrations, run the following from root of application:
```bash
sh ./start.sh
```
- Tests can also be run separately by running[from the project's root folder] 
```bash
composer test tests/VoucherTests.php
```

- In case the start.sh does not seem to be runnable, use chmod 400 start.sh

 **Features**

The API  conformed to REST practices and  provide the following functionality:

- Generate voucher codes for recipients given Special Offer and Expiration Date
- Validate voucher codes and return percentage discounts
- Return all voucher codes with Special Offer of a recipient

 **Endpoints**

- The postman documentation link is https://documenter.getpostman.com/view/3189851/RWTrMwNg

- This application conform to the specified endpoint structure given and return the HTTP status codes appropriate to each operation.  


 **Environment Variables**

- These are found in .env of the root directory of the project

- For production deployments , DEBUG should be switched to 'false' and APP_ENV changed to 'production'


 **Data Migrations**

- This is found in db/migrations/*.sql in the root directory of the project

- This contains initial database schema migration  

 **Routes**

- This can be found in routes/web.php in the root directory of the project