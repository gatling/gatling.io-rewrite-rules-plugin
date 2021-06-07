<?php

// Plugin Name: Gatling.io Documentation Rewrite Rules Plugin
// Plugin URI: https://github.com/gatling/gatling.io-rewrite-rules-plugin
// Description: Adds documentation rewrite rules along any other Wordpress's rules
// Version: 1.0
// Date: Jun 04, 2021
// Author: Gatling Corp

function gatling_docs_mod_rewrite_rules_admin_init() {
  global $wp_rewrite;
  $wp_rewrite->wp_rewrite_rules();
  $wp_rewrite->flush_rules();
  flush_rewrite_rules();
}

add_action('admin_init', 'gatling_docs_mod_rewrite_rules_admin_init');

function gatling_docs_mod_rewrite_rules($rules) {
  $new_rules = <<<EOF
<IfModule mod_rewrite.c>

# https://www.clever-cloud.com/doc/deploy/application/php/php-apps/#prevent-apache-to-redirect-https-calls-to-http-when-adding-a-trailing-slash
DirectorySlash Off

RewriteEngine On
RewriteBase /

## 301 /docs/{version}/(jmq|mqtt)/? -> /docs/gatling/reference/{version}(jmq|mqtt)/
RewriteRule ^docs/(current|[0-9.]+)/jms/?$ https://%{HTTP_HOST}/docs/gatling/reference/$1/jms/ [L,R=301]
RewriteRule ^docs/(current|[0-9.]+)/mqtt/?$ https://%{HTTP_HOST}/docs/gatling/reference/$1/mqtt/ [L,R=301]

## 301 /docs/{version}/{tutorial}/? -> /docs/gatling/tutorials/{tutorial}
RewriteRule ^docs/(current|[0-9.]+)/installation/?$ https://%{HTTP_HOST}/docs/gatling/tutorials/installation/ [L,R=301]
RewriteRule ^docs/(current|[0-9.]+)/quickstart/?$ https://%{HTTP_HOST}/docs/gatling/tutorials/quickstart/ [L,R=301]
RewriteRule ^docs/(current|[0-9.]+)/advanced_tutorial/?$ https://%{HTTP_HOST}/docs/gatling/tutorials/advanced/ [L,R=301]

## 301 /docs/{version}/http/http_{page}/ -> /docs/gatling/reference/{version}/http/{page}
RewriteRule ^docs/(current|[0-9.]+)/http/http_protocol/?$ https://%{HTTP_HOST}/docs/gatling/reference/$1/http/protocol/ [L,R=301]
RewriteRule ^docs/(current|[0-9.]+)/http/http_request/?$ https://%{HTTP_HOST}/docs/gatling/reference/$1/http/request/ [L,R=301]
RewriteRule ^docs/(current|[0-9.]+)/http/http_check/?$ https://%{HTTP_HOST}/docs/gatling/reference/$1/http/check/ [L,R=301]
RewriteRule ^docs/(current|[0-9.]+)/http/http_ssl/?$ https://%{HTTP_HOST}/docs/gatling/reference/$1/http/ssl/ [L,R=301]
RewriteRule ^docs/(current|[0-9.]+)/http/http_helpers/?$ https://%{HTTP_HOST}/docs/gatling/reference/$1/http/helpers/ [L,R=301]

## 301 /docs/{version}/cookbook/? -> /docs/gatling/guides/
RewriteRule ^docs/(current|[0-9.]+)/cookbook/?$ https://%{HTTP_HOST}/docs/gatling/guides/ [L,R=301]
RewriteCond %{REQUEST_URI} !(.*)/$
RewriteRule ^docs/(current|[0-9.]+)/cookbook/(.*) https://%{HTTP_HOST}/docs/gatling/guides/$2/ [L,R=301]
RewriteRule ^docs/(current|[0-9.]+)/cookbook/(.*) https://%{HTTP_HOST}/docs/gatling/guides/$2 [L,R=301]
RewriteRule ^docs/(current|[0-9.]+)/realtime_monitoring/?$ https://%{HTTP_HOST}/docs/gatling/guides/realtime_monitoring/ [L,R=301]

## 301 /docs/{version}/migration_guides/{migration} -> /docs/gatling/reference/{version}/migration/{migration}
RewriteRule ^docs/(current|[0-9.]+)/migration_guides/?$ https://%{HTTP_HOST}/docs/gatling/reference/$1/migration/ [L,R=301]
RewriteCond %{REQUEST_URI} !(.*)/$
RewriteRule ^docs/(current|[0-9.]+)/migration_guides/(.*)$ https://%{HTTP_HOST}/docs/gatling/reference/$1/migration/$2/ [L,R=301]
RewriteRule ^docs/(current|[0-9.]+)/migration_guides/(.*)$ https://%{HTTP_HOST}/docs/gatling/reference/$1/migration/$2 [L,R=301]

## 301 /docs/frontline and PDFs
RewriteRule ^docs/frontline/?$ /docs/enterprise/self-hosted/ [L,R=301]
RewriteRule ^docs/frontline/FrontLine-Release-Notes.pdf$ https://%{HTTP_HOST}/docs/enterprise/self-hosted/reference/current/release/release-notes/ [L,R=301]
RewriteRule ^docs/frontline/FrontLine-([0-9-]+)-Highlights.pdf$ https://%{HTTP_HOST}/docs/enterprise/self-hosted/reference/current/release/highlight-$1/ [L,R=301]
RewriteRule ^docs/frontline/FrontLine-Installation-Guide.pdf$ https://%{HTTP_HOST}/docs/enterprise/self-hosted/reference/current/installation/ [L,R=301]
RewriteRule ^docs/frontline/FrontLine-User-Guide.pdf$ https://%{HTTP_HOST}/docs/enterprise/self-hosted/reference/current/user/ [L,R=301]
RewriteRule ^docs/frontline/FrontLine-Plugins-Guide.pdf$ https://%{HTTP_HOST}/docs/enterprise/self-hosted/reference/current/plugins/ [L,R=301]

## Everything else
RewriteRule ^docs/(current|[0-9.]+)/?$ https://%{HTTP_HOST}/docs/gatling/reference/$1/ [L,R=301]
RewriteCond %{REQUEST_URI} !(.*)/$
RewriteRule ^docs/(current|[0-9.]+)/(.*) https://%{HTTP_HOST}/docs/gatling/reference/$1/$2/ [L,R=301]
RewriteRule ^docs/(current|[0-9.]+)/(.*) https://%{HTTP_HOST}/docs/gatling/reference/$1/$2 [L,R=301]

## After everything else, make sure everything ends up with a trailing slash
RewriteCond %{REQUEST_FILENAME} -d
RewriteCond %{REQUEST_URI} !(.*)/$
RewriteRule ^(.+)$ https://%{HTTP_HOST}/$1/ [L,R=301,QSA]
</IfModule>


EOF;

  return $new_rules . $rules;
}

add_filter('mod_rewrite_rules', 'gatling_docs_mod_rewrite_rules');
