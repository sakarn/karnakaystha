<?php

namespace Drupal\parichay\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Parichay revision.
 *
 * @ingroup parichay
 */
class ParichayRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Parichay revision.
   *
   * @var \Drupal\parichay\Entity\ParichayInterface
   */
  protected $revision;

  /**
   * The Parichay storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $parichayStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->parichayStorage = $container->get('entity_type.manager')->getStorage('parichay');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'parichay_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => format_date($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.parichay.version_history', ['parichay' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $parichay_revision = NULL) {
    $this->revision = $this->ParichayStorage->loadRevision($parichay_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->ParichayStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Parichay: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Parichay %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.parichay.canonical',
       ['parichay' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {parichay_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.parichay.version_history',
         ['parichay' => $this->revision->id()]
      );
    }
  }

}
