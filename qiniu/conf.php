<?php
/**
 * <peichao01@17chj.com> 我在使用的时候无法在其他php文件的函数中使用此页的这几个变量。因为我非主职PHP的，经查证，全局变量使用前需要前声明才行。
 * 如此这般之后，我就可以正常的按照php-sdk-doc 的指南来正常使用了。
 * 先提交，望大家审核。
 */
global $QINIU_UP_HOST;
global $QINIU_RS_HOST;
global $QINIU_RSF_HOST;
 
global $QINIU_ACCESS_KEY;
global $QINIU_SECRET_KEY;

$QINIU_UP_HOST	= 'http://up.qiniu.com';
$QINIU_RS_HOST	= 'http://rs.qbox.me';
$QINIU_RSF_HOST	= 'http://rsf.qbox.me';

$QINIU_ACCESS_KEY	= '<Please apply your access key>';
$QINIU_SECRET_KEY	= '<Dont send your secret key to anyone>';

