<?php

namespace Hejunjie\AddressParser\Support;

use Hejunjie\ChinaDivision\Division;

class RegionMatcher
{
    /**
     * 解析地址字符串并根据规则提取省、市、区、街道信息
     * 
     * @param string $addr 需要解析的地址信息
     * @param string $unknownValue 未匹配到数据时填充内容
     * @param array $level1Data 一级行政区（省）数据（不传递则使用默认数据源）
     * @param array $level2Data 二级行政区（市）数据（不传递则使用默认数据源）
     * @param array $level3Data 三级行政区（区/县）数据（不传递则使用默认数据源）
     * 
     * @return array 
     *
     * 说明：
     * - 数据源来自 \Hejunjie\ChinaDivision\Division::getCityLevels()
     * - 格式为：['id'=>['name'=>'城市名称','pid'=>'上级ID，省级为0']]
     * - 详见：https://github.com/zxc7563598/php-china-division
     */
    public static function parseAddress(string $addr, string $unknownValue = '未知', array $level1Data = [], array $level2Data = [], array $level3Data = []): array
    {
        // Step 1: 清洗地址字符串
        $addr_origin = $addr;
        $addr = str_replace([' ', ','], ['', ''], $addr);
        $addr = str_replace('自治区', '省', $addr);
        $addr = str_replace('自治州', '州', $addr);
        $addr = str_replace('小区', '', $addr);
        $addr = str_replace('校区', '', $addr);
        // 初始化变量
        $a2 = $a3 = '';
        $street = '';
        // Step 2: 根据地址确定三级区域（省、市、区）
        // 判断县、区、旗的存在
        if (
            mb_strpos($addr, '县') !== false && mb_strpos($addr, '县') < floor((mb_strlen($addr) / 3) * 2) ||
            (mb_strpos($addr, '区') !== false && mb_strpos($addr, '区') < floor((mb_strlen($addr) / 3) * 2)) ||
            mb_strpos($addr, '旗') !== false && mb_strpos($addr, '旗') < floor((mb_strlen($addr) / 3) * 2)
        ) {
            // 匹配区、县或旗
            if (mb_strstr($addr, '旗')) {
                $deep3_keyword_pos = mb_strpos($addr, '旗');
                $a3 = mb_substr($addr, $deep3_keyword_pos - 1, 2);
            }
            if (mb_strstr($addr, '区')) {
                $deep3_keyword_pos = mb_strpos($addr, '区');
                if (mb_strstr($addr, '市')) {
                    $city_pos = mb_strpos($addr, '市');
                    $zone_pos = mb_strpos($addr, '区');
                    $a3 = mb_substr($addr, $city_pos + 1, $zone_pos - $city_pos);
                } else {
                    $a3 = mb_substr($addr, $deep3_keyword_pos - 2, 3);
                }
            }
            if (mb_strstr($addr, '县')) {
                $deep3_keyword_pos = mb_strpos($addr, '县');
                if (mb_strstr($addr, '市')) {
                    $city_pos = mb_strpos($addr, '市');
                    $zone_pos = mb_strpos($addr, '县');
                    $a3 = mb_substr($addr, $city_pos + 1, $zone_pos - $city_pos);
                } else {
                    if (mb_strstr($addr, '自治县')) {
                        $a3 = mb_substr($addr, $deep3_keyword_pos - 6, 7);
                        if (in_array(mb_substr($a3, 0, 1), ['省', '市', '州'])) {
                            $a3 = mb_substr($a3, 1);
                        }
                    } else {
                        $a3 = mb_substr($addr, $deep3_keyword_pos - 2, 3);
                    }
                }
            }
            // 街道部分
            $street = mb_substr($addr_origin, $deep3_keyword_pos + 1);
        } else {
            // 处理没有区、县、旗的情况
            if (mb_strripos($addr, '市')) {
                $deep3_keyword_pos = mb_strripos($addr, '市');
                $a3 = mb_substr($addr, $deep3_keyword_pos - 2, 3);
                $street = mb_substr($addr_origin, $deep3_keyword_pos + 1);
            } else {
                $a3 = '';
                $street = $addr;
            }
        }
        // Step 3: 确定市
        if (mb_strpos($addr, '市') || mb_strstr($addr, '盟') || mb_strstr($addr, '州')) {
            if ($tmp_pos = mb_strpos($addr, '市')) {
                $a2 = mb_substr($addr, $tmp_pos - 2, 3);
            } else if ($tmp_pos = mb_strpos($addr, '盟')) {
                $a2 = mb_substr($addr, $tmp_pos - 2, 3);
            } else if ($tmp_pos = mb_strpos($addr, '州')) {
                if ($tmp_pos = mb_strpos($addr, '自治州')) {
                    $a2 = mb_substr($addr, $tmp_pos - 4, 5);
                } else {
                    $a2 = mb_substr($addr, $tmp_pos - 2, 3);
                }
            }
        } else {
            $a2 = '';
        }
        // Step 4: 智能解析省市区信息
        if (count($level1Data) == 0 || count($level2Data) == 0 || count($level3Data) == 0) {
            $level_data = Division::getCityLevels();
            $level1Data = $level_data['level_1'];
            $level2Data = $level_data['level_2'];
            $level3Data = $level_data['level_3'];
        }
        $parseParsedAddress = self::parseParsedAddress($a2, $a3, $unknownValue, $level1Data, $level2Data, $level3Data);
        // Step 5: 合并结果
        $parseParsedAddress['street'] = $street;

        return $parseParsedAddress;
    }

