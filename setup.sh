#!/usr/bin/env bash

# Resolve project root (directory of this script)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Run the PHP setup script
php "$SCRIPT_DIR/bin/setup.php"
