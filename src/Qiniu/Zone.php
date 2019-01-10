<?php
namespace Qiniu;

use Qiniu\Region;

class Zone extends Region{

    public static function zone0()
    {
        return parent::Region0();
    }

    public static function zone1()
    {
        return parent::Region1();
    }

    public static function zone2()
    {
        return parent::Region2();
    }

    public static function zoneAs0()
    {
        return parent::RegionAs0();
    }

    public static function zoneNa0()
    {
        return parent::RegionNa0();
    }

    public static function zoneZ0()
    {
        return parent::RegionZ0();
    }

    public static function zoneZ1()
    {
        return parent::RegionZ1();
    }
}