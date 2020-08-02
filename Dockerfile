FROM tidakdijual/php_7.2_oci8

RUN mkdir /var/www/html/site
ADD . /var/www/html/site/

WORKDIR /var/www/html/site

RUN chmod 777 bootstrap -R
RUN chmod 777 storage -R


RUN /usr/bin/composer install
COPY conf/vendor/. /var/www/html/site/vendor/

COPY conf/site.conf /etc/apache2/sites-enabled/site.conf
COPY conf/php.ini /usr/local/etc/php/php.ini

EXPOSE 81
 
