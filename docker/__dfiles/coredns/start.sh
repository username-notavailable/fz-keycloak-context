#!/bin/bash

/init_dns.sh

/bin/envsubst < /conf/Corefile > /conf/Corefile.running

/coredns -dns.port $FZKC_EXPOSED_PORT_DNS -conf /conf/Corefile.running