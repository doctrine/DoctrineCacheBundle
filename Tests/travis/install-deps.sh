#!/usr/bin/env sh

BASEDIR=$(dirname $0);

if [ "$TRAVIS_PHP_VERSION" = "hhvm" ]; then
    exit 0;
fi

pecl install riak
phpenv config-add $BASEDIR/php.ini
