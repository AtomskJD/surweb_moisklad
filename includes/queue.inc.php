<?php 

/**
 * Создаем две очереди и привязываем обработчики
 */
function surweb_moysklad_cron_queue_info() {
  $queues = array();
  $queues['surweb_moysklad_add_oreder'] = array(
    'worker callback' => 'smoy_rules_action_send_order', //function to call for each item
    'time' => 60, //seconds to spend working on the queue
  );

  $queues['surweb_moysklad_update_oreder'] = array(
    'worker callback' => 'smoy_rules_action_update_order', //function to call for each item
    'time' => 60, //seconds to spend working on the queue
  );
  return $queues;
}

/**
 * Обработчики очереди
 */
function addOrderQueue($data) {
  
}
function updateOrderQueue($data) {}