#!/usr/bin/env bash

# To update & rebuild Dilps, launch this script from the project root directory

# Make a dump of the database
echo "********************* Dumping database..."
php $PWD/bin/dump-data.php $(date +%Y%m%d%H%M%S).db.backup.sql.gz

# Update project from master branch
echo "********************* Updating project files..."
git pull origin master

# Rebuild project
sh $PWD/bin/build.sh
