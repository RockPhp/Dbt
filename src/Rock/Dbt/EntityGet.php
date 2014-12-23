<?php

class Rock_Dbt_EntityGet
{

    public static function getTable(Rock_Dbt_EntityBase $entity)
    {
        return $entity->__getTable();
    }

    public static function getFields(Rock_Dbt_EntityBase $entity, $fixEncoding = false)
    {
        $arrayVars = $entity->__getFields();
        unset($arrayVars['__tableName']);
        unset($arrayVars['__arrayFilter']);
        unset($arrayVars['__arrayOrderBy']);
        unset($arrayVars['__arrayPK']);
        unset($arrayVars['__encoding']);
        $encoding = self::getEncoding($entity);
        foreach ($arrayVars as $k => $v) {
            if ($fixEncoding && $encoding !== null) {
                $arrayVars[$k] = mb_convert_encoding($v, $encoding, 'UTF-8');
            }
            if ($v === null || (substr($k, 0, 2) == '__')) {
                unset($arrayVars[$k]);
            }
        }
        return $arrayVars;
    }

    public static function encodeFromEntity(Rock_Dbt_EntityBase $entity, $str)
    {
        $encoding = self::getEncoding($entity);
        if (! empty($encoding)) {
            return mb_convert_encoding($str, 'UTF-8', $encoding);
        }
        return $str;
    }

    public static function getArrayPK(Rock_Dbt_EntityBase $entity)
    {
        return $entity->__getArrayPK();
    }

    public static function getEncoding(Rock_Dbt_EntityBase $entity)
    {
        return $entity->__getEncoding();
    }

    public static function getArrayOrderBy(Rock_Dbt_EntityBase $entity)
    {
        return $entity->__getArrayOrderBy();
    }

    public static function getAllFields(Rock_Dbt_EntityBase $entity)
    {
        $reflection = new ReflectionClass($entity);
        $arrayTmp = array();
        $properties = $reflection->getProperties();
        foreach ($properties as $v) {
            $arrayTmp[$v->name] = $v->name;
        }
        unset($arrayTmp['__arrayOrderBy']);
        unset($arrayTmp['__tableName']);
        unset($arrayTmp['__arrayFilter']);
        unset($arrayTmp['__arrayPK']);
        unset($arrayTmp['__encoding']);
        return $arrayTmp;
    }

    public static function getArrayFilter(Rock_Dbt_EntityBase $entity)
    {
        return $entity->__getArrayFilter();
    }
}
