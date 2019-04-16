<?php 
include_once('moysklad.class.php');
include_once('functions.inc.php');

/*----------  CATCHER ONE Handler  ----------*/
/*
  Данный запрос работает со следующими типами документов (getOrderStockReport):
- Отгрузка
- Заказ покупателя
- Розничная продажа
- Счёт покупателю
 * URL: /smoy-sync
 */
function smoy_catcherOne_page() {
  $moysklad = new Moysklad();

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = file_get_contents("php://input");
    $message = "[webhook] - " . $content;
                  watchdog('moysklad_hook', $message, NULL, WATCHDOG_INFO, "/smoy-sync");

    if ($json = json_decode($content)) {
      $operationURL = $json->events[0]->meta->href;

    /*
      Хук с моегосклада приходит быстрее чем проходит постановка в очередь
     */ sleep(2);
    /**
     * кодга приходит хук или квази-хук проверим очередь и если есть ссылка
     * на такойже объект то удалим ее из очереди
     */ smoy_delete_from_queue($operationURL);  
    

      $document = $moysklad->getRequestData($operationURL);
      
      if ($document->code == 200) {
        $report = $moysklad->getOrderStockReport( $document->data->id );
        $audit  = $moysklad->getCustomerOrderAudit( $document->data->id, 5 );
        // $audit->data[0]->diff->positions[0]->oldValue->assortment->meta->href;
        /*
          Обработка остатков из аудита
         */
        if ($audit->code == 200) {

        
          $oldValues = array();
          $newValues = array();
          foreach ($audit->data as $row) {
            // dpm($row->diff->positions);

            foreach ($row->diff->positions as $position) {
              // dpm($position);
              if ( isset($position->oldValue) ) {
                $oldValues[$position->oldValue->assortment->meta->href] = $position->oldValue->assortment->meta->href;
              }
              if ( isset($position->newValue) ) {
                $newValues[$position->newValue->assortment->meta->href] = $position->newValue->assortment->meta->href;
              }
            }
          }
          foreach ($oldValues as $url) {
            $audit_request = $moysklad->getRequestData( $url );
            $__sku  = $audit_request->data->code;
            $__name = $audit_request->data->code;
            $_audit_request_stock = $moysklad->getProductStock($audit_request->data->id);
            $__qty = $_audit_request_stock->data->quantity;
            
            if ( smoy_set_qty($__sku, $__qty) ) {
                  $message = "[AUDIT CHANGE] - " . $__sku . " " . $__name . " " . $__qty;
                  watchdog('moysklad_hook', $message, NULL, WATCHDOG_NOTICE, "/smoy-sync");
            } else {
              $message = "[AUDIT SIMILAR] - " . $__sku . " " . $__name . " " . $__qty;
              watchdog('moysklad_hook', $message, NULL, WATCHDOG_ALERT, "/smoy-sync");
            }
          }

        } else {
          watchdog('moysklad_hook', "audit " . $audit->code, NULL, WATCHDOG_ALERT, "/smoy-sync");
        }
        /* *** */

        /*
          обработка остатков из заказа
         */
        if ($report->code == 200) {
          $_positions = $report->data->positions;

          foreach ($_positions as $position) {
            $__name = $position->name;
            $__qty  = $position->quantity;
            $meta   = $position->meta;
            if ($meta->type == 'product') {
              $product = $moysklad->getRequestData( $meta->href );
              
              if ($product->code == 200) {
                $_product = $product->data;
                $__sku = $_product->code;

                // Изменение остатков
                if ( smoy_set_qty($__sku, $__qty) ) {
                  $message = "[REPORT CHANGE] - " . $__sku . " " . $__name . " " . $__qty;
                  watchdog('moysklad_hook', $message, NULL, WATCHDOG_NOTICE, "/smoy-sync");
                } else {
                  $message = "[REPORT SIMILAR] - " . $__sku . " " . $__name . " " . $__qty;
                  watchdog('moysklad_hook', $message, NULL, WATCHDOG_ALERT, "/smoy-sync");
                }

              } else {
                file_put_contents('post.txt', "product " . $product->code . "\n", FILE_APPEND);
              }

            }
          }
        } else {
          watchdog('moysklad_hook', "change " . $report->code, NULL, WATCHDOG_ALERT, "/smoy-sync");
          file_put_contents('post.txt', "change " . $report->code . "\n", FILE_APPEND);
        }
      } else {
        watchdog('moysklad_hook', "document " . $document->code, NULL, WATCHDOG_ALERT, "/smoy-sync");
        }
      
    }
      
  }


} /*----------  END  ----------*/






