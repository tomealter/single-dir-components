<?php

/**
 * @file
 * Provides all environment specific includes and indicator settings.
 */

// Setup per-environment config sync settings.
$config['config_split.config_split.prod']['status'] = FALSE;
$config['config_split.config_split.stage']['status'] = FALSE;
$config['config_split.config_split.dev']['status'] = FALSE;
$config['config_split.config_split.local']['status'] = FALSE;

// Environment indicator defaults.
$config['environment_indicator.indicator']['fg_color'] = '#FFFFFF';
$config['environment_indicator.indicator']['name'] = 'Local';

// Determine passed-in environment based on hosting provider ENV variables.
$site_environment = '';
switch (TRUE) {
  case isset($_ENV['AH_SITE_ENVIRONMENT']):
    $site_environment = $_ENV['AH_SITE_ENVIRONMENT'];
    break;

  case isset($_ENV['PANTHEON_ENVIRONMENT']):
    $site_environment = $_ENV['PANTHEON_ENVIRONMENT'];
    $pantheon_settings = $base_path . '/settings.pantheon.php';
    if (file_exists($pantheon_settings)) {
      include $pantheon_settings;
    }
    break;

  case isset($_ENV['ENVIRONMENT']):
    $site_environment = $_ENV['ENVIRONMENT'];
    break;
}

/**
 * Enable appropriate config overrides. Feel free to adjust as necessary.
 *
 * These multi-case statements are meant to be platform agnostic and cover
 * a wide range of potential options.
 */
switch ($site_environment) {
  case 'prod':
  case 'live':
    $config['config_split.config_split.prod']['status'] = TRUE;
    $config['environment_indicator.indicator']['bg_color'] = '#A61429';
    $config['environment_indicator.indicator']['name'] = 'Prod';
    $env = 'prod';
    break;

  case 'f1-stage':
  case 'test':
  case 'stage':
    $config['config_split.config_split.stage']['status'] = TRUE;
    $config['environment_indicator.indicator']['bg_color'] = '#2A046A';
    $config['environment_indicator.indicator']['name'] = 'Stage';
    $env = 'stage';
    break;

  case 'f1-dev':
  case 'dev':
    $config['config_split.config_split.dev']['status'] = TRUE;
    $config['environment_indicator.indicator']['bg_color'] = '#142C64';
    $config['environment_indicator.indicator']['name'] = 'Dev';
    $env = 'dev';
    break;

  default:
    $config['config_split.config_split.local']['status'] = TRUE;
    $config['environment_indicator.indicator']['bg_color'] = '#000000';
    $env = 'local';
    break;
}

// Elasticsearch env variables.
if (!empty($_ENV['CLUSTER_MACHINE_NAME'])) {
  $cluster_machine_name = $_ENV['CLUSTER_MACHINE_NAME'];
  $settings['elasticsearch_host'] = $_ENV['ELASTICSEARCH_HOST'];
  $settings['elasticsearch_rest_port'] = $_ENV['ELASTICSEARCH_REST_PORT'];
  $settings['elasticsearch_index_prefix'] = $_ENV['ELASTICSEARCH_INDEX_PREFIX'];
  // Set elasticsearch url and AWS config.
  if ($env !== 'local') {
    $config['elasticsearch_connector.cluster.' . $cluster_machine_name]['url'] = $_ENV['ELASTICSEARCH_HOST'] . ':' . $_ENV['ELASTICSEARCH_REST_PORT'];
    $config['elasticsearch_connector.cluster.' . $cluster_machine_name]['options']['use_authentication'] = 1;
    $config['elasticsearch_connector.cluster.' . $cluster_machine_name]['options']['authentication_type'] = 'elasticsearch_aws_connector_aws_signed_requests';
    $config['elasticsearch_connector.cluster.' . $cluster_machine_name]['options']['elasticsearch_aws_connector_aws_authentication_type'] = 'aws_credentials';
    $config['elasticsearch_connector.cluster.' . $cluster_machine_name]['options']['elasticsearch_aws_connector_aws_region'] = $_ENV['AWS_REGION'];
    $config['elasticsearch_connector.cluster.' . $cluster_machine_name]['options']['elasticsearch_aws_connector_aws_credentials_key'] = $_ENV['AWS_KEY'];
    $config['elasticsearch_connector.cluster.' . $cluster_machine_name]['options']['elasticsearch_aws_connector_aws_credentials_secret'] = $_ENV['AWS_SECRET'];
  }
}
elseif (
  !empty(getenv('ELASTICSEARCH_HOST'))
  && !empty(getenv('ELASTICSEARCH_REST_PORT'))
) {
  $config['elasticsearch_connector.cluster.' . $cluster_machine_name]['url'] = $_ENV['ELASTICSEARCH_HOST'] . ':' . $_ENV['ELASTICSEARCH_REST_PORT'];
}

// Load any environment-specific settings files.
$settings_file = $base_path . '/settings.' . $env . '.php';
if (file_exists($settings_file)) {
  include $settings_file;
}

// Load any environment-specific services definition files.
$services_file = $base_path . '/services.' . $env . '.yml';
if (file_exists($services_file)) {
  $settings['container_yamls'][] = $services_file;
}
