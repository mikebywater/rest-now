## Getting Started

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/b09eff4fc3e24202afd4b461b3b56497)](https://app.codacy.com/app/mikebywater/rest-now?utm_source=github.com&utm_medium=referral&utm_content=mikebywater/rest-now&utm_campaign=Badge_Grade_Dashboard)

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


