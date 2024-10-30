include .env
COMPOSE_PROFILES=$(APP_ENV)
export

#Docker
up:
	docker-compose up -d
down:
	docker-compose down
restart: down up
rebuild:
	docker-compose build
	docker-compose down
	docker-compose up -d
stop:
	docker-compose stop
logs:
	docker-compose logs -f
exec:
	docker-compose exec app bash
cache-clear:
	docker-compose exec app php bin/console cache:clear

#Microsoft Graph API commands
microsoft-list-users:
	docker-compose exec app php bin/console microsoft:list-users
microsoft-list-groups:
	docker-compose exec app php bin/console microsoft:list-groups
microsoft-list-groups-members:
	docker-compose exec app php bin/console microsoft:list-groups-members
microsoft-add-users-to-group-by-ids:
	docker-compose exec app php bin/console microsoft:add-user-to-group-by-ids $(userId) $(groupId)
microsoft-add-users-to-group:
	docker-compose exec app php bin/console microsoft:add-user-to-group $(userEmail) $(groupName)
microsoft-remove-user-from-group-by-ids:
	docker-compose exec app php bin/console microsoft:remove-user-from-group-by-ids $(userId) $(groupId)
microsoft-remove-user-from-group:
	docker-compose exec app php bin/console microsoft:remove-user-from-group $(userEmail) $(groupName)
microsoft-clear-group-by-id:
	docker-compose exec app php bin/console microsoft:clear-group-by-id $(groupId)
microsoft-clear-group-by-name:
	docker-compose exec app php bin/console microsoft:clear-group-by-name $(groupName)
