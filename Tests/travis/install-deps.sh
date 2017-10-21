#!/usr/bin/env sh

BASEDIR=$(dirname $0);

pecl install riak
phpenv config-add $BASEDIR/php.ini
