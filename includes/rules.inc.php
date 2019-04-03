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
          'value' => array('type' => 'unknown', 'label' =>  'Пременная заказа'),
      ),
    ),


    // ИЗМЕНЕНИЕ ЗАКАЗА
    'smoy_updateOrder' => array(
      'base' => 'smoy_rules_action_update_order',
        'label' =>  'Изменить существующий заказ',
        'group' => "surweb",
        'parameter' => array(
          'value' => array('type' => 'unknown', 'label' => 'Пременная заказа'),
      ),
    ),

    
    // УДАЛЕНИЕ ЗАКАЗА
    'smoy_deleteOrder' => array(
      'base' => 'smoy_rules_action_delete_order',
        'label' =>  'Удалить существующий заказ',
        'group' => "surweb",
        'parameter' => array(
          'value' => array('type' => 'unknown', 'label' => 'Переменная заказа'),
      ),
    ),

    // Дебаг принятой переменной DPM
    'smoy_debug' => array(
      'base' => 'smoy_rules_debug_action',
      'label' =>  'Дебажить',
      'group' => "surweb",
      'parameter' => array(
        'value' => array('type' => 'unknown', 'label' => t('Value to debug')),
      ),
    ),



    // Принудительное применение правил ценообразования
    'smoy_refreshOrder' => array(
      'base' => 'smoy_rules_action_refresh_order',
        'label' =>  'Применить правила ценообразования',
        'group' => "surweb",
        'parameter' => array(
          'value' => array('type' => 'unknown', 'label' => 'Переменная заказа'),
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
 * Implements of hook_rules_condition_info().
 */
function surweb_moysklad_rules_condition_info() {
  $conditions = array(
    'smoy_checkDiscount' => array(
      'base' => 'smoy_rules_conditions_checkDiscount',
        'label' =>  'Проверить скидку',
        'group' => "surweb",
        'parameter' => array(
          'value' => array('type' => 'unknown', 'label' => t('order')),
          'value2' => array('type' => 'text', 'label' => t('Value to debug'),'description' => t("The message body.")),
      ),
    ),
  );


  return $conditions;
}







/**
 * Rules action for debugging values.
 */
function smoy_rules_debug_action($value) {
  dpm($value);
}






/**
 * Экшен правило отправки заказа на мой склад
 * @param  Object  $order   Объект заказа
 * @return Object           Интерфейсный объект
 */
function smoy_rules_action_send_order( $order ) {
  $moysklad = new Moysklad();
  $actionOrder = $moysklad->setCustomerOrder($order);

  /*
    Обработка непредвиденных событий (логика обработчика)
    Ряд событий требуют повторной отправки используя очереди
   */
  
  if ($actionOrder->code == 200) {

    // drupal_set_message($actionOrder->message, 'status', FALSE);
    watchdog('moysklad_rules', $actionOrder->message, NULL, WATCHDOG_INFO, NULL);

    #DONE : На случай если хук с моего склада не вернется, передаем объект 
    #       похожий на хук в очередь для обработки уже по крону...
    $queue = DrupalQueue ::get('surweb_moysklad_check_orders');
    $queue->createQueue();
    $queue->createItem(smoy_create_quasi_hook($actionOrder->meta, "CREATE"));

  } elseif (
      $actionOrder->code == 404 || // Запрошенный ресурс не существует
      $actionOrder->code == 429 || // Превышен лимит количества запросов
      $actionOrder->code == 500 || // При обработке запроса возникла непредвиденная ошибка
      $actionOrder->code == 502 || // Сервис временно недоступен
      $actionOrder->code == 503 || // Сервис временно отключен
      $actionOrder->code == 504    // Превышен таймаут обращения к сервису, повторите попытку позднее
    ) {
      $queue_add    = DrupalQueue::get('surweb_moysklad_add_oreder');
      $queue_add->createQueue();
      $queue_add->createItem($order);
      // drupal_set_message($actionOrder->message, 'error', FALSE);
      watchdog('moysklad_rules', $actionOrder->message, NULL, WATCHDOG_ERROR, NULL);

  }

}





/**
 * Экшен правило на обновление существующего заказа на моемскладе
 * @param  Object $order объект commerce order
 * @return Object        Интерфейсный объект
 */
function smoy_rules_action_update_order( $order ) {
  $moysklad = new Moysklad();
  $actionOrder = $moysklad->putCustomerOrder($order);
  

  if ($actionOrder->code == 200) {
    // если изменения небыли внесены на мой склад но заказ пересохранен в 
    // коммерце необходимо очистить очередь от дубликатов
    smoy_delete_from_queue($actionOrder->meta->href);

    // drupal_set_message($actionOrder->message, 'status', FALSE);
    watchdog('moysklad_rules', $actionOrder->message, NULL, WATCHDOG_INFO, NULL);

    #DONE : На случай если хук с моего склада не вернется, передаем объект 
    #       похожий на хук в очередь для обработки уже по крону...
    $queue = DrupalQueue ::get('surweb_moysklad_check_orders');
    $queue->createQueue();
    $queue->createItem(smoy_create_quasi_hook($actionOrder->meta, "UPDATE"));

  } elseif (
      $actionOrder->code == 404 || // Запрошенный ресурс не существует
      $actionOrder->code == 429 || // Превышен лимит количества запросов
      $actionOrder->code == 500 || // При обработке запроса возникла непредвиденная ошибка
      $actionOrder->code == 502 || // Сервис временно недоступен
      $actionOrder->code == 503 || // Сервис временно отключен
      $actionOrder->code == 504    // Превышен таймаут обращения к сервису, повторите попытку позднее
    ) {
      $queue_add    = DrupalQueue::get('surweb_moysklad_update_oreder');
      $queue_add->createQueue();
      $queue_add->createItem($order);
      // drupal_set_message($actionOrder->message, 'error', FALSE);
      watchdog('moysklad_rules', $actionOrder->message, NULL, WATCHDOG_ERROR, NULL);
  }

}





function smoy_rules_action_delete_order( $order ) {
  $moysklad = new Moysklad();
  $actionOrder = $moysklad->delCustomerOrder($order);
  drupal_set_message($actionOrder->message, 'message', FALSE);

}



/**
 * Условие для правила
 * @param  Object $_order    commerce order
 * @param  string $_discount Значение ключа для поля скидка в форме билинга для пользователя
 * @return bool            TRUE если есть совпадение
 */
function smoy_rules_conditions_checkDiscount( $_order = NULL, $_discount = NULL ) {
  
  if (is_null($_order) || is_null($_discount)) {
    return false;  
  }

  $order_wrapper = entity_metadata_wrapper('commerce_order', $_order);
  $order_discount_name = $order_wrapper->commerce_customer_billing->field_disc->value();

  if ($order_discount_name == $_discount) {
    return true;
  } else {
    return false;
  }

  return false;
}





/**
 * Экшен правило которое можно использовать для динамической проверки остатков на моемскладе
 * @param  [type] $code [description]
 * @return [type]       [description]
 */
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
 * Экшен правило принудительного ценообразования
 * @param  [type] $_order [description]
 * @return [type]         [description]
 */
function smoy_rules_action_refresh_order ( $_order ) {
  commerce_cart_order_refresh($_order);
  return true;
}




/*----------  Старые демки  ----------*/

/** 
 * The action function for rules_example_action_hello_user
 */
function demo_rules_actions_hello_user($account, $text) {
  drupal_set_message(t('Hello @username', array('@username' => $account->name)));
  drupal_set_message($text);
}





/**
 * The action function for rules_example_action_hello_world
 */
function demo_rules_actions_hello_world() {
  drupal_set_message(t('Hello World'));
} 