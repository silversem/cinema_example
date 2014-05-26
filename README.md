Cinema example
==============

###Instructions of how to install:

1) $ git clone git@github.com:silversem/cinema_example.git

2) $ cd cinema_example

3) $ curl -sS https://getcomposer.org/installer | php

4) $ ./composer.phar install

5) $ ./composer.phar update

6) MySQL dump in ./dump directory

7) Change mysql connection settings in ./web/index.php

8) Apache's virtual host file should look like below:

    <VirtualHost *:80>

        ServerName cinema_example.local.com
        ServerAlias www.cinema_example.local.com

        DocumentRoot /home/cinema_example
        CustomLog /home/cinema_example/log/com.silex_access_log common
        ErrorLog /home/cinema_example/log/com.silex_error_log

        DocumentRoot "/home/cinema_example/web/"

        <Directory /home/cinema_example/web/>
            Options Indexes FollowSymLinks
            AllowOverride All
            Require all granted
            DirectoryIndex index.php
        </Directory>

    </VirtualHost>


*Semyen Pavlov.*
