<?php

declare(strict_types = 1);

namespace CloudCastle\Convertor;

use stdClass;
use CloudCastle\Convertor\Get;
use CloudCastle\XmlGenerator\Xml;
use CloudCastle\XmlGenerator\Config;
use CloudCastle\XmlGenerator\XmlGenerator;

/**
 * Класс Convertor
 * @version 0.0.1
 * @package CloudCastle\Convertor
 * @generated Зорин Алексей, please DO NOT EDIT!
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * @copyright 2022 разработчик Зорин Алексей Евгеньевич.
 */
final class Convertor
{

    /**
     * Конвертировать содержимое xml файла или xml строку в строку json
     * @param string $data Путь к xml файлу или xml строка
     * @return string Строка json
     */
    public static function xmlToJson(string $data): string
    {
        $xml = Get::getToXml($data);
        return json_encode($xml);
    }

    /**
     * Сконвертировать содержание json файла, массива, объекта или json строки в строку xml
     * @param mixed $data
     * @return Xml
     */
    public static function jsonToXml(mixed $data):Xml
    {
        $json = Get::getToJson($data);
        $xml = self::startDecodeToXml($json);
        return $xml;
    }

    /**
     * Запустить конвертацию объекта в xml
     *
     * @param stdClass $data Объект который необходимо конвертировать в xml
     * @return Xml
     */
    private static function startDecodeToXml(stdClass $data):Xml
    {
        $config = new Config();
        $config->setType('filesystem');
        $xmlGenerator = new XmlGenerator($config);
        $xmlGenerator->startDocument();
        self::createXmlStructure($data, $xmlGenerator);
        return $xmlGenerator->get();
    }

    /**
     * Создать структуру XML документа
     *
     * @param stdClass|array $data
     * @param XmlGenerator $xmlGenerator
     * @return void
     */
    private static function createXmlStructure($data, XmlGenerator $xmlGenerator): void
    {
        foreach ($data as $key => $value) {
            $attributes = self::getAttrubutes($data, $key);
            if ((is_string($value) OR is_int($value) OR is_bool($value)) AND $key !== '@attributes') {
                $xmlGenerator->startElement($key);
                self::setAttrubutes($attributes, $xmlGenerator);
                $xmlGenerator->text($value);
                $xmlGenerator->closeElement();
            } elseif (is_object($value) AND $key !== '@attributes') {
                $xmlGenerator->startElement($key);
                self::setAttrubutes($attributes, $xmlGenerator);
                self::createXmlStructure($value, $xmlGenerator);
                $xmlGenerator->closeElement();
            } elseif (is_array($value)) {
                self::setDataToArray($value,$key, $xmlGenerator);
            }
        }
    }

    /**
     * Получить атрибуты элемента
     *
     * @param stdClass|array $data Объект или массив объектов, у которого необходимо получить атрибуты
     * @param string|int $key Ключ объекта по которому необходимо получить атрибуты
     * @return array
     */
    private static function getAttrubutes($data, $key): array
    {
        $attributes = [];
        if (isset($data->$key->{'@attributes'})) {
            $attributes = $data->$key->{'@attributes'};
        }
        return (array)$attributes;
    }

    /**
     * Установить атрибуты элемента
     *
     * @param array $attributes Массив атрибутов
     * @param XmlGenerator $xmlGenerator Объект генератора XML структуры
     * @return void
     */
    private static function setAttrubutes(array $attributes, XmlGenerator $xmlGenerator): void
    {
        if ($attributes) {
            foreach ($attributes as $attrName => $attrValue) {
                $xmlGenerator->addAttribute($attrName, $attrValue);
            }
        }
    }

    /**
     * Дополнить структуру XML из массива
     *
     * @param array $data Массив значений для наполнения
     * @param string|int $elementName Наименование элемента для наполнения
     * @param XmlGenerator $xmlGenerator Объект генератора XML структуры
     * @return void
     */
    public static function setDataToArray(array $data, $elementName, XmlGenerator $xmlGenerator):void
    {
        foreach ($data as $item) {
            $xmlGenerator->startElement($elementName);
            self::createXmlStructure($item, $xmlGenerator);
            $xmlGenerator->closeElement();
        }
    }

}
