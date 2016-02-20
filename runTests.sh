#!/bin/env sh
#set -euo pipefail

docker run -v $(pwd):/app:rw mkenney/phpunit -c /app/test/phpunit.xml

