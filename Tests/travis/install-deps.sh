#!/usr/bin/env sh

BASEDIR=$(dirname $0);

if [ "$TRAVIS_PHP_VERSION" = "hhvm" ]; then
    exit 0;
fi

VERSION_NAME=$(phpenv version-name)

if [ $VERSION_NAME = "5.3" ] || [ $VERSION_NAME = "5.4" ] || [ $VERSION_NAME = "5.5" ] || [ $VERSION_NAME = "5.6" ]; then
    echo "extension = mongo.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
    echo "extension = memcache.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
fi

pecl install riak
phpenv config-add $BASEDIR/php.ini
