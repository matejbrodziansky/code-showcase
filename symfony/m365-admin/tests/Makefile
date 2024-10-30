.PHONY: all adduser removeuser

include .env
export

all: adduser removeuser

adduser:
	cd ..; docker-compose exec app php bin/console microsoft:add-user-to-group-by-ids $$TESTUSER $$TESTGROUP

removeuser:
	cd ..; docker-compose exec app php bin/console microsoft:remove-user-from-group-by-ids $$TESTUSER $$TESTGROUP
