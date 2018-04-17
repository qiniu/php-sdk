<?php
namespace Qiniu\Rtc;

class AppClient
{
    private $_transport;
    private $_mac;
    private $_baseURL;

    public function __construct($mac)
    {
        $this->_mac = $mac;
        $this->_transport = new Transport($mac);

        $cfg = Config::getInstance();
        $this->_baseURL = sprintf("%s/%s/apps", $cfg->RTCAPI_HOST, $cfg->RTCAPI_VERSION);
    }

    /*
     * hub: 直播空间名
     * title: app 的名称  注意，Title 不是唯一标识，重复 create 动作将生成多个 app
     * NoAutoCloseRoom: bool 类型，可选，禁止自动关闭房间。默认为 false ，即用户退出房间后，房间会被主动清理释放。
     * NoAutoCreateRoom: bool 类型，可选，禁止自动创建房间。默认为 false ，即不需要主动调用接口创建即可加入房间。
     * NoAutoKickUser: bool 类型，可选，禁止自动踢人（抢流）。默认为 false ，即同一个身份的 client (app/room/user) ，新的连麦请求可以成功，旧连接被关闭。
     */
    public function createApp($hub, $title, $maxUsers = null, $noAutoCloseRoom = null, $noAutoCreateRoom = null, $noAutoKickUser = null)
    {
        $params['hub'] = $hub;
        $params['title'] = $title;
        if (!empty($maxUsers)) {
            $params['maxUsers'] = $maxUsers;
        }
        if (!empty($noAutoCloseRoom)) {
            $params['noAutoCloseRoom'] = $noAutoCloseRoom;
        }
        if (!empty($noAutoCreateRoom)) {
            $params['noAutoCreateRoom'] = $noAutoCreateRoom;
        }
        if (!empty($noAutoKickUser)) {
            $params['noAutoKickUser'] = $noAutoKickUser;
        }
        $body = json_encode($params);
        try {
            $ret = $this->_transport->send(HttpRequest::POST, $this->_baseURL, $body);
        } catch (\Exception $e) {
            return $e;
        }
        return $ret;
    }

    /*
     * appId: app 的唯一标识，创建的时候由系统生成。
     * Title: app 的名称， 可选。
     * Hub: 绑定的直播 hub，可选，用于合流后 rtmp 推流。
     * MaxUsers: int 类型，可选，连麦房间支持的最大在线人数。
     * NoAutoCloseRoom: bool 指针类型，可选，true 表示禁止自动关闭房间。
     * NoAutoCreateRoom: bool 指针指型，可选，true 表示禁止自动创建房间。
     * NoAutoKickUser: bool 类型，可选，禁止自动踢人。
     * MergePublishRtmp: 连麦合流转推 RTMP 的配置，可选择。其详细配置包括如下
            Enable: 布尔类型，用于开启和关闭所有房间的合流功能。
            AudioOnly: 布尔类型，可选，指定是否只合成音频。
            Height, Width: int64，可选，指定合流输出的高和宽，默认为 640 x 480。
            OutputFps: int64，可选，指定合流输出的帧率，默认为 25 fps 。
            OutputKbps: int64，可选，指定合流输出的码率，默认为 1000 。
            URL: 合流后转推旁路直播的地址，可选，支持魔法变量配置按照连麦房间号生成不同的推流地址。如果是转推到七牛直播云，不建议使用该配置。
            StreamTitle: 转推七牛直播云的流名，可选，支持魔法变量配置按照连麦房间号生成不同的流名。例如，配置 Hub 为 qn-zhibo ，配置 StreamTitle 为 $(roomName) ，则房间 meeting-001 的合流将会被转推到 rtmp://pili-publish.qn-zhibo.***.com/qn-zhibo/meeting-001地址。详细配置细则，请咨询七牛技术支持。
     */
    public function updateApp($appId, $hub, $title, $maxUsers = null, $mergePublishRtmp = null, $noAutoCloseRoom = null, $noAutoCreateRoom = null, $noAutoKickUser = null)
    {
        $url = $this->_baseURL . '/' . $appId;
        $params['hub'] = $hub;
        $params['title'] = $title;
        if (!empty($maxUsers)) {
            $params['maxUsers'] = $maxUsers;
        }
        if (!empty($noAutoCloseRoom)) {
            $params['noAutoCloseRoom'] = $noAutoCloseRoom;
        }
        if (!empty($noAutoCreateRoom)) {
            $params['noAutoCreateRoom'] = $noAutoCreateRoom;
        }
        if (!empty($noAutoKickUser)) {
            $params['noAutoKickUser'] = $noAutoKickUser;
        }
        if (!empty($mergePublishRtmp)) {
            $params['mergePublishRtmp'] = $mergePublishRtmp;
        }
        $body = json_encode($params);
        try {
            $ret = $this->_transport->send(HttpRequest::POST, $url, $body);
        } catch (\Exception $e) {
            return $e;
        }
        return $ret;
    }

