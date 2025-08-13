# Are you using elasticsearch?

## If No,
* Delete the following files
    * `.ddev/docker-compose.elasticsearch.yaml`
    * In the terminal: `composer remove drupal/elasticsearch_connector drupal/elasticsearch_aws_connector`
    * Delete section in the `web/sites/default/settings.php`
  ```
  // Elasticsearch env variables
  $settings['elasticsearch_host'] = $_ENV['ELASTICSEARCH_HOST'];
  $settings['elasticsearch_rest_port'] = $_ENV['ELASTICSEARCH_REST_PORT'];
  $settings['elasticsearch_index_prefix'] = $_ENV['ELASTICSEARCH_INDEX_PREFIX'];
  // Set elasticsearch url and AWS config
  if ($env !== 'local') {
    $config['elasticsearch_connector.cluster.[CLUSTER_MACHINE_NAME]']['url'] = $_ENV['ELASTICSEARCH_HOST'] . ':' . $_ENV['ELASTICSEARCH_REST_PORT'];
    $config['elasticsearch_connector.cluster.[CLUSTER_MACHINE_NAME]']['options']['use_authentication'] = 1;
    $config['elasticsearch_connector.cluster.[CLUSTER_MACHINE_NAME]']['options']['authentication_type'] = 'elasticsearch_aws_connector_aws_signed_requests';
    $config['elasticsearch_connector.cluster.[CLUSTER_MACHINE_NAME]']['options']['elasticsearch_aws_connector_aws_authentication_type'] = 'aws_credentials';
    $config['elasticsearch_connector.cluster.[CLUSTER_MACHINE_NAME]']['options']['elasticsearch_aws_connector_aws_region'] = $_ENV['AWS_REGION'];
    $config['elasticsearch_connector.cluster.[CLUSTER_MACHINE_NAME]']['options']['elasticsearch_aws_connector_aws_credentials_key'] = $_ENV['AWS_KEY'];
    $config['elasticsearch_connector.cluster.[CLUSTER_MACHINE_NAME]']['options']['elasticsearch_aws_connector_aws_credentials_secret'] = $_ENV['AWS_SECRET'];
  }
  ```

## If Yes,

* Please run the following command: `ddev get drud/ddev-elasticsearch`
* Enable the following modules:
    * `elasticsearch_connector`
    * `elasticsearch_aws_connector`
* In the `web/sites/default/settings.php` replace `[CLUSTER_MACHINE_NAME]` with one created in the drupal admin.

Follow [How to: AWS Elasticsearch and Drupal 8]
[How to: AWS Elasticsearch and Drupal 8]:https://forumone.atlassian.net/wiki/spaces/TECH/pages/869400923/How+to+AWS+Elasticsearch+and+Drupal+8