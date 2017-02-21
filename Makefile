ifneq ("$(wildcard composer.phar)", "")
		COMPOSER := php composer.phar
	else
		COMPOSER := composer
	endif

all: update

setup:
			ln -snf .env.example .env

setup-testing:
			ln -snf .env.travis .env

update:
			$(COMPOSER) update

update-source:
			$(COMPOSER) update --prefer-source

validate:
			$(COMPOSER) validate

test:
			./vendor/bin/phpunit tests
