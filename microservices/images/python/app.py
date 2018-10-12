from redis import Redis, RedisError
import json

import pika
import urllib

from PIL import Image
from resizeimage import resizeimage
import requests
import time
time.sleep(10)
# Connect to Redis
redis = Redis(host="images_redis", db=0, socket_connect_timeout=2, socket_timeout=2)

connection = pika.BlockingConnection(pika.ConnectionParameters(host="images_rabbitmq"))
channel = connection.channel()

channel.exchange_declare(exchange='images',
                         exchange_type='fanout')

result = channel.queue_declare()
queue_name = result.method.queue

channel.queue_bind(exchange='images',
                   queue=queue_name)

print(' [*] Waiting for requests.')

def send_file(path):
    files = {'file': open(path, 'rb')}
    raw_resp = requests.post("http://storage_app:80/files", files=files)
    print(raw_resp.text)
    return raw_resp.json()

def callback(ch, method, properties, body):
    print(" [x] %r" % body)
    number = redis.incr("counter")

    msg = json.loads(body)
    print(msg["url"])
    print(msg["name"])

    dir_path =  '/tmp/'
    photo_filename = 'photo-' + str(number) + '.jpg'
    thumb_filename = 'thumb-' + str(number) + '.jpg'

    img_data = requests.get(msg["url"])
    print(img_data)
    #print(img_data.content)
    #print(img_data.text)
    with open(dir_path + photo_filename, 'wb') as handler:
        handler.write(img_data.content)

    #urllib.urlretrieve(msg["url"], dir_path + photo_filename)

    with open(dir_path + photo_filename, 'r+b') as f:
        with Image.open(f) as image:
            thumb = resizeimage.resize('thumbnail', image, [200, 200])
            thumb.save(dir_path + thumb_filename, image.format)

    response1 = send_file(dir_path + thumb_filename)
    response2 = send_file(dir_path + photo_filename)
    if response1["status"] and response2["status"]:
        redis.set('name:' + str(number), msg["name"])
        redis.set('thumb:' + str(number), response1['id'])
        redis.set('full:' + str(number), response2['id'])
        redis.rpush('list-photos', str(number))

    ch.basic_ack(delivery_tag = method.delivery_tag)

channel.basic_consume(callback,
                      queue=queue_name)

channel.start_consuming()