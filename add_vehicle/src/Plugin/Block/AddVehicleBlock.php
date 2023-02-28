<?php
/**
 * @file
 * Contains \Drupal\add_vehile\Plugin\Block\AddVehicleBlock.
 */

namespace Drupal\add_vehicle\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a 'add_vehicle' block.
 *
 * @Block(
 *   id = "add_vehicle_block",
 *   admin_label = @Translation("Add Vehicle block"),
 *   category = @Translation("Custom Add Vehicle block")
 * )
 */
class AddVehicleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $form = \Drupal::formBuilder()->getForm('Drupal\add_vehicle\Form\AddVehicleForm');

    return $form;
   }
}