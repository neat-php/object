FROM ubuntu:18.04

RUN export DEBIAN_FRONTEND=noninteractive DEBCONF_NONINTERACTIVE_SEEN=true && \
    apt-get update && \
    apt-get install -y curl git software-properties-common unzip && \
    add-apt-repository ppa:ondrej/php && \
    apt-get update && \
    apt-get install -y php7.4-cli php7.4-sqlite3 php7.4-pdo php7.4-xml php7.4-mbstring && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV PATH="/usr/local/bin:${PATH}"

WORKDIR /src

CMD vendor/bin/phpunit
