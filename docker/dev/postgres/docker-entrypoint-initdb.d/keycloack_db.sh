#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
	CREATE USER keycloak WITH PASSWORD 'keycloak';
	CREATE DATABASE keycloak OWNER keycloak ENCODING 'UTF8' LOCALE_PROVIDER 'icu' ICU_LOCALE 'und' LC_COLLATE 'C.UTF-8' LC_CTYPE 'C.UTF-8' template template0;
EOSQL
