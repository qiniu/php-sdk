<?php
require_once(dirname(__FILE__) . "/rs.php");
require_once(dirname(__FILE__) . "/rsf.php");

function prefixBatch($op, $bucket, $prefix, $destBucket = '', $marker = '') {
  $allItems = array();
  $allResults = array();
  $itemCount = 0;
  while(true){
    $self = new Qiniu_MacHttpClient(null);
    $result = Qiniu_RSF_ListPrefix($self, $bucket, $prefix, $marker);
    if($op === 'list') {
      $allItems = array_merge($allItems, $result[0]);
    }
    $marker = $result[1];

    $itemCount += count($result[0]);
    if($itemCount === 0) { // 无满足条件的文件时，不要进入后面的Qiniu_RS_BatchXXXX，否则浪费时间且报错
      return array(
        'result' => 0,
        'itemCount' => 0
      );
    }

    if(count($result[0]) === 0) { // 猜测后台七牛是按返回文件数与limit比较来判断结束的，因此需要处理特殊边界条件
      break; // 此次循环体执行时无文件，不要进入后面的Qiniu_RS_BatchXXXX，否则浪费时间且报错
    }

    switch($op) {
      case 'stat':
      case 'delete':
        $destBucket = '';
      case 'move':
      case 'copy':
        $entries = genEntries($bucket, $result[0], $destBucket);
        break;
    }

    switch($op) {
      case 'stat':
        $batchResult = Qiniu_RS_BatchStat($self, $entries);
        $allResults = array_merge($allResults, $batchResult);
        break;
      case 'delete':
        $batchResult = Qiniu_RS_BatchDelete($self, $entries);
        $allResults = array_merge($allResults, $batchResult);
        break;
      case 'move':
        $batchResult = Qiniu_RS_BatchMove($self, $entries);
        $allResults = array_merge($allResults, $batchResult);
        break;
      case 'copy':
        $batchResult = Qiniu_RS_BatchCopy($self, $entries);
        $allResults = array_merge($allResults, $batchResult);
        break;
    }

    if($result[2] === Qiniu_RSF_EOF) {
      break;
    }
  }

  switch($op) {
    case 'list':
      return $allItems;
      break;
    case 'stat':
    case 'delete':
    case 'move':
    case 'copy':
      return $allResults;
    break;
  }
}

function genEntries($bucket, $items, $destBucket = '') {
  $entries = array();
  foreach ($items as $item) {
    $entry = new StdClass;
    if(!$destBucket) {
      $entry->bucket = $bucket;
      $entry->key = $item['key'];
    } else {
      $src = new StdClass;
      $src->bucket = $bucket;
      $src->key = $item['key'];
      $dest = new StdClass;
      $dest->bucket = $destBucket;
      $dest->key = $item['key'];
      $entry->src = $src;
      $entry->dest = $dest;
    }
    $entries[] = $entry;
  }
  return $entries;
}
