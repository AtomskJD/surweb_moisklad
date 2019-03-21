<?php 

include_once('moysklad.class.php');
include_once("functions.inc.php");

function smoy_hooks_page ($form, &$form_state) {
  $hooks_page = array();
  $moysklad = new Moysklad();

  $_hooks = $moysklad->getWebHook();

 // Панель информации о хуках
    $hooks_page['hooks'] = array(
        '#type' => 'fieldset',
        '#title' => 'Доступные хуки',

    );

  // Панель создания нового хука
    $hooks_page['new_hook'] = array(
        '#type' => 'fieldset',
        '#title' => 'Создать новый Вебхук',

    );

    $hooks_page['new_hook']['action'] = array(
      '#type' => 'select',
      '#title' => 'Действия',
      '#options' => array(
        'CREATE' => 'CREATE',
        'UPDATE' => 'UPDATE',
        'DELETE' => 'DELETE',
      ),
    );

    $hooks_page['new_hook']['entityType'] = array(
      '#type' => 'textfield', 
      '#title' => 'Тип события', 
      '#size' => 30, 
      '#maxlength' => 60, 
      '#required' => FALSE,
    );
    $hooks_page['new_hook']['url'] = array(
      '#type' => 'textfield', 
      '#title' => 'Адрес для хука', 
      '#size' => 60, 
      '#maxlength' => 256, 
      '#required' => FALSE,
    );

    $hooks_page['new_hook']['submit'] = array(
      '#type'     => 'submit',
      '#value'    => 'Добавить хук',
      '#name'     => 'add_op',
      '#submit'   => array('smoy_addHook'),
    );


    if ($_hooks->code == 200) {
      $i = 0;
      
      usort($_hooks->data, function($a, $b) {
        return strcmp ($a->entityType, $b->entityType);
      });


    	foreach ($_hooks->data as $hook) {
        $i++;
    		$hooks_page['hooks'][$hook->id] = array(
    			'#markup' => "<div id='".$hook->id."'><strong>"
          .strtoupper($hook->entityType)
          ."</strong> - <strong>"
          .strtoupper($hook->action)
          ."</strong> - <span>" 
          .$hook->url 
          ."</span></div>",
    		);

        $hooks_page['hooks']['b_'.$hook->id] = array(
          '#type'     => 'submit',
          '#value'    => 'Удалить хук '.$i,
          '#name'     => 'del_op',
          '#hook_id'  => $hook->id,
          '#submit'   => array('smoy_deleteHook'),
        );
    	}
    }

  // drupal_set_message(t('dodo'), 'status', FALSE);
  return $hooks_page;

}