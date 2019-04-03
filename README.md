# Модуль surweb_moisklad
> Модуль актуализирует остатки товара на Моемскладе после совершения заказа или его редактирования на сайте.
> Актуализация остатков на сайте происходит через ВебХуки Моегосклада. 
> Реализована обработка Хуков для приемки, списания, заказа покупателя и оприходования.
> Для корректной работы необходимы модули: commerce, commerce_cart, commerce_discount, rules; на стороне сервера curl.

## Инструкции
1. развертывание бекапа (…)

2. изменение .htaccess - закоментировать фрагмент описывающий переадресацию на https

3. Установить и включить модуль surweb_moysklad v.3-x - Включить модуль Database logging (dblog)

4. Отключить правила управления складскими остатками
/admin/config/workflow/rules/
    - Stock: Decrease when completing the order process
    - Stock: Increase when canceling the order process
    - При удалении товара из заказа
    - Изменение количества товара в заказе
    - Первичное добавление товара в существующий заказ

5. Импорт скидок
    1. summ 10
    ```
    {
      "name" : "discount_summ_10",
      "label" : "summ 10",
      "type" : "order_discount",
      "status" : "1",
      "component_title" : "\u0421\u043a\u0438\u0434\u043a\u0430 10% \u0441\u0443\u043c\u043c\u0430 \u043e\u0442 5000 \u0434\u043e 10000",
      "sort_order" : "10",
      "commerce_discount_offer" : {
        "type" : "percentage",
        "commerce_percentage" : { "und" : [ { "value" : "10.00" } ] },
        "rdf_mapping" : []
      },
      "commerce_compatibility_strategy" : { "und" : [ { "value" : "none" } ] },
      "commerce_compatibility_selection" : [],
      "commerce_discount_date" : [],
      "inline_conditions" : { "und" : [
          {
            "condition_name" : "commerce_order_compare_order_amount",
            "condition_settings" : {
              "line_item_types" : { "fee" : "fee", "product" : "product" },
              "operator" : "\u003E=",
              "total" : { "amount" : 500000, "currency_code" : "RUB" },
              "condition_logic_operator" : null
            },
            "condition_negate" : 0
          },
          {
            "condition_name" : "commerce_order_compare_order_amount",
            "condition_settings" : {
              "line_item_types" : { "fee" : "fee", "product" : "product" },
              "operator" : "\u003C",
              "total" : { "amount" : 1000000, "currency_code" : "RUB" }
            },
            "condition_negate" : 0,
            "condition_logic_operator" : "1"
          }
        ]
      },
      "discount_usage_per_person" : [],
      "discount_usage_limit" : [],
      "rdf_mapping" : []
    }
    ```

    2. summ 20
    ```
    {
      "name" : "discount_summ_20",
      "label" : "summ 20",
      "type" : "order_discount",
      "status" : "1",
      "component_title" : "\u0421\u043a\u0438\u0434\u043a\u0430 20% \u0441\u0443\u043c\u043c\u0430 \u043e\u0442 10000 \u0434\u043e 30000",
      "sort_order" : "10",
      "commerce_discount_offer" : {
        "type" : "percentage",
        "commerce_percentage" : { "und" : [ { "value" : "20.00" } ] },
        "rdf_mapping" : []
      },
      "commerce_compatibility_strategy" : { "und" : [ { "value" : "none" } ] },
      "commerce_compatibility_selection" : [],
      "commerce_discount_date" : [],
      "inline_conditions" : { "und" : [
          {
            "condition_name" : "commerce_order_compare_order_amount",
            "condition_settings" : {
              "line_item_types" : { "fee" : "fee", "product" : "product" },
              "operator" : "\u003E=",
              "total" : { "amount" : 1000000, "currency_code" : "RUB" },
              "condition_logic_operator" : null
            },
            "condition_negate" : 0
          },
          {
            "condition_name" : "commerce_order_compare_order_amount",
            "condition_settings" : {
              "line_item_types" : { "fee" : "fee", "product" : "product" },
              "operator" : "\u003C",
              "total" : { "amount" : 3000000, "currency_code" : "RUB" }
            },
            "condition_negate" : 0,
            "condition_logic_operator" : "1"
          }
        ]
      },
      "discount_usage_per_person" : [],
      "discount_usage_limit" : [],
      "rdf_mapping" : []
    }
    ```

    3. summ 25
    ```
    {
      "name" : "discount_summ_25",
      "label" : "summ 25",
      "type" : "order_discount",
      "status" : "1",
      "component_title" : "\u0421\u043a\u0438\u0434\u043a\u0430 25% \u0441\u0443\u043c\u043c\u0430 \u0431\u043e\u043b\u044c\u0448\u0435 30000",
      "sort_order" : "10",
      "commerce_discount_offer" : {
        "type" : "percentage",
        "commerce_percentage" : { "und" : [ { "value" : "25.00" } ] },
        "rdf_mapping" : []
      },
      "commerce_compatibility_strategy" : { "und" : [ { "value" : "none" } ] },
      "commerce_compatibility_selection" : [],
      "commerce_discount_date" : [],
      "inline_conditions" : { "und" : [
          {
            "condition_name" : "commerce_order_compare_order_amount",
            "condition_settings" : {
              "line_item_types" : { "fee" : "fee", "product" : "product" },
              "operator" : "\u003E=",
              "total" : { "amount" : 3000000, "currency_code" : "RUB" },
              "condition_logic_operator" : null
            },
            "condition_negate" : 0
          }
        ]
      },
      "discount_usage_per_person" : [],
      "discount_usage_limit" : [],
      "rdf_mapping" : []
    }
    ```

    4. rule 3
    ```
    {
      "name" : "discount_rule_3",
      "label" : "rule 3",
      "type" : "order_discount",
      "status" : "1",
      "component_title" : "\u0421\u043a\u0438\u0434\u043a\u0430 3% (\u042f \u0432\u0441\u0442\u0443\u043f\u0438\u043b \u0432 \u0433\u0440\u0443\u043f\u043f\u0443 VK \u0438 \u0445\u043e\u0447\u0443 \u0441\u043a\u0438\u0434\u043a\u0443 3%)",
      "sort_order" : "20",
      "commerce_discount_offer" : {
        "type" : "percentage",
        "commerce_percentage" : { "und" : [ { "value" : "3.00" } ] },
        "rdf_mapping" : []
      },
      "commerce_compatibility_strategy" : { "und" : [ { "value" : "none" } ] },
      "commerce_compatibility_selection" : [],
      "commerce_discount_date" : [],
      "inline_conditions" : [],
      "discount_usage_per_person" : [],
      "discount_usage_limit" : [],
      "rdf_mapping" : []
    }
    ```

    5. rule 5
    ```
    {
      "name" : "discount_rule_5",
      "label" : "rule 5",
      "type" : "order_discount",
      "status" : "1",
      "component_title" : "\u0421\u043a\u0438\u0434\u043a\u0430 5% (\u0423 \u043c\u0435\u043d\u044f \u0435\u0441\u0442\u044c \u043a\u0430\u0440\u0442\u043e\u0447\u043a\u0430 \u043f\u043e\u0441\u0442\u043e\u044f\u043d\u043d\u043e\u0433\u043e \u043a\u043b\u0438\u0435\u043d\u0442\u0430 5%)",
      "sort_order" : "20",
      "commerce_discount_offer" : {
        "type" : "percentage",
        "commerce_percentage" : { "und" : [ { "value" : "5.00" } ] },
        "rdf_mapping" : []
      },
      "commerce_compatibility_strategy" : { "und" : [ { "value" : "none" } ] },
      "commerce_compatibility_selection" : [],
      "commerce_discount_date" : [],
      "inline_conditions" : [],
      "discount_usage_per_person" : [],
      "discount_usage_limit" : [],
      "rdf_mapping" : []
    }
    ```

