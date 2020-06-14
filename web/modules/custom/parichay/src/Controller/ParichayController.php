<?php

namespace Drupal\parichay\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\parichay\Entity\ParichayInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ParichayController.
 *
 *  Returns responses for Parichay routes.
 */
class ParichayController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Parichay revision.
   *
   * @param int $parichay_revision
   *   The Parichay revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($parichay_revision) {
    $parichay = $this->entityTypeManager()->getStorage('parichay')
      ->loadRevision($parichay_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('parichay');

    return $view_builder->view($parichay);
  }

  /**
   * Page title callback for a Parichay revision.
   *
   * @param int $parichay_revision
   *   The Parichay revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($parichay_revision) {
    $parichay = $this->entityTypeManager()->getStorage('parichay')
      ->loadRevision($parichay_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $parichay->label(),
      '%date' => $this->dateFormatter->format($parichay->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Parichay.
   *
   * @param \Drupal\parichay\Entity\ParichayInterface $parichay
   *   A Parichay object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ParichayInterface $parichay) {
    $account = $this->currentUser();
    $parichay_storage = $this->entityTypeManager()->getStorage('parichay');

    $langcode = $parichay->language()->getId();
    $langname = $parichay->language()->getName();
    $languages = $parichay->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $parichay->label()]) : $this->t('Revisions for %title', ['%title' => $parichay->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all parichay revisions") || $account->hasPermission('administer parichay entities')));
    $delete_permission = (($account->hasPermission("delete all parichay revisions") || $account->hasPermission('administer parichay entities')));

    $rows = [];

    $vids = $parichay_storage->revisionIds($parichay);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\parichay\ParichayInterface $revision */
      $revision = $parichay_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $parichay->getRevisionId()) {
          $link = $this->l($date, new Url('entity.parichay.revision', [
            'parichay' => $parichay->id(),
            'parichay_revision' => $vid,
          ]));
        }
        else {
          $link = $parichay->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.parichay.translation_revert', [
                'parichay' => $parichay->id(),
                'parichay_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.parichay.revision_revert', [
                'parichay' => $parichay->id(),
                'parichay_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.parichay.revision_delete', [
                'parichay' => $parichay->id(),
                'parichay_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['parichay_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
