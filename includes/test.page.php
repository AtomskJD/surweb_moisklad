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

  ////////////////////
  // pagenator TEST //
  ////////////////////


$_sku = '008031';

      $nid = 2253;
      $node = node_load($nid);
      $test = node_save($node);

        dpm($node);



  // dpm(create_commerce_product(array('title' => test, 'price' => 20000, 'sku' => 'testststst')));


  return array('#markup' => 
    '<h2>DEBUG</h2>'
    . '<pre>'
    . variable_get('moysklad_login', 'user@name') . "\n"
    . variable_get('moysklad_pass', '') . "\n"

    . '<pre>'

  );
}
