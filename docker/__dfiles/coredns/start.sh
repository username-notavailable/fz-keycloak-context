#!/bin/bash

/init_dns.sh

/coredns -dns.port $FZKC_EXPOSED_PORT_DNS -conf /conf/Corefile