.PHONY: init test format-code build release

init:
	composer install

test:
	vendor/bin/pint src tests --test
	vendor/bin/phpunit

format-code:
	vendor/bin/pint src tests

build:
	php -d phar.readonly=off build/build.php

release:
	sed -ri "s/setVersion\('[^']+'\)/setVersion('$(VERSION)')/" src/Command.php
	make build
	git add src/Command.php build/ydict.php.phar
	git commit -m "feat: bump version to $(VERSION)"
	git tag -a "v$(VERSION)" -m "v$(VERSION)"
