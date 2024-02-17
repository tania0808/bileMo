## Prerequisites
#### You need to have installed on your machine:

- Composer : https://getcomposer.org/
- Docker : https://www.docker.com/

## Usage
1. Clone the repository:
```
git clone git@github.com:tania0808/bileMo.git
```

2. Configure your environment variables and yout database
```
DATABASE_URL=
```

3. Run the application:
```
composer install
docker-compose up
symfony server:start -d
```
4. Load the fixtures:
```
php bin/console doctrine:fixtures:load
```