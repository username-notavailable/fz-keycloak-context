#!/bin/bash

if [[ "$FZKC_NETWORK_DNS_IP" != "53" ]]
then
    iptables -t nat -A OUTPUT -d 127.0.0.1 -p tcp --dport 53 -j REDIRECT --to-ports $FZKC_EXPOSED_PORT_DNS
    iptables -t nat -A OUTPUT -p udp -d 127.0.0.1 --dport 53 -j DNAT --to-destination 127.0.0.1:$FZKC_EXPOSED_PORT_DNS
fi