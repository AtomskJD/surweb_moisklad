<?php 


function smoy_moysklad_page() {
  return array('#markup' => 
    '<h2>Мойсклад</h2>' .
    '<li><a href="/admin/store/settings/moysklad/settings">Настройки</a></li>'.
    '<li><a href="/admin/store/settings/moysklad/settings">Тесты</a></li>'
  );
}