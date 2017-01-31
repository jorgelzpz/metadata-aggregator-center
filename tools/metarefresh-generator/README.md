This tool generates a suitable config file for the SimpleSAMLphp's metarefresh module.

Calling the generator:

    generator [--output OUTPUT] [--] <url>

Example:

    generator \
        --output /var/simplesaml/config/config-metarefresh.php \
        https://aggregator.mydomain.tld

The generated config-metarefresh.php file will load config-metarefresh-extra.php if
present in the same directory, allowing you to have additional metarefresh configurations.