6. Импорт правил  

    1. Создание заказа

    ```
    { "rules_commerce_to_smoy_order" : {
        "LABEL" : "\u0421\u043e\u0437\u0434\u0430\u043d\u0438\u0435 \u0437\u0430\u043a\u0430\u0437\u0430",
        "PLUGIN" : "reaction rule",
        "OWNER" : "rules",
        "TAGS" : [ "Commerce Checkout", "moysklad" ],
        "REQUIRES" : [ "surweb_moysklad", "commerce_checkout", "entity" ],
        "ON" : { "commerce_checkout_complete" : [], "commerce_order_insert" : [] },
        "DO" : [ { "smoy_sendOrder" : { "value" : [ "commerce-order" ] } } ]
      }
    }
    ```


    2. Изменение заказа
    ```
    { "rules_smoy_change_order" : {
        "LABEL" : "\u0418\u0437\u043c\u0435\u043d\u0435\u043d\u0438\u0435 \u0437\u0430\u043a\u0430\u0437\u0430",
        "PLUGIN" : "reaction rule",
        "WEIGHT" : "10",
        "OWNER" : "rules",
        "TAGS" : [ "moysklad" ],
        "REQUIRES" : [ "commerce_cart", "surweb_moysklad", "entity" ],
        "ON" : { "commerce_order_update" : [] },
        "IF" : [
          { "NOT commerce_order_is_cart" : { "commerce_order" : [ "commerce_order" ] } }
        ],
        "DO" : [ { "smoy_updateOrder" : { "value" : [ "commerce-order" ] } } ]
      }
    }
    ```

    3. Удаление заказа
    ```
    { "rules_smoy_order_delete" : {
        "LABEL" : "\u0423\u0434\u0430\u043b\u0435\u043d\u0438\u0435 \u0437\u0430\u043a\u0430\u0437\u0430",
        "PLUGIN" : "reaction rule",
        "OWNER" : "rules",
        "TAGS" : [ "moysklad" ],
        "REQUIRES" : [ "surweb_moysklad", "entity" ],
        "ON" : { "commerce_order_delete" : [] },
        "DO" : [ { "smoy_deleteOrder" : { "value" : [ "commerce-order" ] } } ]
      }
    }
    ```

    4. изменение LI
    ```
    { "rules__li" : {
        "LABEL" : "\u0438\u0437\u043c\u0435\u043d\u0435\u043d\u0438\u0435 LI",
        "PLUGIN" : "reaction rule",
        "OWNER" : "rules",
        "REQUIRES" : [ "surweb_moysklad", "entity" ],
        "ON" : { "commerce_line_item_update" : [] },
        "DO" : [ { "smoy_updateOrder" : { "value" : [ "commerce-line-item:order" ] } } ]
      }
    }
    ```

    5. summ 10
    ```
    { "commerce_discount_rule_discount_summ_10" : {
        "LABEL" : "summ 10",
        "PLUGIN" : "reaction rule",
        "WEIGHT" : "-1",
        "OWNER" : "rules",
        "TAGS" : [
          "Commerce Discount",
          "\u0421\u043a\u0438\u0434\u043a\u0430 \u0437\u0430\u043a\u0430\u0437\u0430"
        ],
        "REQUIRES" : [ "commerce_discount", "rules", "entity", "commerce_checkout" ],
        "ON" : { "commerce_order_presave" : [], "commerce_checkout_complete" : [] },
        "IF" : [
          { "commerce_discount_compatibility_check" : {
              "commerce_order" : [ "commerce-order" ],
              "commerce_discount" : "discount_summ_10"
            }
          },
          { "AND" : [
              { "commerce_order_compare_order_amount" : {
                  "operator" : "\u003C",
                  "total" : { "value" : { "amount" : 1000000, "currency_code" : "RUB" } }
                }
              },
              { "commerce_order_compare_order_amount" : {
                  "operator" : "\u003E=",
                  "total" : { "value" : { "amount" : 500000, "currency_code" : "RUB" } }
                }
              }
            ]
          }
        ],
        "DO" : [
          { "commerce_discount_percentage" : {
              "entity" : [ "commerce_order" ],
              "commerce_discount" : "discount_summ_10"
            }
          },
          { "drupal_message" : { "message" : "\u043f\u0440\u0435\u0434\u043e\u0441\u0442\u0430\u0432\u0438\u0442\u044c \u0441\u043a\u0438\u0434\u043a\u0443 10%" } }
        ]
      }
    }
    ```

    6. summ 20
    ```
    { "commerce_discount_rule_discount_summ_20" : {
        "LABEL" : "summ 20",
        "PLUGIN" : "reaction rule",
        "WEIGHT" : "-1",
        "OWNER" : "rules",
        "TAGS" : [
          "Commerce Discount",
          "\u0421\u043a\u0438\u0434\u043a\u0430 \u0437\u0430\u043a\u0430\u0437\u0430"
        ],
        "REQUIRES" : [ "commerce_discount", "commerce_checkout", "entity" ],
        "ON" : { "commerce_checkout_complete" : [], "commerce_order_presave" : [] },
        "IF" : [
          { "commerce_discount_compatibility_check" : {
              "commerce_order" : [ "commerce-order" ],
              "commerce_discount" : "discount_summ_20"
            }
          },
          { "AND" : [
              { "commerce_order_compare_order_amount" : {
                  "operator" : "\u003C",
                  "total" : { "value" : { "amount" : 3000000, "currency_code" : "RUB" } }
                }
              },
              { "commerce_order_compare_order_amount" : {
                  "operator" : "\u003E=",
                  "total" : { "value" : { "amount" : 1000000, "currency_code" : "RUB" } }
                }
              }
            ]
          }
        ],
        "DO" : [
          { "commerce_discount_percentage" : {
              "entity" : [ "commerce_order" ],
              "commerce_discount" : "discount_summ_20"
            }
          }
        ]
      }
    }
    ```

    7. summ 25
    ```
    { "commerce_discount_rule_discount_summ_25" : {
        "LABEL" : "summ 25",
        "PLUGIN" : "reaction rule",
        "WEIGHT" : "-1",
        "OWNER" : "rules",
        "TAGS" : [
          "Commerce Discount",
          "\u0421\u043a\u0438\u0434\u043a\u0430 \u0437\u0430\u043a\u0430\u0437\u0430"
        ],
        "REQUIRES" : [ "commerce_discount", "commerce_checkout", "entity" ],
        "ON" : { "commerce_checkout_complete" : [], "commerce_order_presave" : [] },
        "IF" : [
          { "commerce_discount_compatibility_check" : {
              "commerce_order" : [ "commerce-order" ],
              "commerce_discount" : "discount_summ_25"
            }
          },
          { "commerce_order_compare_order_amount" : {
              "operator" : "\u003E=",
              "total" : { "value" : { "amount" : 3000000, "currency_code" : "RUB" } }
            }
          }
        ],
        "DO" : [
          { "commerce_discount_percentage" : {
              "entity" : [ "commerce_order" ],
              "commerce_discount" : "discount_summ_25"
            }
          }
        ]
      }
    }
    ```

    8. Присвоить скидку на основе поля "Скидка" 5%
    ```
    { "rules_user_set_discount_5" : {
        "LABEL" : "\u041f\u0440\u0438\u0441\u0432\u043e\u0438\u0442\u044c \u0441\u043a\u0438\u0434\u043a\u0443 \u043d\u0430 \u043e\u0441\u043d\u043e\u0432\u0435 \u043f\u043e\u043b\u044f \u0022\u0421\u043a\u0438\u0434\u043a\u0430\u0022 5%",
        "PLUGIN" : "reaction rule",
        "OWNER" : "rules",
        "TAGS" : [ "discount" ],
        "REQUIRES" : [ "surweb_moysklad", "commerce_discount", "commerce_checkout", "entity" ],
        "ON" : { "commerce_checkout_complete" : [], "commerce_order_presave" : [] },
        "IF" : [
          { "smoy_checkDiscount" : {
              "value" : [ "commerce-order" ],
              "value2" : "\u0423 \u043c\u0435\u043d\u044f \u0435\u0441\u0442\u044c \u043a\u0430\u0440\u0442\u043e\u0447\u043a\u0430 \u043f\u043e\u0441\u0442\u043e\u044f\u043d\u043d\u043e\u0433\u043e \u043a\u043b\u0438\u0435\u043d\u0442\u0430"
            }
          },
          { "AND" : [
              { "commerce_discount_compatibility_check" : {
                  "commerce_order" : [ "commerce_order" ],
                  "commerce_discount" : "discount_summ_10"
                }
              },
              { "commerce_discount_compatibility_check" : {
                  "commerce_order" : [ "commerce_order" ],
                  "commerce_discount" : "discount_summ_20"
                }
              },
              { "commerce_discount_compatibility_check" : {
                  "commerce_order" : [ "commerce_order" ],
                  "commerce_discount" : "discount_summ_25"
                }
              }
            ]
          }
        ],
        "DO" : [
          { "commerce_discount_percentage" : {
              "entity" : [ "commerce-order" ],
              "commerce_discount" : "discount_rule_5"
            }
          }
        ]
      }
    }
    ```

    9. Присвоить скидку на основе поля "Скидка" 3%
    ```
    { "rules_user_set_discount_3" : {
        "LABEL" : "\u041f\u0440\u0438\u0441\u0432\u043e\u0438\u0442\u044c \u0441\u043a\u0438\u0434\u043a\u0443 \u043d\u0430 \u043e\u0441\u043d\u043e\u0432\u0435 \u043f\u043e\u043b\u044f \u0022\u0421\u043a\u0438\u0434\u043a\u0430\u0022 3%",
        "PLUGIN" : "reaction rule",
        "OWNER" : "rules",
        "TAGS" : [ "discount" ],
        "REQUIRES" : [ "surweb_moysklad", "commerce_discount", "commerce_checkout", "entity" ],
        "ON" : { "commerce_checkout_complete" : [], "commerce_order_presave" : [] },
        "IF" : [
          { "smoy_checkDiscount" : {
              "value" : [ "commerce-order" ],
              "value2" : "\u042f \u0432\u0441\u0442\u0443\u043f\u0438\u043b \u0432 \u0433\u0440\u0443\u043f\u043f\u0443 VK \u0438 \u0445\u043e\u0447\u0443 \u0441\u043a\u0438\u0434\u043a\u0443 5%"
            }
          },
          { "AND" : [
              { "commerce_discount_compatibility_check" : {
                  "commerce_order" : [ "commerce_order" ],
                  "commerce_discount" : "discount_summ_10"
                }
              },
              { "commerce_discount_compatibility_check" : {
                  "commerce_order" : [ "commerce_order" ],
                  "commerce_discount" : "discount_summ_20"
                }
              },
              { "commerce_discount_compatibility_check" : {
                  "commerce_order" : [ "commerce_order" ],
                  "commerce_discount" : "discount_summ_25"
                }
              }
            ]
          }
        ],
        "DO" : [
          { "commerce_discount_percentage" : {
              "entity" : [ "commerce-order" ],
              "commerce_discount" : "discount_rule_3"
            }
          }
        ]
      }
    }
    ```
    10. Применение ценообразования
    ```
    { "rules_discounts_pre_save" : {
        "LABEL" : "\u041f\u0440\u0438\u043c\u0435\u043d\u0435\u043d\u0438\u0435 \u0446\u0435\u043d\u043e\u043e\u0431\u0440\u0430\u0437\u043e\u0432\u0430\u043d\u0438\u044f",
        "PLUGIN" : "reaction rule",
        "OWNER" : "rules",
        "TAGS" : [ "Commerce Discount", "moysclad" ],
        "REQUIRES" : [ "commerce_cart", "surweb_moysklad", "entity" ],
        "ON" : { "commerce_order_presave" : [] },
        "IF" : [
          { "NOT commerce_order_is_cart" : { "commerce_order" : [ "commerce_order" ] } }
        ],
        "DO" : [ { "smoy_refreshOrder" : { "value" : [ "commerce-order" ] } } ]
      }
    }
    ```

    11. **УДАЛИТЬ** или отключить автоправила от скидок 
        - rule 3
        - rule 5
	



