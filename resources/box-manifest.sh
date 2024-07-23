#!/usr/bin/env bash

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

curl -Ls https://github.com/llaville/box-manifest/releases/latest/download/box-manifest.phar -o $SCRIPT_DIR/box-manifest
chmod +x $SCRIPT_DIR/box-manifest

$SCRIPT_DIR/box-manifest manifest:build --ansi -vv -c box.json --output-file=sbom.json
$SCRIPT_DIR/box-manifest manifest:build --ansi -vv -c box.json --output-file=console.txt --format console
$SCRIPT_DIR/box-manifest manifest:stub  --ansi -vv -c box.json --output-file=stub.php --resource console.txt --resource sbom.json
#$SCRIPT_DIR/box compile --ansi -vv -c box.json.dist
