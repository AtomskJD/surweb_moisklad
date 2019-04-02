<?php 
include_once('moysklad.class.php');


/**
 * Handler for Moysclad::checkConnection
 */
function smoy_checkConnection () {
    $moysklad = new Moysklad();
    $check = $moysklad->getOrganization();
    drupal_set_message($check->message, 'status', FALSE);
}


function smoy_checkCounterparty () {
    $moysklad = new Moysklad();
    $check = $moysklad->getCounterparty( variable_get('moysklad_counterparty', '') );
    drupal_set_message($check->message, 'status', FALSE);
}


function smoy_checkDefaults () {
    $moysklad = new Moysklad();
    $counterparty = $moysklad->getCounterparty( variable_get('moysklad_counterparty', '') );
    $store = $moysklad->getStore( variable_get('moysklad_store', '') );
    $message  = "<strong>Проверка установок поумолчанию МоегоСклада код (" .$counterparty->code. " " .$store->code. ")</strong>";

    drupal_set_message($message ."<br>". $counterparty->message ."<br>". $store->message, 'status', FALSE);
}


function smoy_checkProducts () {
    $moysklad = new Moysklad();
    $product = $moysklad->getProduct( variable_get('moysklad_counterparty', '') );
    $message  = "<strong>Проверка установок поумолчанию МоегоСклада код (" .$counterparty->code. " " .$store->code. ")</strong>";

    drupal_set_message($message ."<br>". $counterparty->message ."<br>". $store->message, 'status', FALSE);
}


function getAllOrders() {
    $moysklad = new Moysklad();
    $orders = $moysklad->getCustomerOrders();
    $message = "Получение всех заказов <br>" . $orders->message;
    drupal_set_message($message, 'status', TRUE);
}



function testOrder() {
    $moysklad = new Moysklad();
    $order = $moysklad->setCustomerOrder();

    return $order;
    // $message = "Создаём тестовый заказ <br>";
    // drupal_set_message($message, 'status', TRUE);
}


function my_module_form_submit_one($form, &$form_state) {
    dpm($form_state);
}

/**
 * HOOKs handlers for form
 */





/**
 * Form handler function -- hooks.page.php
 */
function smoy_deleteHook($form, &$form_state) {
    if ($form_state['clicked_button']['#name'] == 'del_op') {
        $moysklad = new Moysklad();
        $_del = $moysklad->delWebHook($form_state['clicked_button']['#hook_id']);


        if ($_del->code == 200) {
            drupal_set_message($_del->message, 'status', FALSE);
        } else {
            drupal_set_message('Удаление хука не удалось' . $_del->message, 'error', FALSE);
        }
    }
    // $form_state['clicked_button']['#hook_id'];
}





/**
 * Form handler function -- hooks.page.php
 */
function smoy_addHook ($form, &$form_state) {
  if ($form_state['clicked_button']['#name'] == 'add_op') {
    $moysklad = new Moysklad();
    $action = $form_state['values']['action'];
    $entity = strtolower($form_state['values']['entityType']);
    $url    = strtolower($form_state['values']['url']);
    $_add = $moysklad->setWebHook($action, $entity, $url);

    if ($_add->code == 200) {
        drupal_set_message($_add->message, 'status', FALSE);
    } else {
        drupal_set_message('Добавление хука не удалось' . $_add->message, 'error', FALSE);
    }
  }
}





/**
 * функция изменения остатков для страницы отлова хуков
 * @param  string $_sku   SKU в моем складе должно быть уникальным
 * @param  mixed  $_qty   Чаще приходит в формате float - конвертируется в целое
 * @return bool
 */
function smoy_set_qty ($_sku = NULL, $_qty = NULL) {
    if (is_null($_sku) || is_null($_qty)) {
        return false;
    }

    $product = commerce_product_load_by_sku($_sku);
    if ($product) {
        $pro_wrapper = entity_metadata_wrapper('commerce_product', $product);
        if ((int)$pro_wrapper->commerce_stock->value() != (int)$_qty) {
            $pro_wrapper->commerce_stock->set($_qty);
            $pro_wrapper->save();
            return true;
        } 
    } 

    return false;
}





/**
 * QUEUE test FUNCTION's
 */
