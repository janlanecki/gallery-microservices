init:
	docker swarm init || echo "."
	cd php; make; cd -
	docker network create -d overlay gallery-internal || echo "Network already initialized"