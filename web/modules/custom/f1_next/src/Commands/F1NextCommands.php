<?php

namespace Drupal\f1_next\Commands;

use Consolidation\AnnotatedCommand\CommandError;
use Drupal\consumers\Entity\Consumer;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\simple_oauth\Entity\Oauth2Scope;
use Drupal\user\Entity\User;
use Drush\Commands\DrushCommands;

/**
 * Drush command file for the f1_next module.
 */
class F1NextCommands extends DrushCommands {

  const API_USER_NAME = 'next-api-user';
  const API_USER_ROLE = 'next_api_role';
  const API_SCOPE_NAME = 'nextjs_site';
  const API_USER_MAIL = 'next-api-user@domain.tld';

  /**
   * Generates users and consumers needed for Next.js to speak to Drupal.
   *
   * @command f1_next:setup-users-and-consumers
   * @aliases f1nsuac
   */
  public function setupUsersAndConsumers(): ?CommandError {
    // We want to have two separate consumers:
    $consumers_to_create = [
      [
        'username' => self::API_USER_NAME,
        'mail' => self::API_USER_MAIL,
        'role' => self::API_USER_ROLE,
        'secret' => $_ENV['DRUPAL_CLIENT_SECRET'],
        'client_id' => $_ENV['DRUPAL_CLIENT_ID'],
        'grant_type' => 'client_credentials',
        'scope' => self::API_SCOPE_NAME,
      ],
    ];

    foreach ($consumers_to_create as $consumer) {
      // Create a new user with the required role to be associated with
      // the consumer:
      $new_user = [
        'name' => $consumer['username'],
        'pass' => '',
        'mail' => $consumer['mail'],
        'access' => '0',
        'status' => 1,
        'is_default' => 1,
        'roles' => [$consumer['role']],
      ];

      // Create the new account:
      $account = User::create($new_user);
      try {
        $violations = $account->validate();
        // Check if the account can be created:
        if ($violations->count() > 0) {
          foreach ($violations as $violation) {
            $this->logger()->error($violation->getMessage());
            return new CommandError("Could not create a new user account with the name " . $consumer['username'] . ".");
          }
        }
        $account->save();
      }
      catch (EntityStorageException $e) {
        return new CommandError("Could not create a new user account with the name " . $consumer['username'] . ".");
      }

      // Need to create Dynamic Oauth2Scope config entity now for Consumers
      // to work properly.
      // @phpstan-ignore-next-line
      $scope = Oauth2Scope::create([
        'id' => $consumer['scope'],
        'name' => $consumer['scope'],
        'description' => 'Next.js Site',
        'umbrella' => FALSE,
        'granularity_id' => 'role',
        'granularity_configuration' => [
          'role' => $consumer['role'],
        ],
        'grant_types' => [
          'client_credentials' => [
            'status' => TRUE,
          ],
        ],
      ]);

      try {
        // @todo Validate config entities.
        $scope->save();
      }
      catch (EntityStorageException $e) {
        return new CommandError("Could not create a new scope with id: " . $consumer['scope'] . ".");
      }

      // @phpstan-ignore-next-line
      /** @var \Drupal\consumers\Entity\Consumer $consumer */
      // @phpstan-ignore-next-line
      $consumer = Consumer::create([
        'client_id' => $consumer['client_id'],
        'label' => 'Next-drupal consumer: ' . $consumer['role'],
        'description' => 'This consumer was created by the f1_next:create-user-and-consumer drush command.',
        'is_default' => TRUE,
        'user_id' => $account->id(),
        'roles' => [$consumer['role']],
        'secret' => $consumer['secret'],
        'grant_types' => [$consumer['grant_type']],
        'scopes' => [$consumer['scope']],
      ]);

      try {
        // @phpstan-ignore-next-line
        $violations = $consumer->validate();
        // Check if the consumer can be created:
        if ($violations->count() > 0) {
          foreach ($violations as $violation) {
            $this->logger()->error($violation->getPropertyPath() . ' - ' . $violation->getMessage());
            return new CommandError('Could not create a new consumer.');
          }
        }
        // @phpstan-ignore-next-line
        $consumer->save();
      }
      catch (EntityStorageException $e) {
        return new CommandError('Could not create a new consumer.');
      }

    }
    // Output instructions to the user:
    $this->logger()->success(dt('Consumers created successfully.'));
    return NULL;
  }

}
