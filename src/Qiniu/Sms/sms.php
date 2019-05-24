<?php
namespace Qiniu\Sms;

use Qiniu\Http\Client;
use Qiniu\Http\Error;
use Qiniu\Config;
use Qiniu\Auth;

class Sms
{
    private $auth;
    private $baseURL;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;

        $this->baseURL = sprintf("%s/%s/", Config::SMS_HOST, Config::SMS_VERSION);
    }

    /*
     * 创建签名
     * signature: string 类型，必填，
     * source: string	类型，必填，申请签名时必须指定签名来源。取值范围为：
        nterprises_and_institutions 企事业单位的全称或简称
        website 工信部备案网站的全称或简称
        app APP应用的全称或简称
        public_number_or_small_program 公众号或小程序的全称或简称
        store_name 电商平台店铺名的全称或简称
        trade_name 商标名的全称或简称，
     * pics: 本地的图片路径 string	 类型，可选
     */
    public function createSignature($signature, $source, $pics = null)
    {
        $params['signature'] = $signature;
        $params['source'] = $source;
        if (!empty($pics)) {
            $params['pics'] = $this->imgToBase64($pics);
        }
        $body = json_encode($params);
        $url =$this->baseURL."signature";
        $ret = $this->post($url, $body);
        return $ret;
    }

    /*
    * 编辑签名
    *  id 签名id
    * signature: string 类型，必填，
    * source: string	类型，必填，申请签名时必须指定签名来源。取值范围为：
        enterprises_and_institutions 企事业单位的全称或简称
        website 工信部备案网站的全称或简称
        app APP应用的全称或简称
        public_number_or_small_program 公众号或小程序的全称或简称
        store_name 电商平台店铺名的全称或简称
        trade_name 商标名的全称或简称，
    * pics: 本地的图片路径 string	 类型，可选，
    */
    public function updateSignature($id, $signature, $source, $pics = null)
    {
        $params['signature'] = $signature;
        $params['source'] = $source;
        if (!empty($pics)) {
            $params['pics'] = $this->imgToBase64($pics);
        }
        $body = json_encode($params);
        $url =$this->baseURL."signature/".$id;
        $ret = $this->PUT($url, $body);
        return $ret;
    }

    /*
 * 查询签名
 * audit_status: 审核状态 string 类型，可选， 
   取值范围为: "passed"(通过), "rejected"(未通过), "reviewing"(审核中)
 * page:页码 int  类型，
 * maxUsers：人数限制 ，可选，默认为 1
 * page_size: 分页大小 int 类型，可选， 默认为20
 */
    public function checkSignature($audit_status = null, $page = 1, $page_size = 20)
    {

        $url = sprintf(
            "%s?audit_status=%s&page=%s&page_size=%s",
            $this->baseURL."signature",
            $audit_status,
            $page,
            $page_size
        );
        $ret  = $this->get($url);
        return $ret;
    }


    /*
 * 删除签名
 * id 签名id
 */
    public function deleteSignature($id)
    {
        $url = $this->baseURL . 'signature/' . $id;
        list(, $err)  = $this->delete($url);
        return $err;
    }




    /*
    * 创建模板
    * name	: 模板名称 string 类型 ，必填
    * template:  模板内容 string  类型，必填
    * type: 模板类型 string 类型，必填，
      取值范围为: notification (通知类短信), verification (验证码短信), marketing (营销类短信)
    * description:  申请理由简述 string  类型，必填
    * signature_id:  已经审核通过的签名 string  类型，必填
    */
    public function createTemplate($name, $template, $type, $description, $signture_id)
    {
        $params['name'] = $name;
        $params['template'] = $template;
        $params['type'] = $type;
        $params['description'] = $description;
        $params['signature_id'] = $signture_id;

        $body = json_encode($params);
        var_dump($body);
        $url =$this->baseURL."template";
        $ret = $this->post($url, $body);
        return $ret;
    }

    /*
  * 查询模板
  * audit_status: 审核状态 string 类型 ，可选，
    取值范围为: passed (通过), rejected (未通过), reviewing (审核中)
  * page:  页码 int  类型，可选，默认为 1
  * page_size: 分页大小 int 类型，可选，默认为 20
  */
    public function queryTemplate($audit_status = null, $page = 1, $page_size = 20)
    {

        $url = sprintf(
            "%s?audit_status=%s&page=%s&page_size=%s",
            $this->baseURL."template",
            $audit_status,
            $page,
            $page_size
        );
        $ret  = $this->get($url);
        return $ret;
    }

    /*
    * 编辑模板
    * id :模板id
    * name	: 模板名称 string 类型 ，必填
    * template:  模板内容 string  类型，必填
    * description:  申请理由简述 string  类型，必填
    * signature_id:  已经审核通过的签名 string  类型，必填
    */
    public function updateTemplate($id, $name, $template, $description, $signature_id)
    {
        $params['name'] = $name;
        $params['template'] = $template;
        $params['description'] = $description;
        $params['signature_id'] = $signature_id;
        $body = json_encode($params);
        $url =$this->baseURL."template/".$id;
        $ret = $this->PUT($url, $body);
        return $ret;
    }

    /*
    * 删除模板
    * id :模板id
    */
    public function deleteTemplate($id)
    {
        $url = $this->baseURL . 'template/' . $id;
        list(, $err)  = $this->delete($url);
        return $err;
    }

    /*
    * 发送短信
    * 编辑模板
    * template_id :模板id string类型，必填
    * mobiles	: 手机号数组 []string 类型 ，必填
    * parameters:  模板内容 map[string]string	  类型，可选
    */
    public function sendMessage($template_id, $mobiles, array $parameters = null)
    {
        $params['template_id'] = $template_id;
        $params['mobiles'] = $mobiles;
        if (!empty($parameters)) {
            $params['parameters'] = $parameters;
        }
        $body = json_encode($params);
        $url =$this->baseURL."message";
        $ret = $this->post($url, $body);
        return $ret;
    }

    public function imgToBase64($img_file)
    {
        $img_base64 = '';
        if (file_exists($img_file)) {
            $app_img_file = $img_file; // 图片路径
            $img_info = getimagesize($app_img_file); // 取得图片的大小，类型等
            $fp = fopen($app_img_file, "r"); // 图片是否可读权限
            if ($fp) {
                $filesize = filesize($app_img_file);
                if ($filesize > 5*1024*1024) {
                    new Error("pic size < 5M !");
                }
                $content = fread($fp, $filesize);
                $file_content = chunk_split(base64_encode($content)); // base64编码
                switch ($img_info[2]) {           //判读图片类型
                    case 1:
                        $img_type = "gif";
                        break;
                    case 2:
                        $img_type = "jpg";
                        break;
                    case 3:
                        $img_type = "png";
                        break;
                }
                //合成图片的base64编码
                $img_base64 = 'data:image/' . $img_type . ';base64,' . $file_content;
            }
            fclose($fp);
        }

        return $img_base64;
    }

    private function get($url, $cType = null)
    {
        $rtcToken = $this->auth->authorizationV2($url, "GET", null, $cType);
        $rtcToken['Content-Type'] = $cType;
        $ret = Client::get($url, $rtcToken);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        return array($ret->json(), null);
    }

    private function delete($url, $contentType = 'application/json')
    {
        $rtcToken = $this->auth->authorizationV2($url, "DELETE", null, $contentType);
        $rtcToken['Content-Type'] = $contentType;
        $ret = Client::delete($url, $rtcToken);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        return array($ret->json(), null);
    }

    private function post($url, $body, $contentType = 'application/json')
    {
        $rtcToken = $this->auth->authorizationV2($url, "POST", $body, $contentType);
        $rtcToken['Content-Type'] = $contentType;
        $ret = Client::post($url, $body, $rtcToken);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        $r = ($ret->body === null) ? array() : $ret->json();
        return array($r, null);
    }
    private function PUT($url, $body, $contentType = 'application/json')
    {
        $rtcToken = $this->auth->authorizationV2($url, "PUT", $body, $contentType);
        $rtcToken['Content-Type'] = $contentType;
        $ret = Client::put($url, $body, $rtcToken);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        $r = ($ret->body === null) ? array() : $ret->json();
        return array($r, null);
    }
}
