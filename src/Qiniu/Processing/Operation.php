<?php
namespace Qiniu\Processing;

final class Operation
{
    public static function buildOp($cmd, $first_arg, array $args)
    {
        $op = array($cmd);
        if (!empty($first_arg)) {
            array_push($op, $first_arg);
        }
        foreach ($args as $key => $value) {
            array_push($op, "$key/$value");
        }
        return implode('/', $op);
    }

    public static function pipeCmd($cmds)
    {
        return implode('|', $cmds);
    }

    public static function saveas($op, $bucket, $key)
    {
        return self::pipeCmd(array($op, 'saveas/' . \Qiniu\entry($bucket, $key)));
    }
}
