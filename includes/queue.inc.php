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

  $queues['surweb_moysklad_check_orders'] = array(
    'worker callback' => 'smoy_send_quasi_hook', //function to call for each item
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


function smoy_send_quasi_hook($data) {
  $prot =  "http://";
  if (smoy_isSSL()) {
    $prot =  "https://";
  }

  
  $url =  $prot .  $_SERVER['HTTP_HOST'] . "/smoy-sync";
  $headers = array('Content-Type:application/json');
  $body = json_encode($data);
  $process = curl_init($url);
      curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_POST, 1);
      curl_setopt($process, CURLOPT_POSTFIELDS, $body);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, FALSE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
    curl_close($process);
}