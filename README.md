## Getting Started

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/b09eff4fc3e24202afd4b461b3b56497)](https://app.codacy.com/app/mikebywater/rest-now?utm_source=github.com&utm_medium=referral&utm_content=mikebywater/rest-now&utm_campaign=Badge_Grade_Dashboard)

Install via composer

```  composer require entanet/rest-now ```

### Configuration

Configuration is done via the config class

``` php
$config = new \Now\Client\Config();

$config->base_uri = "https://instance-name.service-now.com";
$config->client_id = '0xx000xxx00';
$config->client_secret  = 'client_secret';
$config->username = "my.email@gmail.com";
$config->password = 'secret_password';

```

a second config class can be used to set decide if incremental retry is on, if so 
the http max retries and max delay in second values, however to ensure 
rest-now does not break, some default values have been included in the auth class in some CONST variables
    
``` php
http_client.incremental_retry_is_active
http_client.max_delay_between_retries_in_seconds
http_client.max_retries
...



### Authentication