    /**
     * 解析标准化地址信息，返回省市区名称。
     *
     * @param string $cityInput 大概率是城市的二级地址
     * @param string $regionInput 大概率是区县的三级地址
     * @param string $unknownValue 未匹配到数据时填充内容
     * @param array $level1Data 一级行政区（省）数据
     * @param array $level2Data 二级行政区（市）数据
     * @param array $level3Data 三级行政区（区/县）数据
     * 
     * @return array 包含 province（省）、city（市）、region（区/县）的地址信息
     *
     * 说明：
     * - 优先通过区县匹配确定地址；
     * - 如果匹配不唯一，再结合城市/省份进行辅助判断；
     * - 若区县无法匹配，则回退到城市或省份级别；
     * - 所有字段默认值均为“未知”，确保结果结构一致。
     */
    public static function parseParsedAddress(
        string $cityInput,
        string $regionInput,
        string $unknownValue,
        array $level1Data,
        array $level2Data,
        array $level3Data
    ): array {
        $result = [
            'city' => $unknownValue,
            'region' => $unknownValue,
            'province' => $unknownValue
        ];
        if ($regionInput === '') {
            return $result;
        }
        $regionMatches = array_filter($level3Data, fn($v) => mb_strpos($v['name'], $regionInput) !== false);
        if (count($regionMatches) > 1) {
            $result = self::handleMultipleRegionMatches($regionMatches, $cityInput, $unknownValue, $level1Data, $level2Data, $regionInput);
        } elseif (count($regionMatches) === 1) {
            $result = self::handleSingleRegionMatch(array_values($regionMatches)[0], $unknownValue, $level1Data, $level2Data);
        } else {
            $result = self::handleNoRegionMatch($cityInput, $regionInput, $unknownValue, $level1Data, $level2Data);
        }
        return $result;
    }

    /**
     * 处理当区/县匹配结果为多个时的情况。
     *
     * @param array $regionMatches 多个匹配到的三级行政区
     * @param string $cityInput 用户输入的城市名
     * @param string $unknownValue 未匹配到数据时填充内容
     * @param array $level1Data 一级行政区（省）数据
     * @param array $level2Data 二级行政区（市）数据
     * @param string $regionInput 用户输入的区县名
     * @return array 包含 province、city、region 的推断地址信息
     *
     * 说明：
     * - 当区县不唯一时，尝试通过城市匹配精确定位；
     * - 若城市也不唯一，再尝试通过省份缩小范围；
     * - 若省份也无法唯一定位，则尽量保留用户原始输入作为结果。
     */
    private static function handleMultipleRegionMatches(array $regionMatches, string $cityInput, string $unknownValue, array $level1Data, array $level2Data, string $regionInput): array
    {
        $result = [
            'province' => $unknownValue,
            'city' => $unknownValue,
            'region' => $unknownValue
        ];
        $cityMatches = array_filter($level2Data, fn($v) => mb_strpos($v['name'], $cityInput) !== false);
        if (count($cityMatches) > 1) {
            foreach ($regionMatches as $regionMatch) {
                foreach ($cityMatches as $id => $city) {
                    if ($regionMatch['pid'] == $id) {
                        return [
                            'province' => $level1Data[$city['pid']]['name'] ?? $unknownValue,
                            'city' => $city['name'],
                            'region' => $regionMatch['name']
                        ];
                    }
                }
            }
        }
        if (count($cityMatches) === 1) {
            foreach ($regionMatches as $regionMatch) {
                if (isset($cityMatches[$regionMatch['pid']])) {
                    $city = $cityMatches[$regionMatch['pid']];
                    return [
                        'province' => $level1Data[$city['pid']]['name'] ?? $unknownValue,
                        'city' => $city['name'],
                        'region' => $regionMatch['name']
                    ];
                }
            }
        }
        if (count($cityMatches) === 0) {
            $provinceMatches = array_filter($level1Data, fn($v) => mb_strpos($v['name'], $cityInput) !== false);
            if (count($provinceMatches) > 1) {
                foreach ($regionMatches as $regionMatch) {
                    $pidPrefix = substr($regionMatch['pid'], 0, 2);
                    foreach ($provinceMatches as $id => $province) {
                        if ($pidPrefix == $id) {
                            return [
                                'province' => $province['name'],
                                'city' => $level2Data[$regionMatch['pid']]['name'] ?? '',
                                'region' => $regionMatch['name']
                            ];
                        }
                    }
                }
            }
            if (count($provinceMatches) === 1) {
                foreach ($regionMatches as $regionMatch) {
                    $pidPrefix = substr($regionMatch['pid'], 0, 2);
                    if (isset($provinceMatches[$pidPrefix])) {
                        return [
                            'province' => $provinceMatches[$pidPrefix]['name'],
                            'city' => $level2Data[$regionMatch['pid']]['name'] ?? '',
                            'region' => $regionMatch['name']
                        ];
                    }
                }
            }
            if (count($provinceMatches) === 0) {
                return [
                    'province' => $unknownValue,
                    'city' => $cityInput,
                    'region' => $regionInput
                ];
            }
        }
        return $result;
    }

