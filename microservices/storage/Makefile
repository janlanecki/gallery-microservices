build:
	docker-compose build
	docker run --rm --volume $$PWD:/app composer install -n

start:
	docker-compose up -d --force-recreate

stop:
	docker-compose rm -f
	docker-compose stop

restart: stop start

respawn: rm start

rm:
	docker-compose down -v --remove-orphans

ps:
	docker-compose ps

deploy:
	docker stack deploy -c docker-compose.yml storage
