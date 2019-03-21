<?php 
include_once('moysklad.class.php');
include_once('functions.inc.php');


function smoy_test2_page() {
  $moysklad = new Moysklad();

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = file_get_contents("php://input");    

    if ($json = json_decode($content)) {
      $operationURL = $json->events[0]->meta->href;
      $document = $moysklad->getRequestData($operationURL);
      
      if ($document->code == 200) {
        $change = $moysklad->getOrderStockReport($document->data->id);
        if ($change->code == 200) {
// file_put_contents('post.txt', "line>> " . __LINE__ . "\n", FILE_APPEND);
          $_positions = $change->data->positions;
          foreach ($_positions as $position) {
            $__name = $position->name;
            $__qty  = $position->quantity;
            $meta = $position->meta;
            if ($meta->type == 'product') {
              $product = $moysklad->getRequestData( $meta->href );
              
              if ($product->code == 200) {
                $_product = $product->data;
                $__sku = $_product->code;

                // Изменение остатков
                if ( smoy_set_qty($__sku, $__qty) ) {
                  file_put_contents('post.txt', $__sku 
                  . " " . $__name 
                  . " " . $__qty . " [CHAN] " . "\n", FILE_APPEND);
                } else {
                  file_put_contents('post.txt', $__sku 
                  . " " . $__name 
                  . " " . $__qty . " [FAIL] " . "\n", FILE_APPEND);
                }

              } else {file_put_contents('post.txt', "product " . $product->code . "\n", FILE_APPEND);}

            }
          }
        } else {file_put_contents('post.txt', "change " . $change->code . "\n", FILE_APPEND);}
      } else {file_put_contents('post.txt', "document " . $document->code . "\n", FILE_APPEND);}
      
    }
      
  }



//  while (!feof($webhook)) {
//     $webhookContent .= fread($webhook, 4096);
// }
// fclose($webhook);
 // file_put_contents('req.txt', $webhookContent);
}






