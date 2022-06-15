Broadway sensitization support Bundle
=====

![check dependencies](https://github.com/matiux/broadway-sensitive-serializer-bundle/actions/workflows/check-dependencies.yml/badge.svg)
![test](https://github.com/matiux/broadway-sensitive-serializer-bundle/actions/workflows/tests.yml/badge.svg)
[![codecov](https://codecov.io/gh/matiux/broadway-sensitive-serializer-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/matiux/broadway-sensitive-serializer-bundle)
[![type coverage](https://shepherd.dev/github/matiux/broadway-sensitive-serializer-bundle/coverage.svg)](https://shepherd.dev/github/matiux/broadway-sensitive-serializer-bundle)
[![psalm level](https://shepherd.dev/github/matiux/broadway-sensitive-serializer-bundle/level.svg)](https://shepherd.dev/github/matiux/broadway-sensitive-serializer-bundle)
![security analysis status](https://github.com/matiux/broadway-sensitive-serializer-bundle/actions/workflows/security-analysis.yml/badge.svg)
![coding standards status](https://github.com/matiux/broadway-sensitive-serializer-bundle/actions/workflows/coding-standards.yml/badge.svg)

This bundle is the wrapper for the Broadway [Sensitive Serializer library](https://github.com/matiux/broadway-sensitive-serializer).
You can find more info on the base library [here](https://github.com/matiux/broadway-sensitive-serializer/wiki).

### Setup for development

```shell
git clone https://github.com/matiux/broadway-sensitive-serializer-bundle.git && cd broadway-sensitive-serializer-bundle
cp docker/docker-compose.override.dist.yml docker/docker-compose.override.yml
rm -rf .git/hooks && ln -s ../scripts/git-hooks .git/hooks
```

### Interact with the PHP container
This is a bash script that wrap major docker-compose function. You can find it [here](./docker/dc.sh) and there is a symbolic link in project root.

Some uses:
```shell
./dc up -d
./dc enter
./dc phpunit
./dc psalm
./dc coding-standard-fix-staged
./dc build php --no-cache
```
Check out [here](./docker/dc.sh) for all the options.

### Install dependencies to run test or execute examples
```shell
./dc up -d
./dc enter
composer install
```

### Run test
```shell
./dc up -d
./dc enter
project phpunit
```

### Whole Strategy configuration
[Read the docs](https://github.com/matiux/broadway-sensitive-serializer/wiki/%5BIT%5D-3.Moduli#whole-strategy)

```yaml
broadway_sensitive_serializer:
  aggregate_master_key: 'm4$t3rS3kr3tk31' # Master key to encrypt the keys of aggregates. Get it from an external service or environment variable
  key_generator: open-ssl # For now is the only one generator implemented
  # To use the DBAL  implementation, install matiux/broadway-sensitive-serializer-dbal package with composer
  aggregate_keys: broadway_sensitive_serializer.aggregate_keys.dbal
  #aggregate_keys: broadway_sensitive_serializer.aggregate_keys.in_memory # Default implementation, of little use outside of testing
  data_manager:
    name: AES256 # For now, it is the only encryption strategy implemented
    key: null # Encryption key to sensitize data. If null you will need to pass the key at runtime. This is the convenient way, check out the examples and wiki on main library
    iv: null # Initialization vector. If null it will be generated internally and iv_encoding must be set to true. This is the convenient way, check out the examples and wiki on main library
    iv_encoding: true # Encrypt the iv and is appends to encrypted value. It makes sense to set it to true if the iv option is set to null. This is the convenient way, check out the examples and wiki on main library
    #--- Alternatively -----
    #data_manager:
    #  name: AES256
    #  parameters:
    #    AES256:
    #      key: null
    #      iv: null
    #      iv_encoding: true
  strategy:
    name: whole
    aggregate_key_auto_creation: true # Enable AggregateKey model auto creation. This is the convenient way, check out the examples and wiki on main library
    value_serializer: json # Strategy to serialize payload's values. Default json
    excluded_id_key: id # The key of the aggregate id which should not be encrypted
    excluded_keys: # List of keys to be excluded from encryption
      - occurred_at
    events: # List of events supported by the strategy
      - SensitiveUser\User\Domain\Event\AddressAdded
      - SensitiveUser\User\Domain\Event\UserRegistered
  #--- Alternatively -----
  #strategy:
  #  name: whole
  #  parameters:
  #    whole:
  #      aggregate_key_auto_creation: true
  #      value_serializer: json
  #      excluded_id_key: id
  #      excluded_keys:
  #        - occurred_at
  #      events:
  #        - SensitiveUser\User\Domain\Event\AddressAdded
  #        - SensitiveUser\User\Domain\Event\UserRegistered
```

### Partial Strategy configuration
[Read the docs](https://github.com/matiux/broadway-sensitive-serializer/wiki/%5BIT%5D-3.Moduli#partial-strategy)
```yaml
broadway_sensitive_serializer:
  aggregate_master_key: 'm4$t3rS3kr3tk31' # Master key to encrypt the keys of aggregates. Get it from an external service or environment variable
  key_generator: open-ssl # For now is the only one generator implemented
  # To use the DBAL  implementation, install matiux/broadway-sensitive-serializer-dbal package with composer
  aggregate_keys: broadway_sensitive_serializer.aggregate_keys.dbal
  #aggregate_keys: broadway_sensitive_serializer.aggregate_keys.in_memory # Default implementation, of little use outside of testing
  data_manager:
    name: AES256 # For now, it is the only encryption strategy implemented
    key: null # Encryption key to sensitize data. If null you will need to pass the key at runtime. This is the convenient way, check out the examples and wiki on main library
    iv: null # Initialization vector. If null it will be generated internally and iv_encoding must be set to true. This is the convenient way, check out the examples and wiki on main library
    iv_encoding: true # Encrypt the iv and is appends to encrypted value. It makes sense to set it to true if the iv option is set to null. This is the convenient way, check out the examples and wiki on main library
    #--- Alternatively -----
    #data_manager:
    #  name: AES256
    #  parameters:
    #    AES256:
    #      key: null
    #      iv: null
    #      iv_encoding: true
  strategy:
    name: partial
    aggregate_key_auto_creation: true # Enable AggregateKey model auto creation. This is the convenient way, check out the examples and wiki on main library
    value_serializer: json # Strategy to serialize payload's values. Default json
    events: # List of events supported by the strategy
      - SensitiveUser\User\Domain\Event\AddressAdded:
        - address # List of keys to sensitize
      - SensitiveUser\User\Domain\Event\UserRegistered:
        - name
        - surname
  #--- Alternatively -----
  #strategy:
  #  name: partial
  #  parameters:
  #    partial:
  #      aggregate_key_auto_creation: true
  #      value_serializer: json
  #      events:
  #        - SensitiveUser\User\Domain\Event\AddressAdded:
  #           - address
  #        - SensitiveUser\User\Domain\Event\UserRegistered:
  #           - name
  #           - surname
```

### Custom Strategy configuration
[Read the docs](https://github.com/matiux/broadway-sensitive-serializer/wiki/%5BIT%5D-3.Moduli#custom-strategy)
```yaml
broadway_sensitive_serializer:
  aggregate_master_key: 'm4$t3rS3kr3tk31' # Master key to encrypt the keys of aggregates. Get it from an external service or environment variable
  key_generator: open-ssl # For now is the only one generator implemented
  # To use the DBAL  implementation, install matiux/broadway-sensitive-serializer-dbal package with composer
  aggregate_keys: broadway_sensitive_serializer.aggregate_keys.dbal
  #aggregate_keys: broadway_sensitive_serializer.aggregate_keys.in_memory # Default implementation, of little use outside of testing
  data_manager:
    name: AES256 # For now, it is the only encryption strategy implemented
    key: null # Encryption key to sensitize data. If null you will need to pass the key at runtime. This is the convenient way, check out the examples and wiki on main library
    iv: null # Initialization vector. If null it will be generated internally and iv_encoding must be set to true. This is the convenient way, check out the examples and wiki on main library
    iv_encoding: true # Encrypt the iv and is appends to encrypted value. It makes sense to set it to true if the iv option is set to null. This is the convenient way, check out the examples and wiki on main library
    #--- Alternatively -----
    #data_manager:
    #  name: AES256
    #  parameters:
    #    AES256:
    #      key: null
    #      iv: null
    #      iv_encoding: true
  strategy:
    name: custom
    aggregate_key_auto_creation: true # Enable AggregateKey model auto creation. This is the convenient way, check out the examples and wiki on main library
    value_serializer: json # Strategy to serialize payload's values. Default json
  #--- Alternatively -----
  #strategy:
  #  name: custom
  #  parameters:
  #    custom:
  #      aggregate_key_auto_creation: true
  #      value_serializer: json
```