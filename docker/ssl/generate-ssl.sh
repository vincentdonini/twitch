#!/bin/bash

# Definition of the certificate storage folder in Docker
CERT_DIR="./docker/nginx/ssl"
mkdir -p "$CERT_DIR"

# Certificate file paths
PRIVATE_KEY="$CERT_DIR/certificate.key"
CERT_FILE="$CERT_DIR/certificate.crt"

# OpenSSL configuration file path (make sure this file exists)
OPENSSL_CNF="./docker/ssl/openssl.cnf"

# Deleting old certificates if necessary
rm -f "$PRIVATE_KEY" "$CERT_FILE"

# Generating the private key
openssl genpkey -algorithm RSA -out "$PRIVATE_KEY"

# Creating the self-signed certificate for 1 year, using the openssl.cnf configuration file
openssl req -new -key "$PRIVATE_KEY" -x509 -out "$CERT_FILE" -days 365 -config "$OPENSSL_CNF"

echo "✅ SSL certificate successfully generated in $CERT_DIR"
