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

$directly = new Directly('app');
$directly->run('/');
```


## License

The Directly Framework is licensed under the MIT license. See [License File](LICENSE) for more information.