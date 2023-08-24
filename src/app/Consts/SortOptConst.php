<?php
namespace App\Consts;

class SortOptConst
{
  const RANDOM = '0';
  const RANDOM_NAME = 'ランダム';
  const DESCENDING = '1';
  const DESCENDING_NAME = '評価が高い順';
  const ASCENDING = '2';
  const ASCENDING_NAME = '評価が低い順';
  const SORT_LIST = [
    self::RANDOM => self::RANDOM_NAME,
    self::DESCENDING => self::DESCENDING_NAME,
    self::ASCENDING => self::ASCENDING_NAME
  ];
}