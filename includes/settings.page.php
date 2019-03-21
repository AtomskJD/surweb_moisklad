<?php 

include_once('moysklad.class.php');
include_once("functions.inc.php");

function smoy_settings_page ($form, &$form_state) {
  $settings_page = array();

 // Панель информации о соединении
    $settings_page['connection'] = array(
        '#type' => 'fieldset',
        '#title' => 'Настройки пользователя',
    );

     $settings_page['connection']['moysklad_login'] = array(
          '#type' => 'textfield',
          '#title' => "Аккаунт на moysklad",
          '#default_value' => variable_get('moysklad_login', 'user@name'),
          '#size' => 30,
          '#maxlength' => 30,
          '#description' => "Имя пользователя вида user@name",
          '#required' => TRUE,
      );
    $settings_page['connection']['moysklad_pass'] = array(
          '#type' => 'textfield',
          '#title' => "Пароль пользователя moysklad",
          '#default_value' => variable_get('moysklad_pass', ''),
          '#size' => 30,
          '#maxlength' => 30,
          '#required' => TRUE,
      );
    $settings_page['connection']['check_connection'] = array(
      '#type' => 'submit',
      '#value' => 'Проверить логин',
      '#weight' => 15,
      '#submit' => array('smoy_checkConnection'),
    );
   
   
    // Заказ по умолчанию
    $settings_page['defaults'] = array(
      '#type' => 'fieldset',
      '#title' => 'Параметры заказа поумолчанию',
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#weight' => 15,
    );

      /* Поля */
      $settings_page['defaults']['moysklad_counterparty'] = array(
        '#type' => 'textfield',
        '#title' => "E-mail контрагента для сайта",
        '#default_value' => variable_get('moysklad_counterparty', ''),
        '#size' => 30,
        '#maxlength' => 30,
        '#required' => TRUE,
      );

      $settings_page['defaults']['moysklad_store'] = array(
        '#type' => 'textfield',
        '#title' => "Код склада",
        '#description' => 'Используется внешний код склада <a href="https://online.moysklad.ru/app/#Warehouse/" target="_blank">страница складов</a>',
        '#default_value' => variable_get('moysklad_store', ''),
        '#size' => 30,
        '#maxlength' => 30,
        '#required' => TRUE,
      );

      /* Кнопки */
      $settings_page['defaults']['check_counterparty'] = array(
        '#type' => 'submit',
        '#value' => 'Проверить контагента',
        '#weight' => 15,
        '#submit' => array('smoy_checkDefaults'),
        );

  // drupal_set_message(t('dodo'), 'status', FALSE);
  return system_settings_form ( $settings_page );

  // $listOfRequests = $test->getQueriesList(8000, array('offset' => 0, 'limit' => 50));
  // dpm($listOfRequests);

  /* 
  $remote_data = $test->getAllItems(3000);

  // DB search
  $t_start = microtime(true);
  $products = array();
  foreach ($remote_data as $row) {
    // dpm($row);
    $product = db_select('uc_products', 'p')
      ->fields('p', array('nid', 'model'))
      ->condition('p.model', $row->code)
      ->execute()
      ->fetchAll();

    $products[] = $product;
  }
      dpm($products);
      dpm(microtime(true) - $t_start, 'DB timer');

*/

  // return system_settings_form( $adminPage->getForm() );
}