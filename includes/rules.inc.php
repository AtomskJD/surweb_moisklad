<?php 
include_once('moysklad.class.php');
include_once('functions.inc.php');

function smoy_info () {
  kpr(phpinfo());
}

/**
 * Implement hook_rules_action_info()
 * Declare any meta data about actions for Rules
 */
function surweb_moysklad_rules_action_info() {
  $actions = array(
    'smoy_checkProduct' => array(
      'base' => 'smoy_rules_action_checkProduct',
        'label' =>  'Проверить товар',
        'group' => "surweb",
        'parameter' => array(
          'value' => array('type' => 'unknown', 'label' => t('Value to debug')),
      ),
    ),

    // СОЗДАНИЕ ЗАКАЗА
    'smoy_sendOrder' => array(
      'base' => 'smoy_rules_action_send_order',
        'label' =>  'Отправить новый заказ',
        'group' => "surweb",
        'parameter' => array(
          'value' => array('type' => 'unknown', 'label' => t('Value to debug')),
      ),
    ),


    // ИЗМЕНЕНИЕ ЗАКАЗА
    'smoy_updateOrder' => array(
      'base' => 'smoy_rules_action_update_order',
        'label' =>  'Изменить существующий заказ',
        'group' => "surweb",
        'parameter' => array(
          'value' => array('type' => 'unknown', 'label' => t('Value to debug')),
      ),
    ),

    // УДАЛЕНИЕ ЗАКАЗА
    'smoy_deleteOrder' => array(
      'base' => 'smoy_rules_action_delete_order',
        'label' =>  'Удалить существующий заказ',
        'group' => "surweb",
        'parameter' => array(
          'value' => array('type' => 'unknown', 'label' => t('Value to debug')),
      ),
    ),

    'smoy_debug' => array(
      'base' => 'smoy_rules_debug_action',
      'label' =>  'Дебажить',
      'group' => "surweb",
      'parameter' => array(
        'value' => array('type' => 'unknown', 'label' => t('Value to debug')),
      ),
    ),



    'demo_rules_actions_hello_world' => array(
      'label' => t('Print Hello World on the page'),
      'group' => t('Rules Example'),
      'module' => 'surweb_moysklad',
    ),
    'demo_rules_actions_hello_user' => array(
      'label' => t('Print Hello to the logged in user'),
      'group' => t('Rules Example'),
      'module' => 'surweb_moysklad',
      'parameter' => array(
        'account' => array(
          'type' => 'user',
          'label' => t('User to say hello to'),
        ),
      'message' => array(
          'type' => 'text',
          'label' => t('Message'),
          'description' => t("The message body."),
        ),
      ),
    ),

  );
 
  return $actions;
}



/**
 * Rules action for debugging values.
 */
function smoy_rules_debug_action($value) {
  dpm($value);
}


/**
 * Rules action for debugging values.
 */
function smoy_rules_action_send_order( $order ) {
  $moysklad = new Moysklad();
  $actonOrder = $moysklad->setCustomerOrder($order);

  /*
    Обработка непредвиденных событий (логика обработчика)
    Ряд событий требуют повторной отправки используя очереди
   */
  
  if ($actonOrder->code == 200) {
    drupal_set_message($actionOrder->message, 'status', FALSE);
  } elseif (
      $actonOrder->code == 404 || // Запрошенный ресурс не существует
      $actonOrder->code == 429 || // Превышен лимит количества запросов
      $actonOrder->code == 500 || // При обработке запроса возникла непредвиденная ошибка
      $actonOrder->code == 502 || // Сервис временно недоступен
      $actonOrder->code == 503 || // Сервис временно отключен
      $actonOrder->code == 504    // Превышен таймаут обращения к сервису, повторите попытку позднее
    ) {
      $queue_add    = DrupalQueue::get('surweb_moysklad_add_oreder');
      $queue_add->createQueue();
      $queue_add->createItem($order);
      drupal_set_message($actionOrder->message, 'error', FALSE);
  }

}


function smoy_rules_action_update_order( $order ) {
  $moysklad = new Moysklad();
  $actonOrder = $moysklad->putCustomerOrder($order);
  

  if ($actonOrder->code == 200) {
    drupal_set_message($actionOrder->message, 'status', FALSE);
  } elseif (
      $actonOrder->code == 404 || // Запрошенный ресурс не существует
      $actonOrder->code == 429 || // Превышен лимит количества запросов
      $actonOrder->code == 500 || // При обработке запроса возникла непредвиденная ошибка
      $actonOrder->code == 502 || // Сервис временно недоступен
      $actonOrder->code == 503 || // Сервис временно отключен
      $actonOrder->code == 504    // Превышен таймаут обращения к сервису, повторите попытку позднее
    ) {
      $queue_add    = DrupalQueue::get('surweb_moysklad_update_oreder');
      $queue_add->createQueue();
      $queue_add->createItem($order);
      drupal_set_message($actionOrder->message, 'error', FALSE);
  }

}



function smoy_rules_action_delete_order( $order ) {
  $moysklad = new Moysklad();
  $actonOrder = $moysklad->delCustomerOrder($order);
  drupal_set_message($actionOrder->message, 'message', FALSE);

}




function smoy_rules_action_checkProduct($code) {
  drupal_set_message($code, 'status', FALSE);
  $moysklad = new Moysklad();
  
  $product = $moysklad->getProduct( $code );
  $message  = "<strong>Проверка товара на МоемСкладе код (" .$product->code. ")</strong>";

  drupal_set_message($message ."<br>". $product->message , 'status', FALSE);
  dpm($product->data);

  if ( $product->code == 200) {
    $stock = $moysklad->getProductStock($product->data->id);
    $message = "<strong>Проверка остатков склада для ".$product->data->id."</strong>";
    drupal_set_message($message ."<br>". $stock->message , 'status', FALSE);
    dpm($stock->data);
  }
}

/**
 * The action function for rules_example_action_hello_world
 */
function demo_rules_actions_hello_world() {
  drupal_set_message(t('Hello World'));
} 
 
/** 
 * The action function for rules_example_action_hello_user
 */
function demo_rules_actions_hello_user($account, $text) {
  drupal_set_message(t('Hello @username', array('@username' => $account->name)));
  drupal_set_message($text);
}