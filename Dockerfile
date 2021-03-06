FROM php:7.0-apache

RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load

RUN apt-get update -y
RUN apt-get install -y build-essential
RUN apt-get install -y git
RUN apt-get install -y ruby-full
RUN apt-get install -y nodejs
RUN apt-get install -y npm
RUN ln -s /usr/bin/nodejs /usr/bin/node

RUN gem install compass
RUN npm install -g grunt-cli bower

RUN mkdir /dist
COPY . /dist
WORKDIR /dist

RUN npm install
RUN echo '{ "allow_root": true }' > /root/.bowerrc
RUN bower install
RUN grunt build

WORKDIR /var/www/html/
