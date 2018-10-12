docker network create gallery-internal

(cd php && docker build -t uw-php .)
(cd bm-php && docker build -t bm-php .)
(cd microservices/images/python && docker build -t app-python .)

make build -C microservices/storage
make build -C microservices/like
make build -C microservices/images

chmod 777 microservices/storage/code/uploads
chmod 777 microservices/web/code/upload

make start -C microservices/storage
make start -C microservices/like
make start -C microservices/images
make start -C microservices/web
