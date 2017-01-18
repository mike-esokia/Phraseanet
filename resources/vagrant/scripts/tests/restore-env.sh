#!/usr/bin/env bash

if [ "$1" == 'help' ]; then
    echo "Configures the vagrant instance to run in regular mode."
    exit 0;
fi

bin/setup system:config -vvv set main.database.dbname ab_master
bin/setup system:config -vvv set main.database.user phraseanet
bin/setup system:config -vvv set main.database.password phraseanet
bin/console -vvv compile:configuration