    /*
     * appId: app 的唯一标识，创建的时候由系统生成。
     */
    public function getApp($appId)
    {
        $url = $this->_baseURL . '/' . $appId;
        try {
            $ret = $this->_transport->send(HttpRequest::GET, $url);
        } catch (\Exception $e) {
            return $e;
        }
        return $ret;
    }

    /*
     * appId: app 的唯一标识，创建的时候由系统生成
     */
    public function deleteApp($appId)
    {
        $url = $this->_baseURL . '/' . $appId;
        try {
            $ret = $this->_transport->send(HttpRequest::DELETE, $url);
        } catch (\Exception $e) {
            return $e;
        }
        return $ret;
    }

    /*
     * 获取房间的人数
     * appId: app 的唯一标识，创建的时候由系统生成。
     * roomName: 操作所查询的连麦房间。
     */
    public function getappUserNum($appId, $roomName)
    {
        $url = sprintf("%s/%s/rooms/%s/users", $this->_baseURL, $appId, $roomName);
        try {
            $ret = $this->_transport->send(HttpRequest::GET, $url);
        } catch (\Exception $e) {
            return $e;
        }
        return $ret;
    }

   /*
    * 踢出玩家
    * appId: app 的唯一标识，创建的时候由系统生成。
    * roomName: 连麦房间
    * userId: 请求加入房间的用户ID
    */
    public function kickingPlayer($appId, $roomName, $userId)
    {
        $url = sprintf("%s/%s/rooms/%s/users/%s", $this->_baseURL, $appId, $roomName, $UserId);
        try {
            $ret = $this->_transport->send(HttpRequest::DELETE, $url);
        } catch (\Exception $e) {
            return $e;
        }
        return $ret;
    }

    /*
     * 获取房间的人数
     * appId: app 的唯一标识，创建的时候由系统生成。
     * prefix: 所查询房间名的前缀索引，可以为空。
     * offset: int 类型，分页查询的位移标记。
     * limit: int 类型，此次查询的最大长度。
     * GET /v3/apps/<AppID>/rooms?prefix=<RoomNamePrefix>&offset=<Offset>&limit=<Limit>
     */
    public function listRooms($appId, $prefix, $offset, $limit)
    {
        $url = sprintf("%s/%s/rooms", $this->_baseURL, $appId);
        try {
            $ret = $this->_transport->send(HttpRequest::GET, $url);
        } catch (\Exception $e) {
            return $e;
        }
        return $ret;
    }

    /*
     * appId: app 的唯一标识，创建的时候由系统生成。
     * roomName: 房间名称，需满足规格 ^[a-zA-Z0-9_-]{3,64}$
     * userId: 请求加入房间的用户 ID，需满足规格 ^[a-zA-Z0-9_-]{3,50}$
     * expireAt: int64 类型，鉴权的有效时间，传入以秒为单位的64位Unix绝对时间，token 将在该时间后失效。
     * permission: 该用户的房间管理权限，"admin" 或 "user"，默认为 "user" 。当权限角色为 "admin" 时，拥有将其他用户移除出房间等特权.
     */
    public function appToken($appId, $roomName, $userId, $expireAt, $permission)
    {
        $params['appId'] = $appId;
        $params['userId'] = $userId;
        $params['roomName'] = $roomName;        
        $params['permission'] = $permission;
        $params['expireAt'] = $expireAt;
        $appAccessString = json_encode($params);
        $encodedappAccess = Utils::base64UrlEncode($appAccessString);
        $sign = hash_hmac('sha1', $encodedappAccess, $this->_mac->_secretKey, true);
        $encodedSign = Utils::base64UrlEncode($sign);
        return $this->_mac->_accessKey . ":" . $encodedSign . ":" . $encodedappAccess;
    }
}
