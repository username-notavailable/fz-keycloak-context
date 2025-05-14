#!/bin/bash

if [[ "$FZKC_NETWORK_DNS_IP" != "53" ]]
then
    iptables -t nat -A OUTPUT -d $FZKC_NETWORK_DNS_IP -p tcp --dport 53 -j REDIRECT --to-ports $FZKC_EXPOSED_PORT_DNS
    iptables -t nat -A OUTPUT -p udp -d $FZKC_NETWORK_DNS_IP --dport 53 -j DNAT --to-destination $FZKC_NETWORK_DNS_IP:$FZKC_EXPOSED_PORT_DNS
fi