<?php
namespace App\Consts;

class RoleConst
{
  const ADMINISTRATOR = '0';
  const ADMINISTRATOR_NAME = '管理者';
  const SHOP_STAFF = '1';
  const SHOP_STAFF_NAME = '店舗代表者';
  const ROLE_LIST = [
    self::ADMINISTRATOR => self::ADMINISTRATOR_NAME,
    self::SHOP_STAFF => self::SHOP_STAFF_NAME
  ];
}