<?php

namespace Drupal\add_vehicle\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Re-populate a dropdown based on form state.
 */
class AddVehicleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ajax_example_dependentdropdown';
  }

  /**
   * {@inheritdoc}
   *
   * The $nojs parameter is specified as a path parameter on the route.
   *
   * @see ajax_example.routing.yml
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nojs = NULL) {

    $yearList = array_combine(range(2023, 1910), range(2023, 1910));
    $APIParamYear = '';
    $APIParamYearMake = '';

    if (empty($form_state->getValue('vehicle_year'))) {
      $vehicleYearValue = '';
     
    } else {
      $vehicleYearValue = $form_state->getValue('vehicle_year');
      $APIParamYear = 'makes/'.$vehicleYearValue;
    }

    if (empty($form_state->getValue('vehicle_make'))) {
      $vehicleMakeIDMakeName = '';
    } else {

      $vehicleMakeIDMakeName = $form_state->getValue('vehicle_make');
      $vehicleMakeIDMakeName = explode('_', $vehicleMakeIDMakeName);

      if(str_word_count($vehicleMakeIDMakeName[1]) > 1) {
        $vehicleMakeName = preg_replace('/\s+/', '-', $vehicleMakeIDMakeName[1]);
      } else {
        $vehicleMakeName = $vehicleMakeIDMakeName[1];
      }

      $APIParamYearMake = 'models/'.$vehicleYearValue.'/'.$vehicleMakeName.'/'.$vehicleMakeIDMakeName[0];
      
    }

    $form['markup'] = [
      '#type' => 'markup',
      '#markup' => '<div class="markup-vehicle"><h4>SET YOUR VEHICLE</h4><P>Get an exact fit for your vehicle.</P></div>'
    ];

    $form['vehicle_detail'] = [
      '#prefix' => '<div id="vehicle-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['vehicle_detail']['vehicle_year'] = [
      '#type' => 'select',
      '#title' => $this->t('1 | Year'),
      '#options' => $yearList,
      '#default_value' => '',
      '#ajax' => [
        'callback' => '::vehicleYearCallback',
        'wrapper' => 'vehicle-make',
      ],
      '#prefix' => '<div id="vehicle-year" class="vehicle-field">',
      '#suffix' => '</div>',
    ];

    $form['vehicle_detail']['vehicle_make'] = [
      '#prefix' => '<div id="vehicle-make"  class="vehicle-field">',
      '#suffix' => '</div>',
      '#validated' => TRUE,
      '#type' => 'select',
      '#title' => $this->t('2 | Make'),
      '#options' => static::getVehicleData($vehicleYearValue, $APIParamYear),
      '#default_value' => !empty($form_state->getValue('vehicle_make')) ? $form_state->getValue('vehicle_make') : '',

      '#ajax' => [
        'callback' => '::vehicleMakeCallback',
        'wrapper' => 'vehicle-model',
      ],
    ];

    $form['vehicle_detail']['vehicle_model'] = [
      '#prefix' => '<div id="vehicle-model"  class="vehicle-field">',
      '#suffix' => '</div>',
      '#validated' => TRUE,
      '#title' => $this->t('3 | Model'),
      '#type' => 'select',
      '#options' => static::getVehicleData($vehicleMakeIDMakeName, $APIParamYearMake),
      '#default_value' => !empty($form_state->getValue('vehicle_model')) ? $form_state->getValue('vehicle_model') : '',

      '#ajax' => [
        'callback' => '::setMessage',
      ],
    ];

    $form['vehicle_detail']['vehicle_engine'] = [
      '#prefix' => '<div id="vehicle-engine"  class="vehicle-field">',
      '#suffix' => '</div>',
      '#validated' => TRUE,
      '#title' => $this->t('4 | Engine'),
      '#type' => 'select',
      '#options' => [],
      '#default_value' => '',
      '#disabled' => True
    ];

    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result_message"></div>'
    ];
    $form['loader'] = [
      '#type' => 'markup',
      '#markup' => '<span class="vehicle-loader icon glyphicon glyphicon-refresh ajax-progress ajax-progress-throbber" aria-hidden="true"></span>'
    ];
    
    if ($vehicleYearValue == '' && $vehicleMakeIDMakeName == '') {
      $form['vehicle_detail']['vehicle_make']['#disabled'] = TRUE;
      $form['vehicle_detail']['vehicle_model']['#disabled'] = TRUE;
    } elseif($vehicleYearValue != '' && $vehicleMakeIDMakeName == '') {
      $form['vehicle_detail']['vehicle_model']['#disabled'] = TRUE;
      $form['vehicle_detail']['vehicle_make']['#disabled'] = FALSE;
    } elseif($vehicleYearValue != '' && $vehicleMakeIDMakeName != '') {
      $form['vehicle_detail']['vehicle_model']['#disabled'] = FALSE;
    }

    $form['#theme'] = 'add_vehicle';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}


  /**
   * Provide a new dropdown based on the AJAX call.
   *
   * This callback will occur *after* the form has been rebuilt by buildForm().
   * Since that's the case, the form should contain the right values for
   * vehicle_make.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The portion of the render structure that will replace the
   *   instrument-dropdown-replace form element.
   */
  public function vehicleYearCallback(array $form, FormStateInterface $form_state) {
    return $form['vehicle_detail']['vehicle_make'];
  }

  /**
   * Provide a new dropdown based on the AJAX call.
   *
   * This callback will occur *after* the form has been rebuilt by buildForm().
   * Since that's the case, the form should contain the right values for
   * vehicle_model.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The portion of the render structure that will replace the
   *   instrument-dropdown-replace form element.
   */
  public function vehicleMakeCallback(array $form, FormStateInterface $form_state) {
    return $form['vehicle_detail']['vehicle_model'];
  }

  /**
   * Provide a new dropdown based on the AJAX call.
   *
   * This callback will occur *after* the form has been rebuilt by buildForm().
   * Since that's the case, the form should contain the right values for
   * vehicle_engine.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The portion of the render structure that will replace the
   *   instrument-dropdown-replace form element.
   */
  public function setMessage(array $form, FormStateInterface $form_state) {

    $client = \Drupal::httpClient();

    $vehicleModel = $form_state->getValue('vehicle_model');

    $httpUrl = 'https://www.autozone.com/ecomm/b2c/v1/ymme/engines/'.$vehicleModel;
  
    try {
      $response = $client->get($httpUrl);
      $result = json_decode($response->getBody(), TRUE);

      $options = [];
        foreach($result as $item) {
          $options['count'] = $item['count']; 
          $options['engine'] = $item['engine']; 
          $options['engineId'] = $item['engineId']; 
        }
    }
    catch (RequestException $e) {
      // log exception
    }

    $response = new AjaxResponse();
    $response->addCommand(
      new HtmlCommand(
        '.result_message',
        '<div class="my_top_message">' . t('Engine name is @engine and Engine ID is @engineId', ['@engine' => $options['engine'], '@engineId' => $options['engineId']]) . '</div>'),
    );
    return $response;
   }

  /**
   * Helper function to populate the dropdown value.
   *
   * Pulling the data from API
   *
   * @param string $key
   *   This will determine which set of options is returned.
   * @param string $APIParam
   *   This is dynamic API parameter.
   *
   * @return array
   *   Dropdown options
   */
  public function getVehicleData($key = '', $APIParam) {
    $options = [];
    if($key) {
      $client = \Drupal::httpClient();

    $httpUrl = 'https://www.autozone.com/ecomm/b2c/v1/ymme/'.$APIParam;

    try {
      $response = $client->get($httpUrl);
      $result = json_decode($response->getBody(), TRUE);
      if(is_numeric($key)) {
        foreach($result as $item) {
          $makeIDVehicleName = $item['makeId'].'_'.$item['make'];
          $options[$makeIDVehicleName] = $item['make']; 
        }
      } else {
        foreach($result as $item) {

          $options[$item['modelId']] = $item['model']; 
        }
      }
    }
    catch (RequestException $e) {
      // log exception
    }
    }
    return $options;
  }
}
