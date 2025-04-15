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
     * 
     * @return array {name: string, mobile: string, idn: string, postcode: string, province: string, city: string, region: string, street: string} 
     */
    public static function parse(string $string, bool $user = true)
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
        $address = RegionMatcher::parseAddress($addr);
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
