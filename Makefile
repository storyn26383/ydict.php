.PHONY: init test format-code build clean release

init:
	composer install

test:
	vendor/bin/pint src tests --test
	vendor/bin/phpunit

format-code:
	vendor/bin/pint src tests

build: clean
	mkdir -p build
	composer install --no-dev
	php -d phar.readonly=off tools/phar-composer build . build/ydict.php.phar

clean:
	rm -rf build

release:
	sed -ri "s/setVersion\('[^']+'\)/setVersion('$(VERSION)')/" src/Command.php
	make build
	git add src/Command.php build/ydict.php.phar
	git commit -m "feat: bump version to $(VERSION)"
	git tag -a "v$(VERSION)" -m "v$(VERSION)"
