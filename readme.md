# Directly

Micro-framework PHP to provider web pages quickly

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install Directly.

```bash
$ composer require wallrio/directly "*"
```



## Usage

Create an index.php file with the following contents:

```php
<?php

require 'vendor/autoload.php';

use directly\Directly as Directly;

$directly = new Directly('application');
$directly->run('/');
```

    
Create an .htaccess file with the following contents:

```
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f                     
RewriteRule ^(.+)$ index.php [L]                
</IfModule>
```


## Class Directly()

- new Directly(DIRECTORY-YOUR-APP);

        DIRECTORY-YOUR-APP  =  specifies the application directory (optional)

- $directly->run(ROUTE);

        ROUTE   =   specifies an initial route (optional)


- Optionally you can force a home directory for your files, use the attribute below for this:

    $directly->publicDir = 'DIRECTORY-PUBLIC';

        DIRECTORY-PUBLIC    =   specify a directory after the directory of your application
    
    ### example
    ```
    $directly->publicDir = 'assets';
    ```

            
## Struct directory

```
    /your-directory-project
        |
        |--application/
        |   |
        |   |--error
        |   |    |--404
        |   |        |--view.php
        |   |
        |   |--global
        |   |    |--header.php
        |   |    |--footer.php
        |   |    
        |   |--inc
        |   |    |--menu.php
        |   |
        |   |--view
        |       |--home
        |          |--view.php
        |       |--about
        |          |--view.php
        |       |--contact
        |          |--view.php
        |
        |--- .htaccess
        |--- index.php

```




## Other options

    $directly->publicDir = 'assets';


## Short tags

    [inc:menu.php] =   includes no document the contents of the menu.php file located in the directory /application/inc

    [inc-route:meta.php] = includes no document the contents of the menu.php file located in the directory /application/view/[CURRENT-PAGE]/meta.php

    [global:FILE_NAME] = includes no document the contents of the menu.php file located in the directory /application/global/FILE_NAME.php

    [domain:url] = replace to url of domain    

## License

The Directly Framework is licensed under the MIT license. See [License File](LICENSE) for more information.