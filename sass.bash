#!usr/bin/env bash

cd $( dirname ${BASH_SOURCE[0]} ) && pwd
sass --sourcemap=none --load-path ../aerendur/bower_components/foundation-sites/scss --load-path ../aerendur/bower_components/motion-ui --load-path ../aerendur/scss --watch scss:css --style minify

# sass --sourcemap=none scss/blank.scss css/blank.css
