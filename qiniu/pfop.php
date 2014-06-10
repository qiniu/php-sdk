<?php
require_once('auth_digest.php');

// --------------------------------------------------------------------------------
// class Qiniu_ImageView

class Qiniu_Pfop {

    public $Bucket;
    public $Key;
    public $Fops;
    public $NotifyURL;
    public $Force;

    public function MakeRequest($self)
    {

        global $QINIU_API_HOST;

        $ReqParams = array(
            'bucket' => $this->Bucket,
            'key' => $this->Key,
            'fops' => $this->Fops,
            'notifyURL' => $this->NotifyURL,
            'force' => $this->Force
        );

        $url = $QINIU_API_HOST . '/pfop/';

        return Qiniu_Client_CallWithForm($self, $url, $ReqParams);
    }

}

function Qiniu_PfopStatus($client, $id)
{
    global $QINIU_API_HOST;

    $url = $QINIU_API_HOST . '/status/get/prefop?';
    $params = array("id" => $id);

    return Qiniu_Client_CallWithForm($client, $url, $params);
}



