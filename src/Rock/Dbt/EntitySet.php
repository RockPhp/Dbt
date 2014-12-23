<?php

class Rock_Dbt_EntitySet
{

    private static function checkEntity(ReflectionClass $reflectionClass, stdClass $dbObj)
    {
        $properties = $reflectionClass->getProperties();
        $arrayTmp = array();
        foreach ($properties as $v) {
            if ($v->isPublic()) {
                throw new Exception('Atributo [' . $v->name . '] da Entidade [' . $reflectionClass->getName() . '] publico');
            }
            $arrayTmp[] = $v->name;
        }
        foreach ($dbObj as $k => $v) {
            if (! in_array($k, $arrayTmp)) {
                throw new Exception('Atributo [' . $k . '] nao existe da Entidade [' . $reflectionClass->getName() . ']');
            }
        }
    }

    public static function setPk(Rock_Dbt_EntityBase $entity, $arrayPk)
    {
        $entity->__setPk($arrayPk);
    }

    public static function fill(Rock_Dbt_EntityBase $entity, stdClass $dbObj)
    {
        $reflection = new ReflectionClass($entity);
        self::checkEntity($reflection, $dbObj);
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            self::fillGreaterThan53($entity, $dbObj);
        } else {
            self::fillLowerThan53($entity, $dbObj);
        }
    }

    private static function fillGreaterThan53(Rock_Dbt_EntityBase $entity, stdClass $dbObj)
    {
        $reflection = new ReflectionClass($entity);
        $encoding = Rock_Dbt_EntityGet::getEncoding($entity);
        foreach ($dbObj as $k => $v) {
            if ($encoding !== null) {
                $v = mb_convert_encoding($v, 'UTF-8', $encoding);
            }
            $property = $reflection->getProperty($k);
            $property->setAccessible(true);
            $property->setValue($entity, $v);
            $property->setAccessible(false);
            $dbObj->$k = $v;
        }
    }

    private static function fillLowerThan53(Rock_Dbt_EntityBase $entity, stdClass $dbObj)
    {
        $encoding = Rock_Dbt_EntityGet::getEncoding($entity);
        foreach ($dbObj as $k => $v) {
            if ($encoding !== null) {
                $v = mb_convert_encoding($v, 'UTF-8', $encoding);
            }
            $dbObj->$k = $v;
        }
        $entity->__setValues($dbObj);
    }

    public static function encodeToEntity(Rock_Dbt_EntityBase $entity, $str)
    {
        $encoding = Rock_Dbt_EntityGet::getEncoding($entity);
        if (! empty($encoding)) {
            return mb_convert_encoding($str, $encoding, 'UTF-8');
        }
        return $str;
    }

    public static function setArrayFilter(Rock_Dbt_EntityBase $entity, array $array)
    {
        $entity->__setArrayFilter($array);
    }
}
