<?php

abstract class Rock_Dbt_GenQuery extends Rock_Dbt_RunQuery
{

    protected function genSelect(Rock_Dbt_EntityBase $entity)
    {
        $quoteTb = $this->quoteTableNames ? '"' : '';
        $quoteField = $this->quoteFields ? '"' : '';
        $arrayTmp = Rock_Dbt_EntityGet::getAllFields($entity);
        $tabela = Rock_Dbt_EntityGet::getTable($entity);
        $campos = $quoteTb . $tabela . $quoteTb . '.' . $quoteField;
        $strImplode = $quoteField . ",\n " . $quoteTb . $tabela . $quoteTb . '.' . $quoteField;
        $campos .= implode($strImplode, array_values($arrayTmp));
        $this->query = "SELECT \n  " . $campos . $quoteField . "\n FROM " . $quoteTb . $tabela . $quoteTb;
        $this->arrayBind = null;
    }

    protected function genOrderBy(Rock_Dbt_EntityBase $entity)
    {
        $orderBy = Rock_Dbt_EntityGet::getArrayOrderBy($entity);
        if (! empty($orderBy)) {
            $quoteTb = $this->quoteTableNames ? '"' : '';
            $quoteField = $this->quoteFields ? '"' : '';
            $tabela = Rock_Dbt_EntityGet::getTable($entity);
            $tabela = $quoteTb . $tabela . $quoteTb;
            $arrayCampos = array();
            foreach ($orderBy as $k => $v) {
                $ascDesc = ($v == Rock_Dbt_EntityBase::ORDER_BY_DESC) ? 'DESC' : 'ASC';
                $arrayCampos[] = $tabela . '.' . $quoteField . $k . $quoteField . ' ' . $ascDesc;
            }
            $orderBy = implode(',', $arrayCampos);
            $this->query .= " \n ORDER BY " . implode(',', $arrayCampos) . " \n ";
        }
    }

    protected function genWhere(Rock_Dbt_EntityBase $entity)
    {
        $arrayTmp = Rock_Dbt_EntityGet::getFields($entity, true);
        if (count($arrayTmp) > 0) {
            $quoteTb = $this->quoteTableNames ? '"' : '';
            $quoteField = $this->quoteFields ? '"' : '';
            $tableName = Rock_Dbt_EntityGet::getTable($entity);
            $tableName = $quoteTb . $tableName . $quoteTb;
            $where = array();
            $arrayKeys = array_keys($arrayTmp);
            foreach ($arrayKeys as $k) {
                $where[] = $tableName . '.' . $quoteField . $k . $quoteField . " = ?";
            }
            $where = implode(" AND \n  ", $where);
            $this->query .= "\n WHERE (\n  " . $where . " \n )";
            $this->arrayBind = array_values($arrayTmp);
        }
    }

    protected function genInsert(Rock_Dbt_EntityBase $entity)
    {
        $this->query = '';
        $arrayTmp = Rock_Dbt_EntityGet::getFields($entity, true);
        if (count($arrayTmp) > 0) {
            $quoteTb = $this->quoteTableNames ? '"' : '';
            $quoteField = $this->quoteFields ? '"' : '';
            $tabela = Rock_Dbt_EntityGet::getTable($entity);
            $tabela = $quoteTb . $tabela . $quoteTb;
            $campos = implode($quoteField . ",\n  " . $quoteField, array_keys($arrayTmp));
            $values = array_fill(0, count($arrayTmp), '?');
            $values = implode(",\n  ", $values);
            $this->query .= 'INSERT INTO ' . $tabela;
            $this->query .= "\n (\n  " . $quoteField . $campos . $quoteField . "\n )";
            $this->query .= "\n VALUES \n (\n  " . $values . "\n )";
            $this->arrayBind = array_values($arrayTmp);
        } else {
            throw new Exception('Entidade vazia para insert em: [' . Rock_Dbt_EntityGet::getTable($entity) . ']');
        }
    }