7. Настроить модуль [/admin/commerce/smoy-settings](/admin/commerce/smoy-settings)
8. Настроить **ВебХуки** моегосклада [/admin/commerce/smoy-settings/hooks](/admin/commerce/smoy-settings/hooks)
    - Действие `CREATE`
    - Тип события `CUSTOMERORDER`
    - Адрес для хука `/smoy-sync (адрес абсолютный)`


## History
ver 3.1
===
Релизные функции
Добавлены:
	* Обработка скидок
	* Проверка остатков со стороны сайта на основе очередей
	* Новые правила обработки заказа из админ интерфейса

ver 3.0
===
Tue 12 Feb 2019 11:38:40 AM +05
Описание: Модуль синхронизации Друпал(комерц) новым API moysklad v1.1;
Description: Modue for sychronization Drupal 7 (commerce) with new JSON API moysklad v1.1;

ver 2.x
===
date: 09 Mar 2017
Описание: Модуль синхронизации Друпал(Уберкарт) новым API moysklad;
Description: Modue for sychronization Drupal 7 (Ubercart) with new JSON API moysklad;

ver 1.x
===
Описание: Модуль синхронизации Друпал(Уберкарт) новым API moysklad;
Description: Modue for sychronization Drupal 7 (Ubercart) with XML API moysklad;