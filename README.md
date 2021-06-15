runuser -u www-data -- composer dump-autoload



* * * * * (sleep 0; cd /var/www/html && php artisan command:trainer >> /dev/null 2>&1)
* * * * * (sleep 0; cd /var/www/html && php artisan command:market 1 >> /dev/null 2>&1)
* * * * * (sleep 20; cd /var/www/html && php artisan command:market >> /dev/null 2>&1)
* * * * * (sleep 40; cd /var/www/html && php artisan command:market >> /dev/null 2>&1)