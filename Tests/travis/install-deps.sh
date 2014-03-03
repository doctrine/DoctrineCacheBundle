#!/usr/bin/env sh

IS_HHVM=`php -r "var_export(defined('HHVM_VERSION'));"`;
BASEDIR=$(dirname $0);

if [ $IS_HHVM ] ; then
    exit 0;
fi

pecl install riak
phpenv config-add $BASEDIR/php.ini
