.PHONY: init test format-code build

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
