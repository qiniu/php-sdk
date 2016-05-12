<?php
namespace Qiniu\Storage;

use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Http\Client;
use Qiniu\Http\Error;
use Qiniu\Storage\BucketManager;

/**
 * Desc   : A simple file export tool based on BucketManager,Generate download command with php,and download with wget
 * Author : elespec@gmail.com
 *
 */
final class BucketExporter
{
    private $auth;

    public function __construct(Auth $auth,$bucket,$logPath=null)
    {
        $this->auth    = $auth;
        $this->bucket  = $bucket;
        $this->logPath = $logPath ? $logPath : '/tmp/log/qiniu_log/';
        $this->runningTime = date('Y-m-d_H-i-s');
        $this->bucketItemListFilePrefix = $this->logPath.$this->bucket.'_itemLists_'.$this->runningTime;
        $this->cmdFilePrefix            = $this->logPath.$this->bucket.'_syncCmd_'.$this->runningTime;
        file_exists($this->logPath) || mkdir($this->logPath,0777,true);
    }

    public function exportBucketitem()
    {
        $bucketMgr = new BucketManager($this->auth);
        $marker = null;
        $prefix = null;
        $limit = 10;
        $delimiter = null;

        for($i=0;;$i++)
        {
            list($item, $marker, $err) = $bucketMgr->listFiles($this->bucket, $prefix, $marker, $limit);//may be here can replace by marker parameter
            $itemLists='';
            $t=0;
            foreach($item as $k=>$v)
            {
                $itemLists.=$v['key']."\n";
                $t++;
            }
            $itemListFile=$this->bucketItemListFilePrefix.'_'.$i.'.txt';
            file_put_contents($itemListFile,$itemLists,FILE_APPEND);

            if($t<$limit)
            {
                $count=$i*$limit+$t;
                $data=array(
                            'msg'=>'Dump over ,Total '.$count.' item !',
                            'count'=>$count,
                            'export_file'=>$itemListFile,
                            );
                return $data;
                break;
            }
        }
    }

    public function exportFileTree($bucketDomain,$basePath=null)
    {
        $cmdFiles=array();
        for($i=0;;$i++)
        {
            $itemListFile=$this->bucketItemListFilePrefix.'_'.$i.'.txt';
            if(!file_exists ($itemListFile))
            {
                break;
            }
            $item = file($itemListFile);
            $syncCmd='';
            foreach($item as $k=>$v)
            {
                if(strlen($v)<1)
                {
                    continue;//Sometimes ,Item name is empty;Skip It;
                }
                $v          = str_replace("\n","",$v);
                $path_info  = pathinfo($v);
                $base_path  = $basePath?$basePath:__DIR__;
                $local_path = str_replace('\\','/',$base_path.'/'.$path_info['dirname']);
                file_exists($local_path) || mkdir($local_path,0777,true);

                if(!file_exists($local_path.'/'.$path_info['basename']))//这里也许可以通过marker遍历位置替代
                {
                    $syncCmd.='wget \''.$bucketDomain.$v.'\' -O '.$local_path.'/'.$path_info['basename'].';'."\n";
                }

            }
            $cmdFIle=$itemListFile=$this->cmdFilePrefix.'_'.$i.'.txt';
            file_put_contents($cmdFIle,$syncCmd,FILE_APPEND);
            $cmdFiles[]=$cmdFIle;
        }
        return $cmdFiles;
    }

    public function syncBucketToLocal($bucketDomain,$basePath)
    {
        $this->exportBucketitem();
        $cmdFile=$this->exportFileTree($bucketDomain,$basePath);
        return $cmdFile;
    }

}
