<?php

namespace Drupal\weight\Plugin\migrate\field\d7;

use Drupal\migrate_drupal\Plugin\migrate\field\FieldPluginBase;

/**
 * D7 Weight Class.
 *
 * @MigrateField(
 *   id = "weight",
 *   core = {7},
 *   type_map = {
 *     "weight" = "weight"
 *   },
 *   source_module = "weight",
 *   destination_module = "weight"
 * )
 */
class Weight extends FieldPluginBase {

}
