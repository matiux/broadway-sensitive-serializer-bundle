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

### Partial Strategy configuration
[Read the docs](https://github.com/matiux/broadway-sensitive-serializer/wiki/%5BIT%5D-3.Moduli#partial-strategy)
```yaml
broadway_sensitive_serializer:
  aggregate_master_key: 'm4$t3rS3kr3tk31' # Master key to encrypt the keys of aggregates. Get it from an external service or environment variable
  key_generator: open-ssl
  #aggregate_keys: broadway_sensitive_serializer.aggregate_keys.in_memory
  aggregate_keys: broadway_sensitive_serializer.aggregate_keys.dbal
  data_manager:
    name: AES256
    key: null # Encryption key to sensitize data. If null you will need to pass the key at runtime
    iv: null # Initialization vector. If null it will be generated internally and iv_encoding must be set to true
    iv_encoding: true # Encrypt the iv and is appends to encrypted value. It makes sense to set it to true if the iv option is set to null
    #--- Alternatively -----
    #data_manager:
    #  name: AES256
    #  parameters:
    #    AES256:
    #      key: null # Encryption key to sensitize data. If null you will need to pass the key at runtime
    #      iv: null # Initialization vector. If null it will be generated internally and iv_encoding must be set to true
    #      iv_encoding: true # Encrypt the iv and is appends to encrypted value. It makes sense to set it to true if the iv option is set to null
  strategy:
    name: partial
    aggregate_key_auto_creation: true
  #--- Alternatively -----
  #strategy:
  #  name: partial
  #  parameters:
  #    partial:
  #      aggregate_key_auto_creation: true
```
### Whole Strategy configuration
[Read the docs](https://github.com/matiux/broadway-sensitive-serializer/wiki/%5BIT%5D-3.Moduli#whole-strategy)
```yaml
broadway_sensitive_serializer:
  aggregate_master_key: 'm4$t3rS3kr3tk31' # Master key to encrypt the keys of aggregates. Get it from an external service or environment variable
  key_generator: open-ssl
  #aggregate_keys: broadway_sensitive_serializer.aggregate_keys.in_memory
  aggregate_keys: broadway_sensitive_serializer.aggregate_keys.dbal
  data_manager:
    name: AES256
    key: null # Encryption key to sensitize data. If null you will need to pass the key at runtime
    iv: null # Initialization vector. If null it will be generated internally and iv_encoding must be set to true
    iv_encoding: true # Encrypt the iv and is appends to encrypted value. It makes sense to set it to true if the iv option is set to null
    #--- Alternatively -----
    #data_manager:
    #  name: AES256
    #  parameters:
    #    AES256:
    #      key: null # Encryption key to sensitize data. If null you will need to pass the key at runtime
    #      iv: null # Initialization vector. If null it will be generated internally and iv_encoding must be set to true
    #      iv_encoding: true # Encrypt the iv and is appends to encrypted value. It makes sense to set it to true if the iv option is set to null
  strategy:
    name: whole
    aggregate_key_auto_creation: true
    excluded_id_key: id
    excluded_keys:
      - occurred_at
    events:
      - SensitiveUser\User\Domain\Event\AddressAdded
      - SensitiveUser\User\Domain\Event\UserRegistered
  #--- Alternatively -----
  #strategy:
  #  name: whole
  #  parameters:
  #    whole:
  #      aggregate_key_auto_creation: true
  #      excluded_id_key: id
  #      excluded_keys:
  #        - occurred_at
  #      events:
  #        - SensitiveUser\User\Domain\Event\AddressAdded
  #        - SensitiveUser\User\Domain\Event\UserRegistered
```