    protected function genUpdate(Rock_Dbt_EntityBase $entity)
    {
        $quoteTb = $this->quoteTableNames ? '"' : '';
        $quoteField = $this->quoteFields ? '"' : '';
        $tabela = Rock_Dbt_EntityGet::getTable($entity);
        $tabela = $quoteTb . $tabela . $quoteTb;
        $arrayTmp = Rock_Dbt_EntityGet::getFields($entity, true);
        $pk = Rock_Dbt_EntityGet::getArrayPK($entity);
        $arrayPk = array();
        foreach ($pk as $v) {
            $arrayPk[$v] = $arrayTmp[$v];
            unset($arrayTmp[$v]);
        }
        $campos = array();
        foreach ($arrayTmp as $k => $v) {
            $campos[] = $quoteField . $k . $quoteField . ' = ? ';
        }
        $campos = implode(",\n  ", $campos);
        $where = array();
        foreach ($arrayPk as $k => $v) {
            $where[] = $quoteField . $k . $quoteField . ' = ?';
        }
        $where = implode(" AND \n  ", $where);
        $this->query = 'UPDATE ' . $tabela;
        $this->query .= "\n SET \n  " . $campos;
        $this->query .= "\n WHERE \n  " . $where;
        $this->arrayBind = array_values($arrayTmp);
        $this->arrayBind = array_merge($this->arrayBind, array_values($arrayPk));
    }

    protected function genDelete(Rock_Dbt_EntityBase $entity)
    {
        $quoteTb = $this->quoteTableNames ? '"' : '';
        $quoteField = $this->quoteFields ? '"' : '';
        $tabela = Rock_Dbt_EntityGet::getTable($entity);
        $tabela = $quoteTb . $tabela . $quoteTb;
        $pk = Rock_Dbt_EntityGet::getArrayPK($entity);
        $arrayTmp = Rock_Dbt_EntityGet::getFields($entity, true);
        $arrayPk = array();
        foreach ($pk as $v) {
            $arrayPk[$v] = $arrayTmp[$v];
        }
        $where = array();
        foreach ($arrayPk as $k => $v) {
            $where[] = $quoteField . $k . $quoteField . " = ?";
        }
        $where = implode(" AND \n  ", $where);
        $this->query = 'DELETE FROM ' . $tabela;
        $this->query .= "\n WHERE \n  " . $where;
        $this->arrayBind = array_values($arrayPk);
    }

    protected function genSelectByPk(Rock_Dbt_EntityBase $entity)
    {
        $quoteTb = $this->quoteTableNames ? '"' : '';
        $quoteField = $this->quoteFields ? '"' : '';
        $tabela = Rock_Dbt_EntityGet::getTable($entity);
        $tabela = $quoteTb . $tabela . $quoteTb;
        $this->genSelect($entity);
        $pk = Rock_Dbt_EntityGet::getArrayPK($entity);
        $arrayTmp = Rock_Dbt_EntityGet::getFields($entity, true);
        $arrayPk = array();
        foreach ($pk as $v) {
            $arrayPk[$v] = $arrayTmp[$v];
        }
        $where = array();
        foreach ($arrayPk as $k => $v) {
            $where[] = $tabela . '.' . $quoteField . $k . $quoteField . " = ?";
        }
        $where = implode(" AND \n  ", $where);
        $this->query .= "\n WHERE \n  " . $where;
        $this->arrayBind = array_values($arrayPk);
    }

    protected function genTruncate(Rock_Dbt_EntityBase $entity)
    {
        $quoteTb = $this->quoteTableNames ? '"' : '';
        $tabela = Rock_Dbt_EntityGet::getTable($entity);
        $tabela = $quoteTb . $tabela . $quoteTb;
        $this->query = 'TRUNCATE ' . $tabela;
        $this->arrayBind = null;
    }
}
