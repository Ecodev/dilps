#!/usr/bin/env bash

# This script build all assets for production environment

# Exit script on any error
set -e

# Disable progress
if [ "$1" = '--no-progress' ]; then
    NO_PROGRESS='--no-progress'
    PROGRESS_GIT='--quiet'
    export PROGRESS_NG='--progress false'
else
    NO_PROGRESS=
    PROGRESS_GIT=
    export PROGRESS_NG=
fi

echo "Installing git hooks..."
ln -fs ../../bin/pre-commit.sh .git/hooks/pre-commit

echo "Updating Node.js packages..."
yarn install $NO_PROGRESS

echo "Updating all PHP dependencies via composer..."
composer install --classmap-authoritative $NO_PROGRESS

echo "Updating database..."
./vendor/bin/doctrine-migrations migrations:migrate --no-interaction
./vendor/bin/doctrine orm:generate-proxies

echo "Clear cache"
rm -rf ./data/tmp/cache/*

echo "Building Angular application..."
yarn run prod
