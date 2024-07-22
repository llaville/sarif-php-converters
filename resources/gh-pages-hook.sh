#!/usr/bin/env bash

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

ASSETS_IMAGE_DIR="docs/assets/images"

php $SCRIPT_DIR/build.php graph-composer $ASSETS_IMAGE_DIR
php $SCRIPT_DIR/build.php converter-ecs $ASSETS_IMAGE_DIR
php $SCRIPT_DIR/build.php converter-phan $ASSETS_IMAGE_DIR
php $SCRIPT_DIR/build.php converter-phpcs $ASSETS_IMAGE_DIR
php $SCRIPT_DIR/build.php converter-phpcs-fixer $ASSETS_IMAGE_DIR
php $SCRIPT_DIR/build.php converter-phplint $ASSETS_IMAGE_DIR
php $SCRIPT_DIR/build.php converter-phpmd $ASSETS_IMAGE_DIR
php $SCRIPT_DIR/build.php converter-phpstan $ASSETS_IMAGE_DIR
php $SCRIPT_DIR/build.php converter-psalm $ASSETS_IMAGE_DIR
php $SCRIPT_DIR/build.php converter-twigcs-fixer $ASSETS_IMAGE_DIR
