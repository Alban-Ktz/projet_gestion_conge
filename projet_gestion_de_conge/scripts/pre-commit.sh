#!/bin/bash
RED='\033[1;31m'
GREEN='\033[0;32m'
NC='\033[0m'
DOCKER='docker-compose exec php-fpm'


echo "Running PHPUnit:"
phpunit=$(exec ${DOCKER} php bin/phpunit)
ret_code=$?
# If it didn't pass, announce it failed and print the output
if [ ${ret_code} != 0 ]; then
	printf "\n${RED}PHPUnit failed:${NC}"
	echo "$phpunit\n"
	exit ${ret_code}
else
	printf "${GREEN}PHPUnit passed.${NC}\n"
fi

echo "Running PHPStan:"
phpstan=$(exec ${DOCKER} vendor/bin/phpstan analyse src)
ret_code=$?
if [ ${ret_code} != 0 ]; then
	printf "\n${RED}PHPStan failed:${NC}"
	echo "$phpstan\n"
	exit ${ret_code}
else
	printf "${GREEN}PHPStan passed.${NC}\n"
fi

echo "Running PHP Code sniffer:"
phpcs=$(exec ${DOCKER} vendor/bin/phpcs)
ret_code=$?
if [ ${ret_code} != 0 ]; then
	printf "\n${RED}PHP Code sniffer failed:${NC}"
	echo "$phpcs\n"
	exit ${ret_code}
else
	printf "${GREEN}PHP Code sniffer passed.${NC}\n"
fi

echo "Running PHP Mess Detector:"
phpmd=$(exec ${DOCKER} vendor/bin/phpmd src ansi phpmd.xml)
ret_code=$?
if [ ${ret_code} != 0 ]; then
	printf "\n${RED}PHP Mess Detector failed:${NC}"
	echo "$phpmd\n"
	exit ${ret_code}
else
	printf "${GREEN}PHP Mess Detector passed.${NC}\n"
fi

exit 0