function smoy_test_page() {
  // print_r(my_module_default_rules_configuration());
  $moysklad = new Moysklad();
  $check = $moysklad->getOrganization();
  // $_debugConnection = $moysklad->debugConnection("https://online.moysklad.ru/api/remap/1.1/entity/organization");
  // dpm($_debugConnection);
  // dpm(__DIR__);
  // dpm(drupal_get_path('module', 'surweb_moysklad'));
/*  drupal_set_message('Authorization: Basic '
        . base64_encode(variable_get('moysklad_login')
        .":"
        . variable_get('moysklad_pass')), 'status', FALSE);*/

  $line_item = 50268;
  $line_item_wrapper = entity_metadata_wrapper('commerce_line_item', $line_item);
  // $line_item_wrapper->commerce_product = 200.0;
  // kpr($line_item_wrapper->commerce_product->value());
  // $product = commerce_product_load(23);
  // smoy_set_qty("007006", 12);
  
  // $pro_wrapper = entity_metadata_wrapper('commerce_product', "007006");
  // $pro_wrapper->commerce_stock->set(50);
  // $pro_wrapper->save();
    
  // kpr($line_item_wrapper->commerce_product->value());
  // kpr($pro_wrapper->commerce_stock->value());

  /**
   * items
   ** sku
   ** title
   ** quantity
   */
  $product_from_LI  = "<strong>Здачения через commerce_line_item (line_item = 50268): </strong><br>";
  $product_from_LI .= "LI_label: " 
    . ($line_item_wrapper->line_item_label->value()) . " ";
  $product_from_LI .= "quantity: " 
    . ($line_item_wrapper->quantity->value()) . " ";
  $product_from_LI .= "sku: " 
    . ($line_item_wrapper->commerce_product->sku->value()) . " ";
  $product_from_LI .= "title: " 
    . ($line_item_wrapper->commerce_product->title->value());


  /**
   * order
   ** 
   */
  $order_id = 36600;
  $order = commerce_order_load($order_id);
  $wrapper = entity_metadata_wrapper('commerce_order', $order);

  dpm($wrapper->status->value(), "STATUS");
  dpm(smoy_commerce_to_moysklad_state_conv('canceled'), "STATUS CONV");
  dpm(smoy_commerce_to_moysklad_state_conv('Отправлен. РКО'), "STATUS CONV");
  dpm(smoy_commerce_to_moysklad_state_conv('checkout_complete'), "STATUS CONV");

  // kpr($moysklad->getStates('customerorder', smoy_commerce_to_moysklad_state_conv('checkout_complete')));
  // foreach ($wrapper->commerce_line_items as $key => $value) {
  //  dpm($wrapper->commerce_line_items[$key]->value());
  //  // dpm($value->value());
  // }

  for ($i=0; $i < count($wrapper->commerce_line_items->value()); $i++) {
    $LI = $wrapper->commerce_line_items[$i];
    // dpm ($LI->quantity->value());
    // dpm ($LI->commerce_product->sku->value());
    // dpm ($LI->commerce_product->title->value());
  }
  // kpr($wrapper->commerce_customer_billing->value());
  $user_form_from_order = "<strong>Здачения через commerce_order (order_id = 35118): </strong><br>";
  $user_form_from_order .= "field_phone: " 
  . $wrapper->commerce_customer_billing->field_phone->value() . "; ";
  $user_form_from_order .= "field_city: " 
  . $wrapper->commerce_customer_billing->field_city->value() . "; ";
  $user_form_from_order .= "field_street: " 
  . $wrapper->commerce_customer_billing->field_street->value() . "; ";
  $user_form_from_order .= "field_comment: " 
  . $wrapper->commerce_customer_billing->field_comment->value()['value'] . "; ";
  $user_form_from_order .= "field_fio: " 
  . $wrapper->commerce_customer_billing->field_fio->value() . "; ";
  $user_form_from_order .= "field_ship: " 
  . $wrapper->commerce_customer_billing->field_ship->value() . "; ";
  $user_form_from_order .= "field_postindex: " 
  . $wrapper->commerce_customer_billing->field_postindex->value() . "; ";
  

  /**
   * Вариант номер 3 из переменной ордер
   */
  $order = commerce_order_load($order_id);
  $positions = array(
    "quantity" => 2.0,
    "price" => 2000,
  );


  // kpr($moysklad->setCustomerOrder($order));

  // $order = entity_metadata_wrapper('commerce_order', $order_id);
  // kpr($order->commerce_line_items[0]->commerce_product->title->value());

  // kpr($wrapper->commerce_customer_billing->field_fio->value());
  // $address = $wrapper->commerce_customer_billing->commerce_customer_address->value();
  getAllOrders();
  // dpm (testOrder());
  $queue_add    = DrupalQueue::get('surweb_moysklad_add_oreder');
  $queue_add->createQueue();
  $queue_add->createItem($order);
  // dpm($queue_add->numberOfItems());
  // watchdog('test', 'message', array(), WATCHDOG_ERROR, 'link');

  // smoy_getQueues();
  // $audit  = $moysklad->getCustomerOrderAudit( "f09604b6-3b2a-11e9-9109-f8fc001606a6", 5 );

// $audit->data[0]->diff->positions[0]->oldValue->assortment->meta->href;
  // kpr($audit->meta);
    $oldValues = array();
    $newValues = array();
  // foreach ($audit->data as $row) {
  //   // dpm($row->diff->positions);

  //   foreach ($row->diff->positions as $position) {
  //     // dpm($position);
  //     if ( isset($position->oldValue) ) {
  //       $oldValues[$position->oldValue->assortment->meta->href] = $position->oldValue->assortment->meta->href;
  //     }
  //     if ( isset($position->newValue) ) {
  //       $newValues[$position->newValue->assortment->meta->href] = $position->newValue->assortment->meta->href;
  //     }
  //   }
  // }

  // OLD values "deleted"
  // dpm($oldValues);
  // NEW values "added"
  // dpm($newValues);

  // foreach ($oldValues as $url) {
  //   $audit_request = $moysklad->getRequestData( $url );
  //   dpm($audit_request->data->id, "ID_");
  //   dpm($audit_request->data->code, "SKU");
  //   $_stock = $moysklad->getProductStock($audit_request->data->id);
  //   dpm($_stock->data->stock, "stock");
  // }


    dpm ($oo = $moysklad->getCustomerOrders(39222), '->getCustomerOrders(39221)');

    if (isset($oo->data[0]->demands)) {
      $demands = $oo->data[0]->demands;
      foreach ($demands as $demand) {
        $_demandResp = $moysklad->getRequestData($demand->meta->href);
        dpm($_demandResp);
        if ( $_demandResp->code == 200 ) {
          dpm($moysklad->delDemand( $_demandResp->data->id ));
        }
      }
    }
    // dpm($moysklad->delDemand( $oo->data[0]->demands ));
    // dpm(isset($oo->demands));



  return array('#markup' => 
    '<h2>DEBUG</h2>'
    . '<pre>'
    . variable_get('moysklad_login', 'user@name') . "\n"
    . variable_get('moysklad_pass', '') . "\n"
    . $check->message . " \n"
    . $product_from_LI . " \n"
    . $user_form_from_order . " \n"
    // . json_encode($_debugConnection, JSON_PRETTY_PRINT). " \n"
    . '<pre>'

  );
}
