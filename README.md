## installation
1. Run composer install on your cmd or terminal
2. Copy .env.example file to .env on the root folder. You can type copy .env.example .env if using command prompt Windows or cp .env.example .env if using terminal, Ubuntu
3. Open your .env file and change the database name (DB_DATABASE) to whatever you have, username (DB_USERNAME) and password (DB_PASSWORD) field correspond to your configuration.
4. Run php artisan key:generate
5. Run php artisan migrate
6. Run php artisan serve
