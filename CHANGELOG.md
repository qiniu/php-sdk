## CHANGE LOG

### v6.0.1

2013-07-03 issue [#10](https://github.com/qiniu/api/pull/10)

- new Qiniu_RS_GetPolicy($expires = 0);
- new Qiniu_RS_PutPolicy($scope, $expires = 0);


### v6.0.0

2013-07-02 issue [#9](https://github.com/qiniu/api/pull/9)

- 遵循 [sdkspec v6.0.2](https://github.com/qiniu/sdkspec/tree/v6.0.2)
  - `Qiniu_Put/PutFile` 调整为基于 up.qiniu.com 的协议，extra *PutExtra 参数可以为 nil
  - `Qiniu_Put/PutFile` 支持支持 key = null (UNDEFINED_KEY)，这样服务端将自动生成 key 并返回
  - `Qiniu_Put/PutFile` 支持自定义的 "x:" 参数(io.PutExtra.Params)、支持 Crc 检查
  - 待增加：rsf, batch, resumable io 的支持
- bugfix: 修复 crc32 为负数的错误
- 增加 `Qiniu_RS_Put/PutFile` 辅助函数，用于服务端上传
