<?php 

// dpm('queque inc');

function surweb_moysklad_cron_queue_info() {
  $queues = array();
  $queues['surweb_moysklad_goods'] = array(
    'worker callback' => 'queueWorker_goods', //function to call for each item
    'time' => 60, //seconds to spend working on the queue
  );
  return $queues;
}


function queueWorker_goods($data) {
	
  $goodsConnector = new GoodsReportConnector('all');
  $ts = microtime(true);
  
  $log_array = variable_get('moysklad_queue_log', '');

  foreach ($goodsConnector->getItems($data['offset'], $data['limit']) as $item) {

    $local_good = new Goods( $goodsConnector->getModel($item) );

    if ($local_good->exists()) {

      if (!$local_good->is_new()) {
        // проверяет совпадение цены договор интерфейса - внутренний
        if (($local_good->getSell_price()) != ($goodsConnector->getSell_price($item))) {
          $local_good->setSell_price(($goodsConnector->getSell_price($item)));
        }
        // проверяет количество на складе 
        if ($local_good->getQuantity() != $goodsConnector->getQuantity($item)) {
          $local_good->setQuantity($goodsConnector->getQuantity($item));

          $log_array[] = "[" . $local_good->getModel()."]"." изменил количество на: ".$goodsConnector->getQuantity($item);
          // file_put_contents('logs/queue_log.txt', $local_good->getModel()."изменил количество на".$goodsConnector->getQuantity($item)."\r\n", FILE_APPEND);
        }
        // проверяет совпадение заголовков
        if ($local_good->getName() != $goodsConnector->getName($item)) {
          $local_good->setName($goodsConnector->getName($item));
        }

        ///////////////////////////////////////////////////
        // удаляю существующий nid из списка на очистку  //
        ///////////////////////////////////////////////////
        $node_clean_manager = variable_get('moysklad_clean_manager', NULL);
        if (!is_null($node_clean_manager)) {
          unset($node_clean_manager[$local_good->getNid()]);

          variable_set('moysklad_clean_manager', $node_clean_manager);
        }


      } else {
        // создание нового товара при изначальном не совпадении sku
        $nid = $local_good->newItem($goodsConnector->getModel($item));
        $local_good->setName($goodsConnector->getName($item));
        $local_good->setSell_price($goodsConnector->getSell_price($item));
        $local_good->setQuantity($goodsConnector->getquantity($item));


        $log_array[] = "[".$goodsConnector->getModel($item)."]"."Создан новый товар: ".$nid;
        // file_put_contents('logs/queue_log.txt', "Создан новый товар : ".$nid."\r\n", FILE_APPEND);
      }
    }

  } // end foreach()

        variable_set('moysklad_queue_log', $log_array);
  

	variable_set('last_worker_info', "<strong>Выполнение очереди: </strong>" . date('d/m  H:i:s') . " Время выполнения: " . (microtime(true) - $ts) . " queried offset " . $data['offset']);
}

	