/*----------  CATCHER TWO Handler  ----------*/
/**
 * Обработка хуков от документов без поддержки report/stock/byoperation
 * URL: /smoy-sync-type-2
 */
function smoy_catcherTwo_page() {
  $moysklad = new Moysklad();

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = file_get_contents("php://input");
    $message = "[webhook] - " . $content;
              watchdog('moysklad_hook', $message, NULL, WATCHDOG_INFO, "/smoy-sync");

    if ( $json = json_decode($content) ) {
      $operationURL = $json->events[0]->meta->href;
      $entityName   = $json->events[0]->meta->type; // supply | loss | enter

      $document = $moysklad->getRequestData($operationURL);

      if ($document->code == 200) {
        /*=================================
        =            positions            =
        =================================*/
        // лимит моегосклада 100 строк вывода на запрос
        // получать значения будем из запроса первого
        $_offset = 0;
        $_size   = 0;
        $_limit  = 0;

        do {
          $_positions = $moysklad->getRequestData( $document->data->positions->meta->href . "?offset=" . $_offset );

          if ($_positions->code == 200) {

            $_offset = $_positions->meta->offset;
            $_size   = $_positions->meta->size;
            $_limit  = $_positions->meta->limit; 

            foreach ($_positions->data->rows as $position) {
              if ($position->assortment->meta->type == 'product') {
                $_product = $moysklad->getRequestData($position->assortment->meta->href);
                
                if ($_product->code == 200) {
                  // __name
                  // __qty
                  // __sku
                  // 
                  // __price
                  // __zakup

                  $__name = $_product->data->name;
                  $__sku  =  $_product->data->code;

                  #DONE: продукт есть остатков нет... getProductStock вернет 201

                  $_product_stock = $moysklad->getProductStock( $_product->data->id);

                  if ($_product_stock->code == 200 ) {
                    $__qty = $_product_stock->data->quantity;
                    $__price = $_product_stock->data->salePrice;
                    $__zakup = $_product_stock->data->price;

                    // Изменение остатков если есть такое изменение
                    if ( smoy_set_qty($__sku, $__qty) ) {
                      $message = "[QTY CHANGE] - " . $__sku . " - " . $__name . ": " . $__qty;
                      watchdog('moysklad_hook', $message, NULL, WATCHDOG_INFO, "/smoy-sync-type-2");
                    } 

                    // Изменение цены продажи если есть такое изменение
                    if ( smoy_set_price($__sku, $__price)) {
                      $message = "[PRICE CHANGE] - " . $__sku . " - " . $__name . ": " . (int)$__price / 100 . " руб. ";
                      watchdog('moysklad_hook', $message, NULL, WATCHDOG_INFO, "/smoy-sync-type-2");
                    }
                    // Изменение цены закупа если есть такое изменение
                    if ( smoy_set_zakup_price($__sku, $__zakup)) {
                      $message = "[ZAKUP CHANGE] - " . $__sku . " - " . $__name . ": " . (int)$__zakup / 100 . " руб. ";
                      watchdog('moysklad_hook', $message, NULL, WATCHDOG_INFO, "/smoy-sync-type-2");
                    }

                  } // $_product_stock == 200
                  elseif ($_product_stock->code == 201) {
                    $__qty = 0;

                    if ( smoy_set_qty($__sku, $__qty) ) {
                      $message = "[QTY ZERO] - " . $__sku . " - " . $__name;
                      watchdog('moysklad_hook', $message, NULL, WATCHDOG_INFO, "/smoy-sync-type-2");
                    }
                  } // $_product_stock == 201
                  else { 
                  watchdog('moysklad_hook', "_product_stock " . json_encode($_product_stock), NULL, WATCHDOG_ALERT, "/smoy-sync-type-2");
                  }
                } // $_product == 200
                else { 
                  watchdog('moysklad_hook', "_product " . $_product->code, NULL, WATCHDOG_ALERT, "/smoy-sync-type-2");
                }
              }
            }
            

            $_offset += $_limit; //loop iterator
          } // $_positions == 200 
          else {
            watchdog('moysklad_hook', "_positions " . $_positions->code, NULL, WATCHDOG_ALERT, "/smoy-sync-type-2");
            break;
          }
        } while ($_offset <= $_size);
        /*=====  End of positions  ======*/
        
        /**
         *
         * получение позиций при их удалении из документа
         * ищем уделенные товары oldValues 
         *
         */
        
        /*============================================
        =            positions from Audit            =
        ============================================*/
        $_audit  = $moysklad->getCustomAudit( $entityName, $document->data->id, 5 );
        /*
          Обработка остатков из аудита
         */
        if ($_audit->code == 200) {

        
          $oldValues = array();
          $newValues = array();
          foreach ($_audit->data as $row) {
            // dpm($row->diff->positions);

            foreach ($row->diff->positions as $position) {
              // dpm($position);
              if ( isset($position->oldValue) ) {
                $oldValues[$position->oldValue->assortment->meta->href] = $position->oldValue->assortment->meta->href;
              }
              if ( isset($position->newValue) ) {
                $newValues[$position->newValue->assortment->meta->href] = $position->newValue->assortment->meta->href;
              }
            }
          }
          foreach ($oldValues as $url) {
            $audit_request = $moysklad->getRequestData( $url );
            $__sku  = $audit_request->data->code;
            $__name = $audit_request->data->code;
            $_audit_request_stock = $moysklad->getProductStock($audit_request->data->id);

            $__qty = $_audit_request_stock->data->quantity;
            $__price = $_audit_request_stock->data->salePrice;
            $__zakup = $_audit_request_stock->data->price;
            
            if ( smoy_set_qty($__sku, $__qty) ) {
              $message = "[QTY AUDIT] - " . $__sku . " " . $__name . " " . $__qty;
              watchdog('moysklad_hook', $message, NULL, WATCHDOG_INFO, "/smoy-sync-type-2");
            }

            // Изменение цены продажи если есть такое изменение
            if ( smoy_set_price($__sku, $__price)) {
              $message = "[PRICE AUDIT] - " . $__sku . " - " . $__name . ": " . (int)$__price / 100 . " руб.";
              watchdog('moysklad_hook', $message, NULL, WATCHDOG_INFO, "/smoy-sync-type-2");
            }
            // Изменение цены закупа если есть такое изменение
            if ( smoy_set_zakup_price($__sku, $__zakup)) {
              $message = "[ZAKUP AUDIT] - " . $__sku . " - " . $__name . ": " . (int)$__zakup / 100 . " руб.";
              watchdog('moysklad_hook', $message, NULL, WATCHDOG_INFO, "/smoy-sync-type-2");
            }


          }

        } else {
          watchdog('moysklad_hook', "audit " . $_audit->code, NULL, WATCHDOG_ALERT, "/smoy-sync");
        }
        /* *** */
        
        /*=====  End of positions from Audit  ======*/
        
      } else {
        watchdog('moysklad_hook', "document " . $document->code, NULL, WATCHDOG_ALERT, "/smoy-sync-type-2");
        }
      
    }
      
  }
} /*----------  END  ----------*/






