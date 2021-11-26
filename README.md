Broadway sensitization support Bundle
=====

```shell
git clone https://github.com/matiux/broadway-sensitive-serializer-bundle.git && cd broadway-sensitive-serializer-bundle
cp docker/docker-compose.override.dist.yml docker/docker-compose.override.yml
rm -rf .git/hooks && ln -s ../scripts/git-hooks .git/hooks
```

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
    #parameters:
    #  AES256:
    #    key: null # Encryption key to sensitize data. If null you will need to pass the key at runtime
    #    iv: null # Initialization vector. If null it will be generated internally and iv_encoding must be set to true
    #    iv_encoding: true # Encrypt the iv and is appends to encrypted value. It makes sense to set it to true if the iv option is set to null
    strategy:
      name: whole
      events:
        - "EventOne"
        - "EventTwo"
      #strategy:
      #  name: whole
      #  parameters:
      #    whole:
      #      events:
      #        - "EventOne"
      #        - "EventTwo"
      #    partial: ~
```

```yaml
services:
  broadway_sensitive_serializer.aggregate_keys.dbal:
    class: Matiux\Broadway\SensitiveSerializer\Dbal\DBALAggregateKeys
    arguments:
      $connection: "@doctrine.dbal.default_connection"
      $tableName: "aggregate_keys"
      $useBinary: false
      $binaryUuidConverter: "@broadway.uuid.converter"
```