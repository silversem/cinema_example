Cinema example
==============

###Instructions of how to install:

1) $ curl -sS https://getcomposer.org/installer | php

2) $ composer.phar install

3) $ composer.phar update

4) MySQL dump in ./dump directory

5) Change mysql connection settings in ./web/index.php

6) Apache's virtual host file should look like below:

    <VirtualHost *:80>

        ServerName cinema_example.local.com
        ServerAlias www.cinema_example.local.com

        DocumentRoot /home/semyen/sites/cinema_example
        CustomLog /home/semyen/sites/cinema_example/log/com.silex_access_log common
        ErrorLog /home/semyen/sites/cinema_example/log/com.silex_error_log

        DocumentRoot "/home/semyen/sites/cinema_example/web/"

        <Directory /home/semyen/sites/cinema_example/web/>
            Options Indexes FollowSymLinks
            AllowOverride All
            Require all granted
            DirectoryIndex index.php
        </Directory>

    </VirtualHost>


*Semyen Pavlov.*