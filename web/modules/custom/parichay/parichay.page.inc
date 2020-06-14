<?php

/**
 * @file
 * Contains parichay.page.inc.
 *
 * Page callback for Parichay entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Parichay templates.
 *
 * Default template: parichay.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_parichay(array &$variables) {
  // Fetch Parichay Entity Object.
  $parichay = $variables['elements']['#parichay'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
