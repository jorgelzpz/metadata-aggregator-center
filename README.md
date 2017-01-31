# Metadata Aggregator Center

Web tool to aggregate multiple SAML metadata sources. Requires pyFF.

## Building the Docker image:

    docker build -t metadata-aggregator:latest .

Running it:

    docker run -v $(pwd):/app -p 8000:80 -d -n macenter metadata-aggregator:latest

Attaching a console to it:

    docker exec -i -t macenter /bin/bash
