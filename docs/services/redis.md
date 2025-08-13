# Are you using redis on the pantheon environment?

## If Yes,
[Pantheon Redis]:https://pantheon.io/docs/object-cache

To add redis run: `ddev get drud/ddev-redis`

As of this writing the default `redis` version that is used for `DDev` is version `6`. That does not work for pantheon.

To change the redis version:
1. Please open `.ddev/docker-compose.redis.yaml`
2. If you are using `php 7.4`, change and setting u for pantheon: `image: redis:6` > `image: redis:5`

For more information visit: [Pantheon Redis].
