<?php

namespace Hejunjie\AddressParser\Support;

class UserInfoExtractor
{

    private static $keywords = ['收货地址', '详细地址', '地址', '收货人', '收件人', '收货', '所在地区', '邮编', '电话', '手机号码', '身份证号码', '身份证号', '身份证', '：', ':', '；', ';', '，', ',', '。'];

    /**
     * 提取手机号、姓名等信息
     * 
     * @param string $raw 原始地址字符串
     * 
     * @return array {
     *     name: string|null,
     *     mobile: string|null,
     *     idn: string|null,
     *     postcode: string|null,
     *     addr: string
     * }
     */
    public static function extractUserInfo(string $raw): array
    {
        // 替换常见的字段标签为统一空格
        $cleaned = str_replace(self::$keywords, ' ', $raw);
        // 清理多余空格
        $cleaned = preg_replace('/\s+/', ' ', $cleaned) ?? '';
        $cleaned = trim($cleaned);
        $result = [
            'name' => '',
            'mobile' => '',
            'idn' => '',
            'postcode' => '',
            'addr' => ''
        ];
        // 提取身份证号
        if (preg_match('/\b\d{17}[0-9Xx]|\d{18}\b/', $cleaned, $matches)) {
            $result['idn'] = strtoupper($matches[0]);
            $cleaned = str_replace($matches[0], '', $cleaned);
        }
        // 提取手机号或座机
        if (preg_match('/\b\d{7,11}[\-_]\d{2,6}|\d{3,4}-\d{6,8}|\d{7,11}\b/', $cleaned, $matches)) {
            $result['mobile'] = $matches[0];
            $cleaned = str_replace($matches[0], '', $cleaned);
        }
        // 提取邮编
        if (preg_match('/\b\d{6}\b/', $cleaned, $matches)) {
            $result['postcode'] = $matches[0];
            $cleaned = str_replace($matches[0], '', $cleaned);
        }
        // 再清理一次空格
        $cleaned = preg_replace('/\s+/', ' ', trim($cleaned)) ?? '';
        // 提取姓名（长度最短的词）
        $parts = explode(' ', $cleaned);
        if (count($parts) > 1) {
            // 选择最短的非空词作为姓名
            $result['name'] = array_reduce($parts, function ($shortest, $item) {
                if (empty($shortest) || (mb_strlen($item) < mb_strlen($shortest))) {
                    return $item;
                }
                return $shortest;
            }, '');
            $cleaned = trim(str_replace($result['name'], '', $cleaned));
        }
        $result['addr'] = $cleaned;
        return $result;
    }
}
