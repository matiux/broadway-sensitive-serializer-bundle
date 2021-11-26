Broadway sensitization support Bundle
=====

```shell
git clone https://github.com/matiux/broadway-sensitive-serializer-bundle.git && cd broadway-sensitive-serializer-bundle
cp docker/docker-compose.override.dist.yml docker/docker-compose.override.yml
rm -rf .git/hooks && ln -s ../scripts/git-hooks .git/hooks
```