<?php

declare(strict_types = 1);

namespace CloudCastle\XmlConvertor;

use stdClass;
use SimpleXMLElement;
use CloudCastle\FileSystem\File;
use CloudCastle\FileSystem\Json;

/**
 * Класс GetXml
 * @version 0.0.1
 * @package CloudCastle\XmlConvertor
 * @generated Зорин Алексей, please DO NOT EDIT!
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * @copyright 2022 разработчик Зорин Алексей Евгеньевич.
 */
final class Get
{

    /**
     * Получить xml из файла или строки
     * 
     * @param string $data Путь к файлу или Строка xml
     * @return SimpleXMLElement
     */
    public static function getToXml(string $data): SimpleXMLElement
    {
        $info = File::info($data);
        if ($info->extension === 'xml') {
            return new SimpleXMLElement(File::read($data));
        } elseif (preg_match('~^(<?xml)(.+)?>(.+)?~ui', $data)) {
            return new SimpleXMLElement($data);
        }
        return new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>');
    }

    /**
     * Получить объект stdClass из файла, объекта, массива или строки json
     * 
     * @param mixed $data
     * @return stdClass
     */
    public static function getToJson(mixed $data): stdClass
    {
        if (File::has($data)) {
            return Json::read($data);
        } elseif (is_object($data) OR is_array($data)) {
            return json_decode(json_encode($data));
        } elseif ($json = json_decode($data)) {
            return $json;
        }
        return new stdClass();
    }

}
