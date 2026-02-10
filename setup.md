## Setup guide

1. Clone repository
2. Download a suitable php version with xampp (https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/5.6.40/)[like 5.6]
3. Run `composer install --ignore-platform-reqs --no-scripts` to install dependencies
4. Run `php artisan key:generate` to generate app key
5. To clear caches run 
```
php artisan clear-compiled
php artisan config:clear
php artisan cache:clear
php artisan dump-autoload
```
6. To run app `php artisan serve`
