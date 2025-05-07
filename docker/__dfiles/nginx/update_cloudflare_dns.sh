#!/bin/bash

ACCOUNT_ID="99c9c6af5ecff66a7bd57443c1efd940"
DNS_ZONE_ID="9af7926155634e3c9b22707a06e5c10b"
DNS_RECORD="969b086a94ec02debe10ae16232f2ded"
DNS_RECORD_NAME="test.fuzzy-net.it"
CLOUDFLARE_EMAIL="viviani.demetrio82@gmail.com"
CLOUDFLARE_API_KEY="Sai1utEErKWnsaocSkAPed9Q9rywy0t9e7Okr8H2"

#curl -X GET "https://api.cloudflare.com/client/v4/user/tokens/verify" \
#     -H "Authorization: Bearer $CLOUDFLARE_API_KEY" \
#     -H "Content-Type:application/json"

DNS_IP=$( curl -s GET https://api.cloudflare.com/client/v4/zones/$DNS_ZONE_ID/dns_records/$DNS_RECORD \
    -H "Authorization: Bearer $CLOUDFLARE_API_KEY" \
    -H "Content-Type:application/json" | jq '. | .result.content' )

echo "DNS IP = $DNS_IP"

CURRENT_IP=$(curl -s GET "http://ipinfo.io/json" | jq '. | .ip')

echo "CURRENT IP = $CURRENT_IP"

if [[ "$CURRENT_IP" != "$DNS_IP" ]]
then
	RESULT=$( curl -s PATCH "https://api.cloudflare.com/client/v4/zones/$DNS_ZONE_ID/dns_records/$DNS_RECORD" \
     	-H "Authorization: Bearer $CLOUDFLARE_API_KEY" \
     	-H "Content-Type: application/json" \
     	--data "{\"type\": \"A\", \"name\": \"$DNS_RECORD_NAME\", \"content\": $CURRENT_IP}" )

	echo 
	echo "RESULT:"
	echo "$RESULT" | jq .
	echo

	DONE=$(echo $RESULT | jq '. | .success')

	if [[ "$DONE" == "true" ]]
	then	
		echo "CURRENT_IP != DNS IP - New DNS_IP = $CURRENT_IP - Update done"
	else
		echo "CURRENT_IP != DNS IP - New DNS_IP = $CURRENT_IP - Update failed"
	fi
else
	echo "CURRENT_IP == DNS IP"
fi