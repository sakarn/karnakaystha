<?php

namespace Drupal\parichay\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Parichay entities.
 *
 * @ingroup parichay
 */
interface ParichayInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Parichay name.
   *
   * @return string
   *   Name of the Parichay.
   */
  public function getName();

  /**
   * Sets the Parichay name.
   *
   * @param string $name
   *   The Parichay name.
   *
   * @return \Drupal\parichay\Entity\ParichayInterface
   *   The called Parichay entity.
   */
  public function setName($name);

  /**
   * Gets the Parichay creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Parichay.
   */
  public function getCreatedTime();

  /**
   * Sets the Parichay creation timestamp.
   *
   * @param int $timestamp
   *   The Parichay creation timestamp.
   *
   * @return \Drupal\parichay\Entity\ParichayInterface
   *   The called Parichay entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Parichay revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Parichay revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\parichay\Entity\ParichayInterface
   *   The called Parichay entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Parichay revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Parichay revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\parichay\Entity\ParichayInterface
   *   The called Parichay entity.
   */
  public function setRevisionUserId($uid);

}
