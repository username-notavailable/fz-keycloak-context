# Fuzzy keycloak context

### A docker compose development context with:
- PostgreSQL
- Nginx
- Redis
- CoreDNS
- Keycloak

<br />

> New castles (services) they can be added to the context with the console **new** command and other containers with the compose.yaml files

Installation:
```
composer create-project fuzzy/fzkc "foo_dir"
```

Usage:
```
php console list
```

<br />

> Use the console commands for manage the context

- Project name = installation directory name
- Castle name = castle installation directory name

- Dev context services hostnames they will be <project_name>.<service_name>.space 

- Castles hostnames they will be <project_name>.<castle_name>.space listening on the user specified port [**exposed port**]; From the host you can also use localhost with the exposed port

<br />

- Castle is a docker image then it's language agnostic
- Castle created docker image's name will be <project_name>-castle (by default)
- Castle created docker container's name will be <project_name>-<castle_name>-castle-container (by default)

<br />

- Default postgres admin username and password is **postgres**
- Default keycloak database name is **keycloak**
- Default keycloak database account username and password is **keycloak**

<br />

- **For two or more fzkc installations or just to customize the env**, set the network settings and the [**exposed ports**] accordingly... take a look at:

    - context dev docker .env file: https://github.com/username-notavailable/fz-keycloak-context/tree/main/docker/dev/.env

     - context dev docker compose.yaml files: https://github.com/username-notavailable/fz-keycloak-context/blob/main/docker/dev/compose.yaml

    - laravelweb dev docker .env file: https://github.com/username-notavailable/laravelweb/blob/main/_docker/dev/.env

    - laravelweb dev docker compose.yaml file: https://github.com/username-notavailable/laravelweb/blob/main/_docker/dev/compose.yaml

<br />

for issues:<br />
d.viviani@fuzzy-net.it