function smoy_getQueues() {
  $queue_add    = DrupalQueue::get('surweb_moysklad_add_oreder');
  $queue_update = DrupalQueue::get('surweb_moysklad_update_oreder');
  // $queue->createQueue();
  dpm($queue_add->createQueue());
  dpm($queue_update->createQueue());
  // return "<strong>Осталось элементов в очереди: </strong>"  .  $queue->numberOfItems();
}




/**
 * Конвертация имен статусов коммерца в имена моего склада
 * Статус далее отправляется на мой склад
 * @param  string $_commerce_state_name_value   Машинное имя из коммерца
 * @return string                               Имя статуса в формате моегосклада
 */
function smoy_commerce_to_moysklad_state_conv ( $_commerce_state_name_value ) {
  switch ($_commerce_state_name_value) {
    case 'canceled':
      $moysklad_state_name_value = 'Отменен';
      break;
    
    case 'Обработка. Звонок':
      $moysklad_state_name_value = 'Обработка. Звонок';
      break;
    
    case 'sobran':
      $moysklad_state_name_value = 'Собран. Ожидает самовывоза';
      break;
    
    case 'invoice':
      $moysklad_state_name_value = 'Выставлен счет, ждем оплаты';
      break;
    
    case 'Доставка по Челябинску':
      $moysklad_state_name_value = 'Доставка по Челябинску';
      break;
    
    case 'send_russianpost':
      $moysklad_state_name_value = 'Завершен. Отправлен почтой';
      break;
    
     case 'completed':
      $moysklad_state_name_value = 'Завершен';
      break;
    
     case 'Отправлен. РКО':
      $moysklad_state_name_value = 'Отправлен. Оплата при получении';
      break;
    
     case 'Дозаказ':
      $moysklad_state_name_value = 'Ждем поступления для дозаказа';
      break;

     case 'Shipping':
      $moysklad_state_name_value = 'Оплата получена. На отправку';
      break;
    

    default:
      $moysklad_state_name_value = 'Новый';
      break;
  }
  return $moysklad_state_name_value;
}



function smoy_order_status_is_complete ( $_order_status ) {
  if ( ($_order_status == 'completed') ||  ($_order_status == 'send_russianpost') ) {
    return true;
  } else return false;
}





/**
 * Создание виртуального хука
 * в дальнейлем отправляем его как обычный хук на страницу которая ловит
 * хуки от моего склада, 
 * @param  string   $meta     Метаданные объекта заказа в формате моегосклада
 * @param  string   $action   тип событая в формате вебхука CREATE, UPDATE, DELETE
 * @return array              Возвращает хук json_decoded
 */
function smoy_create_quasi_hook ( $meta, $action) {
  $hook = array(
    "quasi_hook" => true,
    "events" => array(
      array("meta" => $meta, "action" => $action ),
    )
  );

  return $hook;
}






/**
 * тестируем http || https
 * @return [type] [description]
 */
function smoy_isSSL() { 
  return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443; 
}





/**
 * Возвращает список задач из очереди
 * *служебная функция
 * @param  string   имя очереди
 * @return array    Вернет массив с задачами
 */
function _smoy_queue_items( $queue_name = 'surweb_moysklad_check_orders') {

    $items = db_query('SELECT data, item_id, name FROM {queue} q WHERE name = :name ORDER BY created, item_id ASC', array(':name' => $queue_name ))->fetchAll();

    if ($items) {
      return $items;
    }

      return FALSE;
}





/**
 * Поиск задачи по href
 * *служебная функция
 * @param  string     строка для сравнения
 * @return array      массив совпадений array[item_id1, item_id_2, ... ]
 */
function _smoy_find_in_queue ( $_href ) {
  if ($_items = _smoy_queue_items()) {
    $find = array();

    foreach ($_items as $item) {
      $data = unserialize($item->data);
      $item_id = $item->item_id;

      if ( $data['events'][0]['meta']->href == $_href ) {
        $find[] = $item_id;
      }
    }

    return $find;
  } else {
    return false;
  }
}





/**
 * Удаляет задание из очереди при совпадении href
 * @param  string       строка для поиска 
 * @return bool         true
 */
function smoy_delete_from_queue ( $_href ) {
  $queue_name = 'surweb_moysklad_check_orders';
  $items = _smoy_find_in_queue($_href);

  if (empty($items)) {
    return false;
  }

  foreach ($items as $item_id) {
    db_query("DELETE FROM {queue} WHERE name = :name AND item_id = :item_id", array(':name' => $queue_name, ':item_id' => $item_id)); 
  }

  return true;

}