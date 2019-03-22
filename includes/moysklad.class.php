<?php 
/**
 * класс для работы с моим складом
 */
class Moysklad
{
  private $path;
  private $headers;
  private $url = 'https://online.moysklad.ru/api/remap/1.1';
	
	function __construct()
	{
		$this->headers = array(
        'Content-Type:application/json',
        'Authorization: Basic '
        . base64_encode(variable_get('moysklad_login')
        .":"
        . variable_get('moysklad_pass') ) // <---
    );
    $this->path = drupal_get_path('module', 'surweb_moysklad');
	}

  private function checkError($_process, $return) {
    if (condition) {
      # code...
    }

    return false;
  }





  public function debugConnection ( $_url ) {

    // $process = curl_init('https://online.moysklad.ru/api/remap/1.1/entity/organization');

    $path = $_url;
    // $process = curl_init($this->url . $path);
    $process = curl_init($_url);

      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 10);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $_url);
      } else {
        $code     = $_resp_code;
        $message  = "Соединение с сервером МоегоСклада установлено <br>\n";
        $message .= "Данные клиента: $name ($mail, $phone)";
      }

      
    $data = array(
      'CURLINFO_RESPONSE_CODE' => curl_getinfo($process, CURLINFO_RESPONSE_CODE),
      'CURLINFO_CONNECT_TIME'  => curl_getinfo($process, CURLINFO_CONNECT_TIME),
      'CURLINFO_TOTAL_TIME'    => curl_getinfo($process, CURLINFO_TOTAL_TIME),
      'CURLINFO_OS_ERRNO'      => curl_getinfo($process, CURLINFO_OS_ERRNO),
    );

    curl_close($process);

    return (object)array('code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
  }





  /*
    Проверка соединения с сервером и организацией
   */
	public function getOrganization () {

    // $process = curl_init('https://online.moysklad.ru/api/remap/1.1/entity/organization');

    $path = '/entity/organization';
    $process = curl_init($this->url . $path);

      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      // curl_setopt($process, CURLOPT_POST, 1);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path);
      } else {
        $code     = $_resp_code;
	      $name  		= json_decode($return)->rows[0]->name;
	      $mail  		= json_decode($return)->rows[0]->email;
        $phone    = json_decode($return)->rows[0]->phone;
	      $meta 		= json_decode($return)->rows[0]->meta;

        $message 	= "Соединение с сервером МоегоСклада установлено <br>\n";
        $message .= "Данные клиента: $name ($mail, $phone)";
      }
    curl_close($process);

    return (object)array('code'=>$code, 'message'=>$message, 'meta' => $meta);
	}





  /**
   * @param  $email =  поле фильтр для поиска 
   * @return Стандартный массив -> объект
   */
  public function getCounterparty( $_email = NULL ) {
    if (is_null($_email)) {
      $code = 400;
      $message = 'Не указан email';
      return (object)array('code'=>$code, 'message'=>$message, 'meta' => '');
    }

    $path = '/entity/counterparty';
    $query   = '?filter=email=' . $_email;

    $process = curl_init($this->url . $path . $query);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {
        $code     = $_resp_code;

        $name     = json_decode($return)->rows[0]->name;
        $mail     = json_decode($return)->rows[0]->email;
        $phone    = json_decode($return)->rows[0]->phone;

        $meta     = json_decode($return)->rows[0]->meta;

        $message = "Данные контрагента: $name ($mail, $phone)";
      }
    curl_close($process);

    return (object)array('code'=>$code, 'message'=>$message, 'meta' => $meta);
  }





  /**
   * @param  $externalCode =  поле фильтр для поиска 
   * @return Стандартный массив -> объект
   */
  public function getStore( $_externalCode = NULL ) {
    if (is_null($_externalCode)) {
      $code = 400;
      $message = 'Не указан externalCode';
      return (object)array('code'=>$code, 'message'=>$message, 'meta' => '');
    }

    $path = '/entity/store';
    $query   = '?filter=externalCode=' . $_externalCode;

    $process = curl_init($this->url . $path . $query);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {
        $code     = $_resp_code;
        
        $name     = json_decode($return)->rows[0]->name;
        $address  = json_decode($return)->rows[0]->address;

        $meta     = json_decode($return)->rows[0]->meta;

        $message  = "Данные склада: $name ($address)";
      }
    curl_close($process);

    return (object)array('code'=>$code, 'message'=>$message, 'meta' => $meta);
  }




  /**
   * Поиск статусов для типа сущности
   * @param  $_entity =  поле фильтр для поиска 
   * @return Стандартный массив -> объект
   */
  public function getStates( $_entity = 'customerorder', $_search = NULL ) {

    $path = '/entity/' . $_entity . '/metadata';

    $process = curl_init($this->url . $path);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {
        $code     = $_resp_code;
        
        $meta     = json_decode($return)->meta;
        $data     = json_decode($return)->states;
        $message  = "Данные статусов";
      }
    curl_close($process);

    if (!is_null($_search)) {
      foreach ($data as $state) {
        if ($state->name == $_search) {
          $data = $state;
        }
      }
    }

    return (object)array('code'=>$code, 'message'=>$message, 'data' => $data, 'meta' => $meta);
  }





  /**
   * @param  $code =  поле фильтр для поиска 
   * @return Стандартный массив -> объект
   */
  public function getProduct( $_code = NULL ) {
    if (is_null($_code)) {
      $code = 400;
      $message = 'Не указан code';
      return (object)array('code'=>$code, 'message'=>$message, 'meta' => '');
    }

    $path = '/entity/product';
    $query   = '?filter=code=' . $_code;

    $process = curl_init($this->url . $path . $query);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);

      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {

        if (empty(json_decode($return)->rows)) {
          $code     = 201;
          $message  = "Данные товара. $_code НЕ найдены!";
          $meta     = '';
          $data     = '';
          
        } else {

          $code     = $_resp_code;
          $name     = json_decode($return)->rows[0]->name;
          $article  = json_decode($return)->rows[0]->article;
          $price    = json_decode($return)->rows[0]->salePrices[0]->value;
          $data     = json_decode($return)->rows[0];
          $meta     = json_decode($return)->rows[0]->meta;
          $message  = "Данные товара. name: $name ($article) price: $price";

        }
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
  }





  /**
   * @param  $_id =  поле фильтр для поиска по ИД товара
   * @return Стандартный массив -> объект
   */
  public function getProductStock( $_id = NULL ) {
    if (is_null($_id)) {
      $code = 400;
      $message = 'Не указан code';
      return (object)array('code'=>$code, 'message'=>$message, 'meta' => '');
    }

    $path = '/report/stock/all';
    $query   = '?product.id=' . $_id;

    $process = curl_init($this->url . $path . $query);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {
        
        if (empty(json_decode($return)->rows)) {
          
          $code     = 201;
          $message  = "Данные товара Ид: $_id. <strong>НЕ найдены!</strong>";
          $meta     = '';
          $data     = '';
          
        } else {
          
          $code     = $_resp_code;
          $meta     = json_decode($return)->rows[0]->meta;
          $name     = json_decode($return)->rows[0]->name;
          $stock    = json_decode($return)->rows[0]->stock;
          $reserve  = json_decode($return)->rows[0]->reserve;
          $sku      = json_decode($return)->rows[0]->code;
          $data     = json_decode($return)->rows[0];
          $message  = "Остатки товара. ($sku)name: $name stock: $stock ($reserve reserved)";

        }
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
  }






  /**
   * Запрос аудита по id документа (customerorder)
   * @param  $_id =  поле фильтр для поиска по ИД товара
   * @return Стандартный массив -> объект
   */
  public function getCustomAudit ( $_entity = NULL, $_operationId = NULL, $_limit = 15 ) {
    if ( (is_null($_operationId)) || (is_null($_entity)) ) {
      $code = 400;
      $message = '_operationId';
      return (object)array('code'=>$code, 'message'=>$message, 'meta' => '');
    }

    $path = '/entity/'.$_entity.'/';
    $query   = $_operationId . '/audit';
    $filters = "?limit=" . $_limit;

    $process = curl_init($this->url . $path . $query . $filters);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);

      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {
        
        if (empty(json_decode($return)->rows)) {
          
          $code     = 201;
          $message  = "Данные операции Ид: $_operationId. <strong>НЕ найдены!</strong>";
          $meta     = '';
          $data     = '';
          
        } else {
          
          $code     = $_resp_code;
          $meta     = json_decode($return)->meta;
          $data     = json_decode($return)->rows;
          $message  = "Аудит получен";

        }
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
}




  /**
   * Запрос аудита по id документа (customerorder)
   * @param  $_id =  поле фильтр для поиска по ИД товара
   * @return Стандартный массив -> объект
   */
  public function getCustomerOrderAudit ( $_operationId = NULL, $_limit = 15 ) {
    if (is_null($_operationId)) {
      $code = 400;
      $message = '_operationId';
      return (object)array('code'=>$code, 'message'=>$message, 'meta' => '');
    }

    $path = '/entity/customerorder/';
    $query   = $_operationId . '/audit';
    $filters = "?limit=" . $_limit;

    $process = curl_init($this->url . $path . $query . $filters);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);

      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {
        
        if (empty(json_decode($return)->rows)) {
          
          $code     = 201;
          $message  = "Данные операции Ид: $_operationId. <strong>НЕ найдены!</strong>";
          $meta     = '';
          $data     = '';
          
        } else {
          
          $code     = $_resp_code;
          $meta     = json_decode($return)->meta;
          $data     = json_decode($return)->rows;
          $message  = "Аудит заказа получен";

        }
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
}




  /**
   * Запрос позиций и остатков по id документа (customerorder)
   * @param  $_id =  поле фильтр для поиска по ИД товара
   * @return Стандартный массив -> объект
   */
  public function getOrderStockReport( $_operationId = NULL ) {
    if (is_null($_operationId)) {
      $code = 400;
      $message = 'Не указан code';
      return (object)array('code'=>$code, 'message'=>$message, 'meta' => '');
    }

    $path = '/report/stock/byoperation';
    $query   = '?operation.id=' . $_operationId;

    $process = curl_init($this->url . $path . $query);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);

      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {
        
        if (empty(json_decode($return)->rows)) {
          
          $code     = 201;
          $message  = "Данные операции Ид: $_operationId. <strong>НЕ найдены!</strong>";
          $meta     = '';
          $data     = '';
          
        } else {
          
          $code     = $_resp_code;
          $meta     = json_decode($return)->meta;
          $size     = json_decode($return)->rows[0]->positions;
          $data     = json_decode($return)->rows[0];
          $message  = "Остатки товара ( ".count($size)."шт. ).";

        }
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
  }


  /**
   * отправляет _GET_ запрос на сервер
   * @param  $_URL = URL полученый из meta или сформированый
   * @return Стандартный массив -> объект
   */
  public function getRequestData( $_URL ) {
    if (is_null($_URL)) {
      $code = 400;
      $message = 'Не указан url операции';
      return (object)array('code'=>$code, 'message'=>$message, 'meta' => '');
    }


    $process = curl_init($_URL);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $_URL);
      } else {
        $code     = $_resp_code;

        $meta     = json_decode($return)->meta;
        $data     = json_decode($return);

        $message  = "Запрос выполнен успешно, данные получены";
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
  }



  /**
   * ПОЛУЧЕНИЕ ЗАКАЗА
   * @param  $_search =  поле фильтр для поиска по ИД товара
   * @return Стандартный массив -> объект
   */
  public function getCustomerOrders( $_search = NULL ) {

    $path   = '/entity/customerorder';
    $query  = '';

    if (!is_null($_search)) {
      $query   = '?search=' . $_search;
    }

    $process = curl_init($this->url . $path . $query);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {
        if (empty(json_decode($return)->rows)) {
          $code     = 201;
          $message  = "Данные товара Ид: $_id. <strong>НЕ найдены!</strong>";
          $meta     = '';
          $data     = '';
          
        } else {

          $code     = $_resp_code;
          $size     = json_decode($return)->meta->size;
          $data     = json_decode($return)->rows;
          $message  = "Найдено " .$size. " заказов";

        }
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
  }





  /**
   * GET WebHook
   * ПОЛУЧЕНИЕ СПИСКА ХУКОВ или отдельного хука
   * @param  $_search =  поле фильтр для поиска по ИД товара
   * @return Стандартный массив -> объект
   */
  public function getWebHook( $_id = NULL ) {

    $path   = '/entity/webhook';
    $query  = '';

    if (!is_null($_search)) {
      $query   = '/' . $_id;
    }

    $process = curl_init($this->url . $path . $query);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {
        if (empty(json_decode($return)->rows)) {

          $code     = 201;
          $message  = "хук не найден Ид: $_id";
          $meta     = '';
          $data     = '';
          
        } else {

          $size     = json_decode($return)->meta->size;
          $data     = json_decode($return)->rows;
          $code     = $_resp_code;
          $message  = "Найдено " .$size. " хуков";

        }
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
  }





  /**
   * DELETE WebHook
   * @param  [type] $_id [description]
   * @return [type]      [description]
   */
  public function delWebHook( $_id = NULL) {
    $path   = '/entity/webhook';
    $query   = '';

    if (!is_null($_id)) {
      $query   = '/' . $_id;
    }

    $process = curl_init($this->url . $path . $query);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_CUSTOMREQUEST, 'DELETE');
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {

        $code     = $_resp_code;
        $message  = "ВебХук " .$_id. " успешно удален";

      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => '', 'data' => '');

  }




  /**
   * ADD WebHook
   * @param [type] $_action [description]
   * @param [type] $_entity [description]
   * @param [type] $_url    [description]
   */
  public function setWebHook( $_action, $_entity, $_url ) {

    if ((empty($_url)) || (empty($_entity)) || (empty($_action))) {
      $code = 400;
      $message = 'Не указан один из параметров';
      return (object)array('code'=>$code, 'message'=>$message, 'meta' => '');
    }

    $path   = '/entity/webhook';

    $body = array(

      'url'         => $_url,
      'action'      => $_action,
      'entityType'  => $_entity,

    );

    $body = json_encode($body);

    $process = curl_init($this->url . $path );
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_POST, 1);
      curl_setopt($process, CURLOPT_POSTFIELDS, $body);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path);
      } else {

        $code     = $_resp_code;
        $message  = "Хук успешно добавлен";
        
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => '', 'data' => '');

  }





    /**
   * СОЗДАНИЕ ЗАКАЗА
   * @param  $_order =  объект заказа из commerce
   * @return Стандартный массив -> объект
   */
  public function setCustomerOrder( $_order = NULL ) {
    // if (is_null($_order)) {
    //   $code = 400;
    //   $message = 'Order обязательный параметр';
    //   return array('code'=>$code, 'message'=>$message, 'meta' => '');
    // }

    $order_wrapper = entity_metadata_wrapper('commerce_order', $_order);

    /* USERFORM in order */
    $user_form_from_order = "#" .$_order->order_id. "\n";
    $user_form_from_order .= "Телефон: " 
    . $order_wrapper->commerce_customer_billing->field_phone->value() . ";\n";
    $user_form_from_order .= "Индекс: " 
    . $order_wrapper->commerce_customer_billing->field_postindex->value() . ";\n";
    $user_form_from_order .= "Город: " 
    . $order_wrapper->commerce_customer_billing->field_city->value() . ";\n";
    $user_form_from_order .= "Улица: " 
    . $order_wrapper->commerce_customer_billing->field_street->value() . ";\n";
    $user_form_from_order .= "Комментарий: " 
    . $order_wrapper->commerce_customer_billing->field_comment->value()['value'] . ";\n";
    $user_form_from_order .= "ФИО: " 
    . $order_wrapper->commerce_customer_billing->field_fio->value() . ";\n";
    $user_form_from_order .= "Доставка: " 
    . $order_wrapper->commerce_customer_billing->field_ship->value() . ";\n";

    /* POSITIONS from order line_items */
    $_positions = array();
    $missedProducts = '';
    for ($i=0; $i < count($order_wrapper->commerce_line_items->value()); $i++) {
      $LI = $order_wrapper->commerce_line_items[$i];
      $quantity = $LI->quantity->value();
      $sku      = $LI->commerce_product->sku->value();
      $title    = $LI->commerce_product->title->value();
      
      $_product = $this->getProduct($sku);

      if ($_product->code == 201) {
        $missedProducts .= "\t * " . $sku ." ". $title ." - ". $quantity . "\n";
      }
      if ($_product->code == 200) {
        $position = array(
          "quantity"    => (int)$quantity,
          "reserve"     => (int)$quantity,
          "price"       => $_product->data->salePrices[0]->value,
          "assortment"  => array(
            "meta" => $_product->meta,
          ),
        );

        $_positions[] = $position;
      }

    }

    if (!empty($missedProducts)) {
      $missedProducts = "\nНекоторые товары не найдены на МоемСкладе: \n" . $missedProducts;
    }


    /* ==== */
    $_name = $_order->order_id;
    $_metaOrganization = $this->getOrganization()->meta;
    $_metaAgent = $this->getCounterparty( variable_get('moysklad_counterparty', '') )->meta;
    $_metaStore = $this->getStore( variable_get('moysklad_store', '') )->meta;
    $description = $user_form_from_order . $missedProducts;

    $path   = '/entity/customerorder';

    /* body */

    $body = array(

      'name'          => $_order->order_id,
      'organization'  => array('meta' => $_metaOrganization),
      'agent'         => array('meta' => $_metaAgent),
      'store'         => array('meta' => $_metaStore),
      'positions'     => $_positions,
      'description'   => 'Заказ с сайта ' . "(".$_order->order_id.") \n" . $description

    );

    // return $body;

    $body = json_encode($body);

    $process = curl_init($this->url . $path );
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_POST, 1);
      curl_setopt($process, CURLOPT_POSTFIELDS, $body);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path);
        /*
          Обработка непредвиденных событий в обработчике не в классе (rules.inc.php -> smoy_rules_action_send_order())
         */
      } else {

        $code     = $_resp_code;
        $name     = json_decode($return)->name;
        $data     = json_decode($return);
        $meta     = json_decode($return)->meta;
        $message  = "Создан заказ " .$name;
        
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
  }





    /**
   * ОБНОВЛЕНИЕ ЗАКАЗА
   * @param  $_order =  объект заказа из commerce
   * @return Стандартный массив -> объект
   */
  public function putCustomerOrder( $_order = NULL ) {
    if (is_null($_order)) {
      $code = 400;
      $message = 'Order обязательный параметр';
      return array('code'=>$code, 'message'=>$message, 'meta' => '');
    }

    $order_wrapper = entity_metadata_wrapper('commerce_order', $_order);
    $order_status = $order_wrapper->status->value();

    /* USERFORM in order */
    $user_form_from_order = "#" .$_order->order_id. "\n";
    $user_form_from_order .= "Телефон: " 
    . $order_wrapper->commerce_customer_billing->field_phone->value() . ";\n";
    $user_form_from_order .= "Индекс: " 
    . $order_wrapper->commerce_customer_billing->field_postindex->value() . ";\n";
    $user_form_from_order .= "Город: " 
    . $order_wrapper->commerce_customer_billing->field_city->value() . ";\n";
    $user_form_from_order .= "Улица: " 
    . $order_wrapper->commerce_customer_billing->field_street->value() . ";\n";
    $user_form_from_order .= "Комментарий: " 
    . $order_wrapper->commerce_customer_billing->field_comment->value()['value'] . ";\n";
    $user_form_from_order .= "ФИО: " 
    . $order_wrapper->commerce_customer_billing->field_fio->value() . ";\n";
    $user_form_from_order .= "Доставка: " 
    . $order_wrapper->commerce_customer_billing->field_ship->value() . ";\n";


    /* POSITIONS from order line_items */
    $_positions = array();
    $missedProducts = '';
    for ($i=0; $i < count($order_wrapper->commerce_line_items->value()); $i++) {
      $LI = $order_wrapper->commerce_line_items[$i];
      $quantity = $LI->quantity->value();
      $reserve  = $LI->quantity->value();
      $sku      = $LI->commerce_product->sku->value();
      $title    = $LI->commerce_product->title->value();

      if ($order_status == 'canceled') {
        $reserve  = 0;
      }
      
      $_product = $this->getProduct($sku);

      if ($_product->code == 201) {
        $missedProducts .= "\t * " . $sku ." ". $title ." - ". $quantity . "\n";
      }
      if ($_product->code == 200) {
        $position = array(
          "quantity"    => (int)$quantity,
          "reserve"     => (int)$reserve,
          "price"       => $_product->data->salePrices[0]->value,
          "assortment"  => array(
            "meta" => $_product->meta,
          ),
        );

        $_positions[] = $position;
      }

    }

    if (!empty($missedProducts)) {
      $missedProducts = "\nНекоторые товары не найдены на МоемСкладе: \n" . $missedProducts;
    }



    /* ==== */
    $_name = $_order->order_id;
    $description = $user_form_from_order . $missedProducts;

    #TODO : #FIX getCustomerOrders возвращает результаты ПОИСКА они не всегда предсказуемы; 
    # Нужно прочитать массив сравнить и выбрать ордер по ID

    $_moyOrder = $this->getCustomerOrders($_name);
    $__order_id = $_moyOrder->data[0]->id;

    $path   = '/entity/customerorder';
    $query  = '/' . $__order_id;

    /* body */

    $body = array(

      'positions'     => $_positions,
      'description'   => '* ' . $description

    );

    /* == изменение статуса заказа == */
    // if ($order_status == 'canceled') {
    //   $_state = $this->getStates( 'customerorder', 'Отменен' );
    //   $body['state'] = array('meta' => $_state->data->meta);
    // }

    if ($order_status) {
      $order_status_moysklad = smoy_commerce_to_moysklad_state_conv($order_status);
      $_state = $this->getStates( 'customerorder', $order_status_moysklad );
      $body['state'] = array('meta' => $_state->data->meta);
      
    }

    // return $body;

    $body = json_encode($body);

    $process = curl_init($this->url . $path . $query );
    // $process = curl_init("https://webhook.site/df947635-d33e-49e6-b244-f42fdad15c1a" );
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_CUSTOMREQUEST, 'PUT');
      curl_setopt($process, CURLOPT_POSTFIELDS, $body);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {

        $code     = $_resp_code;
        $name     = json_decode($return)->name;
        $data     = json_decode($return);
        $meta     = json_decode($return)->meta;
        $message  = "Обновлен заказ " .$name;
        
      }
    curl_close($process);

    /**
     * создание документа отгрузки для завершенного заказа
     */
    if (isset($_moyOrder->data[0]->demands)) {
      $demands = $_moyOrder->data[0]->demands;
      foreach ($demands as $demand) {
        $_demandResp = $this->getRequestData($demand->meta->href);
        if ( $_demandResp->code == 200 ) {
          $_demand_del_resp = $this->delDemand( $_demandResp->data->id );
        }
      }
    }

    if ( smoy_order_status_is_complete ($order_status) ) {
      $_demandTemplate = $this->getDemandTemplate($meta);
      if ( $_demandTemplate->code == 200 ) {
        $_demand = $this->setDemand($_demandTemplate->data);
        if ( $_demand->code != 200 ) {
          watchdog('moysklad', 'Не удалось создать отгрузку', NULL, WATCHDOG_ERROR, $this->url . $path . $query);
          # code...
        }
      }
    }


    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
  }
 



  /**
   * УДАЛЕНИЕ ЗАКАЗА
   * @param  $_search =  поле фильтр для поиска по ИД товара
   * @return Стандартный массив -> объект
   */
  public function delCustomerOrder( $_order ) {

    if (is_null($_order)) {
      $code = 400;
      $message = 'Order обязательный параметр';
      return array('code'=>$code, 'message'=>$message, 'meta' => '');
    }


    /* ==== */
    $_name = $_order->order_id;

    #TODO : #FIX getCustomerOrders возвращает результаты ПОИСКА они не всегда предсказуемы; 
    
    $_moyOrder = $this->getCustomerOrders($_name);
    $__order_id = $_moyOrder->data[0]->id;

    $path   = '/entity/customerorder';
    $query  = '/' . $__order_id;
    /* ==== */


    $process = curl_init($this->url . $path . $query);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_CUSTOMREQUEST, 'DELETE');
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {
        if (empty(json_decode($return)->rows)) {
          $code     = 201;
          $message  = "Данные товара Ид: $_id. <strong>НЕ найдены!</strong>";
          $meta     = '';
          $data     = '';
          
        } else {

        $code     = $_resp_code;
        $message  = "Заказ номер $_name ($__order_id) успешно удален";
        }
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => '', 'data' => '');
  }


  public function setDemand ( $_demandTemplate = NULL ) {
    if (is_null($_demandTemplate)) {
      $code = 400;
      $message = 'Метаданные заказа не доставлены';
      return (object)array('code'=>$code, 'message'=>$message, 'meta' => '', 'data' => '');
    }

    $path = '/entity/demand';

    $body = $_demandTemplate;

    $body = json_encode($body);

    $process = curl_init($this->url . $path );
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_POST, 1);
      curl_setopt($process, CURLOPT_POSTFIELDS, $body);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {

        $code     = $_resp_code;
        $data     = json_decode($return);
        $meta     = json_decode($return)->meta;
        $message  = "Получен шаблон для отгрузки";
        
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
  }





  /**
   * Получаем шаблон отгрузки из заказа клиента
   * @param  [type] $_customerOrderMeta заказ клиента в формате метаданных
   * @return [type] стандартный объект 
   * ...->data   -  предзаполненый запрос для отправки POST запросом
   */
  public function getDemandTemplate ( $_customerOrderMeta = NULL ) {
    if (is_null($_customerOrderMeta)) {
      $code = 400;
      $message = 'Метаданные заказа не доставлены';
      return (object)array('code'=>$code, 'message'=>$message, 'meta' => '', 'data' => '');
    }

    $path = '/entity/demand/new';

    $body = array(
      'customerOrder' => array('meta' => $_customerOrderMeta)
    );

    $body = json_encode($body);

    $process = curl_init($this->url . $path );
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_CUSTOMREQUEST, 'PUT');
      curl_setopt($process, CURLOPT_POSTFIELDS, $body);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {

        $code     = $_resp_code;
        $data     = json_decode($return);
        $meta     = json_decode($return)->meta;
        $message  = "Получен шаблон для отгрузки";
        
      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
  }





  /**
   * Получение отгрузок
   * 
   * @return [type] [description]
   */
  public function getDemand ( $_name = NULL ) {

    $path = '/entity/demand';
    $auery = '';

    if (!is_null($_name)) {
      $query   = '?search=' . $_name;
    }

    $process = curl_init($this->url . $path . $query);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {
        $code     = $_resp_code;
        $meta     = json_decode($return)->meta;
        $data     = json_decode($return)->rows;
        $message  = "Данные отгрузок.";
      }
    curl_close($process);

    return (object)array('code'=>$code, 'message'=>$message, 'meta' => $meta, 'data' => $data);
  }





    /**
   * DELETE Demands
   * принимает ИД отгрузки
   * @param  [type] $_id [description]
   * @return [type]      [description]
   */
  public function delDemand( $_id = NULL ) {
    $path   = '/entity/demand';
    $query   = '';

    if (!is_null($_id)) {
      $query   = '/' . $_id;
    }

    $process = curl_init($this->url . $path . $query);
      curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
      curl_setopt($process, CURLOPT_CUSTOMREQUEST, 'DELETE');
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_TIMEOUT, 30);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $return = curl_exec($process);
      $_resp_code = curl_getinfo($process, CURLINFO_RESPONSE_CODE);
      
      if ( (json_decode($return)->errors[0]) || ($_resp_code != 200) ) {
        $code     = $_resp_code;
        $message  = "[$_resp_code] - " . json_decode($return)->errors[0]->error;
        watchdog('moysklad', $message, NULL, WATCHDOG_ERROR, $this->url . $path . $query);
      } else {

        $code     = $_resp_code;
        $message  = "Отгрузка " .$_id. " успешно удалена";

      }
    curl_close($process);

    return (object)array(
      'code'=>$code, 'message'=>$message, 'meta' => '', 'data' => '');

  }











} // class end;

 ?>