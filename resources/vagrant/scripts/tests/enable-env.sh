#!/usr/bin/env bash

if [ "$1" == 'help' ]; then
    echo "Configures the Vagrant instance to run in test mode."
    exit 0;
fi

mysql -u phraseanet -pphraseanet --execute="DROP DATABASE IF EXISTS ab_test"
mysql -u phraseanet -pphraseanet --execute="DROP DATABASE IF EXISTS db_test"
mysql -u phraseanet -pphraseanet --execute="CREATE DATABASE ab_test"
mysql -u phraseanet -pphraseanet --execute="CREATE DATABASE db_test"

./tests/bootstrap.sh install
