<?php

namespace Drupal\parichay;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface ParichayStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Parichay revision IDs for a specific Parichay.
   *
   * @param \Drupal\parichay\Entity\ParichayInterface $entity
   *   The Parichay entity.
   *
   * @return int[]
   *   Parichay revision IDs (in ascending order).
   */
  public function revisionIds(ParichayInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Parichay author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Parichay revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\parichay\Entity\ParichayInterface $entity
   *   The Parichay entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ParichayInterface $entity);

  /**
   * Unsets the language for all Parichay with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
