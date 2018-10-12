# Online image gallery with microservice architecture

## Website which lets you upload images, stores them, displays thumbnails on the front page and lets you like and show the images in full resolution. All four microservices are in their own Docker containers.

### Technologies:
* Docker
* Redis
* RabbitMQ
* nginx
* Python
* PHP

### Created with [blazej24](https://github.com/blazej24)

#### Features:
* nginx web server
* microservice which converts images to lower resolution (creates thumbnails)
* asynchronous communication while sending full sized images
* image storing microservice
* like counting microservice