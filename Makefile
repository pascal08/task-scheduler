style-check:
	vendor/bin/phpcs --extensions=php

style-fix:
	vendor/bin/phpcbf

test:
	vendor/bin/phpunit --coverage-text --coverage-html tests/_reports/

test-no-cov:
	vendor/bin/phpunit --no-coverage

static-analyse:
	vendor/bin/phpstan analyse --level=7 ./ -c ./phpstan.neon

ci: style-check static-analyse test-no-cov