/*----------  Обновление информации о товаре && Создание товара  ----------*/
/**
 * Для обработки веб хуков на товар
 * URL: /smoy-sync-product
 */
function smoy_catcher_page__product() {
   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = file_get_contents("php://input");
    $message = "[webhook] - " . $content;
    watchdog('moysklad_hook', $message, NULL, WATCHDOG_INFO, "/smoy-sync-product");

    if ($json = json_decode($content)) {
      $moysklad = new Moysklad();

      $operationURL = $json->events[0]->meta->href;
      $entityName   = $json->events[0]->meta->type; // supply | loss | enter
      $actionType   = $json->events[0]->action;

      $message = "[webhook__product] - " . $content;
      watchdog('moysklad_hook', $message, NULL, WATCHDOG_INFO, "/smoy-sync-product");
      
      $product = $moysklad->getRequestData($operationURL);

      if ($product->code == 200) {
        // в копейках
        // _salePrice $product->data->salePrices[0]->value 
        // _buyPrice  buyPrice->value Изменять не будем -- это рекомендованая цена
        // $product->data->archived
        // $product->data->code
        // $product->data->description = может не быть
        // $product->data->group->meta = может не быть

        $__sku = $product->data->code;
        $__price = $product->data->salePrices[0]->value;


      }


    /*
      Хук с моегосклада приходит быстрее чем проходит постановка в очередь
     */ sleep(2);
    }
  }
}
/*----------  END  ----------*/
