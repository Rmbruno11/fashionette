# fashionette

#### Coding Challenge

JSON API, implemented using:
• PHP 7.x
• Lumen
• Git

###### INSTRUCTIONS

- * **Requirements**:

- - - Install php 7.2+
    - Clone the repository
    - install dependencies:

  - - ```
      php composer.phar install
      ```

  - **Running the project:**

    ```
    php       -S 127.0.0.1:8000 -t public
    ```

  - **Third party service:**

    To get to the data, we use the third party service TVMaze, which provides a convenient
    API to search for movie titles. Using a cache for Optimization of the number of HTTP requests.

    Their API description can be found here:
    http://www.tvmaze.com/api

    

  - **Running the unit tests:**

    ```
    ./vendor/bin/phpunit
    ```



#### 			EXAMPLES

   - ​	**With results:**

     ```
     http://localhost:8000/?q=Deadwood
     ```

     ![image-20200630183206862](.\image-20200630183206862.png)

   - **Without results:**

     ```
     http://localhost:8000/?q=deadwood
     ```

     ![image-20200630183447061](.\image-20200630183447061.png)

   * **Wrong URL - ERROR:**

     ```
     http://localhost:8000/?t=deadwood
     ```

     ![image-20200630183610782](.\image-20200630183610782.png)



#### How can the API evolve in the future ?

* Api versioning: it is good practice to version the apis: 

  Ex: **http://json-api.local/v1/search?q=<query>** instead of 

  **http://json-api.local/?q=<query >**

* Model the entities (in this case TVShow), and use some serializer (such as Fractal) to assemble the responses. Using a TVShow class and instances of able to transform the information to json for the user.

* Add authentication using lumen middleware.

* Add linters and static code analysis tools (for example: PHP C Fixer or PHP Mess Detector).

* Use a documentation tool (Ex: swagger / openapi).

* improving the cache system towards a shared one.

* Add functional / integration tests.