    /**
     * 处理当区/县匹配结果唯一时的情况。
     *
     * @param array $region 唯一匹配到的三级行政区数据
     * @param string $unknownValue 未匹配到数据时填充内容
     * @param array $level1Data 一级行政区（省）数据
     * @param array $level2Data 二级行政区（市）数据
     * @return array 包含 province、city、region 的地址信息
     *
     * 说明：
     * - 通过区县的上级 city ID 找到城市；
     * - 再通过城市的上级 province ID 找到省份；
     * - 若 city 或 province 缺失，使用默认值（如“市辖区”或“未知”）补充。
     */
    private static function handleSingleRegionMatch(array $region, string $unknownValue, array $level1Data, array $level2Data): array
    {
        $cityId = $region['pid'];
        if (isset($level2Data[$cityId])) {
            $city = $level2Data[$cityId];
            $province = $level1Data[$city['pid']] ?? null;
            return [
                'province' => $province['name'] ?? $unknownValue,
                'city' => $city['name'],
                'region' => $region['name']
            ];
        }
        $province = $level1Data[substr($cityId, 0, 2)] ?? null;
        return [
            'province' => $province['name'] ?? $unknownValue,
            'city' => '',
            'region' => $region['name']
        ];
    }

    /**
     * 处理当区/县无法匹配时的情况。
     *
     * @param string $cityInput 用户输入的城市字段
     * @param string $regionInput 用户输入的区县字段
     * @param string $unknownValue 未匹配到数据时填充内容
     * @param array $level1Data 一级行政区（省）数据
     * @param array $level2Data 二级行政区（市）数据
     * @return array 包含 province、city、region 的地址信息
     *
     * 说明：
     * - 区县找不到匹配时，尝试通过城市匹配确定地址；
     * - 若城市也无匹配，再尝试匹配省份；
     * - 若仍然无匹配，回退使用原始输入，确保结构完整。
     */
    private static function handleNoRegionMatch(string $cityInput, string $regionInput, string $unknownValue, array $level1Data, array $level2Data): array
    {
        $cityMatches = array_filter($level2Data, fn($v) => mb_strpos($v['name'], $cityInput) !== false);

        if (count($cityMatches) > 0) {
            $first = array_values($cityMatches)[0];
            return [
                'province' => $level1Data[$first['pid']]['name'] ?? $unknownValue,
                'city' => $first['name'],
                'region' => $regionInput
            ];
        }

        $provinceMatches = array_filter($level1Data, fn($v) => mb_strpos($v['name'], $cityInput) !== false);

        if (count($provinceMatches) > 0) {
            foreach ($provinceMatches as $id => $province) {
                $citiesUnderProvince = array_filter($level2Data, fn($v) => $v['pid'] == $id);
                return [
                    'province' => $province['name'],
                    'city' => count($citiesUnderProvince) === 0 ? '' : $unknownValue,
                    'region' => $regionInput
                ];
            }
        }

        return [
            'province' => $unknownValue,
            'city' => $cityInput,
            'region' => $regionInput
        ];
    }
}
