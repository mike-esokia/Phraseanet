#!/usr/bin/env bash

function run_script {
    vagrant ssh -c "cd /vagrant/ && $1"
}

if [ "$1" == "help" ]; then
    exit 0
fi

if [ "$1" == "tests" ]; then
    if [ "$2" == "enable" ]; then
        run_script /vagrant/resources/vagrant/scripts/tests/enable-env.sh
    else
        run_script /vagrant/resources/vagrant/scripts/tests/restore-env.sh
    fi
fi
