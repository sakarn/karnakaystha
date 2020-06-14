<?php

namespace Drupal\parichay;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\parichay\Entity\ParichayInterface;

/**
 * Defines the storage handler class for Parichay entities.
 *
 * This extends the base storage class, adding required special handling for
 * Parichay entities.
 *
 * @ingroup parichay
 */
class ParichayStorage extends SqlContentEntityStorage implements ParichayStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ParichayInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {parichay_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {parichay_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ParichayInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {parichay_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('parichay_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
