## CHANGE LOG

### v6.1.4

2013-10-25 issues [#52](https://github.com/qiniu/php-sdk/pull/52)

- PutPolicy: 增加 saveKey、persistentOps/persistentNotifyUrl、fsizeLimit 等支持


### v6.1.3

2013-10-09 issues [#50](https://github.com/qiniu/php-sdk/pull/50)

- 断点续上传使用mkfile
- 修订文档
- 消除测试用例并发问题


### v6.1.2

2013-09-24 issue [#40](https://github.com/qiniu/php-sdk/pull/40)

- 解决与某些 PHP 框架不兼容问题（主要是全局变量的定义）
- 改善 `json_decode` 的错误提示（有可能 `json_last_error_msg` 函数不存在）


### v6.1.1

2013-07-04 issue [#24](https://github.com/qiniu/php-sdk/pull/24)

- 支持断点续上传(`Qiniu_RS_Rput`, `Qiniu_RS_RputFile`)


### v6.1.0

2013-07-04 issue [#22](https://github.com/qiniu/php-sdk/pull/22)

- hotfix: 修复上传的时候 key 中不能出现 '/' 的错误


### v6.0.2

2013-07-04 issue [#20](https://github.com/qiniu/php-sdk/pull/20)

- 增加 rsf, batch 支持
- 初步补充文档


### v6.0.1

2013-07-03 issue [#10](https://github.com/qiniu/php-sdk/pull/10)

- new Qiniu_RS_GetPolicy($expires = 0);
- new Qiniu_RS_PutPolicy($scope, $expires = 0);


### v6.0.0

2013-07-02 issue [#9](https://github.com/qiniu/php-sdk/pull/9)

- 遵循 [sdkspec v6.0.2](https://github.com/qiniu/sdkspec/tree/v6.0.2)
  - `Qiniu_Put/PutFile` 调整为基于 up.qiniu.com 的协议，extra *PutExtra 参数可以为 nil
  - `Qiniu_Put/PutFile` 支持支持 key = null (UNDEFINED_KEY)，这样服务端将自动生成 key 并返回
  - `Qiniu_Put/PutFile` 支持自定义的 "x:" 参数(io.PutExtra.Params)、支持 Crc 检查
  - 待增加：rsf, batch, resumable io 的支持
- bugfix: 修复 crc32 为负数的错误
- 增加 `Qiniu_RS_Put/PutFile` 辅助函数，用于服务端上传
