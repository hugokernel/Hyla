#!/bin/bash

echo "Search $1"
grep "$1" . -n -r --exclude=*.svn* --binary-files=without-match
