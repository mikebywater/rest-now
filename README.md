## Getting Started

Install via composer

```  composer require mikebywater/rest-now ```

### Configuration

Configuration is done via the config class

``` php
$config = new \Now\Client\Config();

$config->base_uri = "https://instance-name.service-now.com";
$config->client_id = '0xx000xxx00';
$config->client_secret  = 'Happiz9y';
$config->username = "mike.j.bywater@gmail.com";
$config->password = 'squirrel123';

```

### Authentication


