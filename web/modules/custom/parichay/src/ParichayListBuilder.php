<?php

namespace Drupal\parichay;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Parichay entities.
 *
 * @ingroup parichay
 */
class ParichayListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Parichay ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\parichay\Entity\Parichay $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.parichay.edit_form',
      ['parichay' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
