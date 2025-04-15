<?php

namespace Hejunjie\AddressParser;

use Hejunjie\AddressParser\Support\RegionMatcher;
use Hejunjie\AddressParser\Support\UserInfoExtractor;

class AddressParser
{

    /**
     * 解析地址
     * @param mixed $string 地址信息
     * @param bool $user 信息中是否包含用户信息
     * @param string $unknownValue 未匹配到数据时填充内容
     * @param array $level1Data 一级行政区（省）数据（不传递则使用默认数据源）
     * @param array $level2Data 二级行政区（市）数据（不传递则使用默认数据源）
     * @param array $level3Data 三级行政区（区/县）数据（不传递则使用默认数据源）
     * 
     * @return array {name: string, mobile: string, idn: string, postcode: string, province: string, city: string, region: string, street: string} 
     *
     * 说明：
     * - 省市区数据源来自 \Hejunjie\ChinaDivision\Division::getCityLevels()
     * - 格式为：['id'=>['name'=>'城市名称','pid'=>'上级ID，省级为0']]
     * - 详见：https://github.com/zxc7563598/php-china-division
     */
    public static function parse(string $string, bool $user = true, $unknownValue = '未知', array $level1Data = [], array $level2Data = [], array $level3Data = [])
    {
        // 默认数据
        $data = [
            'name' => '',
            'mobile' => '',
            'idn' => '',
            'postcode' => '',
            'province' => '',
            'city' => '',
            'region' => '',
            'street' => ''
        ];
        // 提取用户信息（如果需要）
        if ($user) {
            $extractUserInfo = UserInfoExtractor::extractUserInfo($string);
            $data = array_merge($data, [
                'name' => $extractUserInfo['name'],
                'mobile' => $extractUserInfo['mobile'],
                'idn' => $extractUserInfo['idn'],
                'postcode' => $extractUserInfo['postcode']
            ]);
            $addr = $extractUserInfo['addr'];
        } else {
            $addr = $string;
        }
        // 解析地址
        $address = RegionMatcher::parseAddress($addr, $unknownValue, $level1Data, $level2Data, $level3Data);
        // 更新地址信息
        $data['province'] = $address['province'];
        $data['city'] = $address['city'];
        $data['region'] = $address['region'];
        $data['street'] = trim(str_replace(
            [$data['region'], $data['city'], $data['province']],
            ['', '', ''],
            $address['street'] ?? ''
        ));
        // 返回数据
        return $data;
    }
}
