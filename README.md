# rate-limiter
## Installation
```bash
composer require ermeo/rate-limiter
```

## Configuration

### Config file
```yaml
ermeo_api_rate_limit:
    enabled: true
    # The service that is used to persist rate limit metadata.
    cache: 
      # Value accepted are redis, php_redis.
      storage_engine:
      provider:
    # Rules can bypass providers.
    rules:
      # just a key to distinguish the rule
      OAuth:
        service: 'OAuth'
    providers:
        user:         
            service: ''
            headers:
                display: true
                names:
                    limit: X-RateLimit-Limit
                    remaining: X-Rate-Remaining
                    reset: X-RateLimit-Reset
            exception:
              code: 429
              message: ''
        client:
            service: 'ermeo.rate-limit.client'
            headers:
                display: true
                names:
                    limit: X-RateLimit-Limit
                    remaining: X-Rate-Remaining
                    reset: X-RateLimit-Reset
            exception:
                code: 429
                message: ''
```
## Implementation
### RateLimit with doctrine configuration provider
#### Allow rate limiting on doctrine entity
To activate rate limiting on doctrine entity you need to create a migration script to create a new column rate_limit_id
```php
use Ermeo\RateLimitBundle\Interfaces\IsRateLimitedInterface;
use Ermeo\RateLimitBundle\Traits\IsRateLimited;

class User implements IsRateLimitedInterface
{
    use IsRateLimited;
}
```
#### Create a provider
Create a new class UserProvider that extends AbstractRateLimitProvider

```php
use Ermeo\RateLimitBundle\Providers\AbstractRateLimitProvider;
use Ermeo\RateLimitBundle\Interfaces\ConfigurationInterface;

class UserProvider extends AbstractRateLimitProvider
{
    public function getIdentifier() : ?string
    {
        // TODO: Implement getIdentifier() method.
    }
    
    public function getRateLimit() :ConfigurationInterface
    {
        // TODO: Implement getRateLimit() method.
    }

}
```

If you don't want to use doctrine configuration to store the rate limit configuration, you can use our class ArrayConfiguration.
```php

use Ermeo\RateLimitBundle\Configuration\ArrayConfiguration;
use Ermeo\RateLimitBundle\Interfaces\ConfigurationInterface;

public function getRateLimit() :ConfigurationInterface
{
    return new ArrayConfiguration([
        ConfigurationInterface::LIMIT => 10, 
        // period is always declared as second
        ConfigurationInterface::PERIOD => 60
    ]);
}
```
### Rules
You can create Rules that provide the possibility to bypass providers. To implements rules you need to create your class that implements CheckerInterface.
```php
use Ermeo\RateLimitBundle\Interfaces\CheckerInterface;

class Rule implements CheckerInterface
{
    public function byPass() : bool
    {
     // TODO: Implement byPass() method.
    }
}
```
If the method byPass return true all providers will be ignored.

### Headers
You can customize headers foreach providers. Remember if you don't customize it the last provider who the option display is at true will override the previous header.

If you don't declare headers on configuration file, default are :
```yaml
names:
    limit: X-RateLimit-Limit
    remaining: X-Rate-Remaining
    reset: X-RateLimit-Reset
```

### Exception
You can customize exceed rate limit code and message.

Code must be complatible with symfony Response status ```array_keys(Response::$statusTexts)```.

By default code is ```429``` and message is ```API rate limit exceeded.```.

## Running test
### Unit test
```bash
./vendor/bin/phpunit ./Tests
```

### Php cs fixer
```bash
./vendor/bin/php-cs-fixer fix --diff --diff-format=udiff --dry-run --verbose
```
