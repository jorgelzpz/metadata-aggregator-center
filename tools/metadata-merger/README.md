This tool combines all `saml20-idp-remote.php` files found on a directory and its subdirectories,
and combines them into a single output file.

Entities that appear twice or more will be returned just once, combining their 'tags' field, if
present.

Usage:

    merge <source> <destination>

Example:

    merge /var/simplesaml/metadata/downloaded /var/simplesaml/metadata/saml20-idp-remote.php
