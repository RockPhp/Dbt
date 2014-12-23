<?php

abstract class Rock_Dbt_DaoBase extends Rock_Dbt_GenQuery
{

    protected function fetchList(Rock_Dbt_EntityBase $entity, Rock_DbAl_Iface_IStmt $stmt)
    {
        $entityStr = get_class($entity);
        $lista = array();
        while ($obj = $stmt->nextObject()) {
            $entity = new $entityStr();
            Rock_Dbt_EntitySet::fill($entity, $obj);
            $lista[] = $entity;
        }
        return $lista;
    }

    public function getAll(Rock_Dbt_EntityBase $entity, $start = NULL, $limit = NULL)
    {
        $this->genSelect($entity);
        $this->genOrderBy($entity);
        $stmt = $this->runQuery($start, $limit);
        $lista = $this->fetchList($entity, $stmt);
        return $lista;
    }

    public function getFiltered(Rock_Dbt_EntityBase $entity, $start = NULL, $limit = NULL)
    {
        $this->genSelect($entity);
        $this->genWhere($entity);
        $this->genOrderBy($entity);
        $stmt = $this->runQuery($start, $limit);
        $lista = $this->fetchList($entity, $stmt);
        return $lista;
    }

    public function insert(Rock_Dbt_EntityBase $entity)
    {
        $this->genInsert($entity);
        $arrayPk = Rock_Dbt_EntityGet::getArrayPK($entity);
        $stmt = $this->runQuery();
        $id = $this->conn->insertId();
        if (count($arrayPk) == 1 && ! empty($id)) {
            Rock_Dbt_EntitySet::setPk($entity, array(
                $arrayPk[0] => $id
            ));
        }
        return $stmt;
    }

    public function update(Rock_Dbt_EntityBase $entity)
    {
        if ($this->checkPk($entity)) {
            $this->genUpdate($entity);
            return $this->runQuery();
        }
        return false;
    }

    public function save(Rock_Dbt_EntityBase $entity)
    {
        if ($this->checkPk($entity)) {
            return $this->update($entity);
        }
        return $this->insert($entity);
    }

    private function checkPk(Rock_Dbt_EntityBase $entity)
    {
        $arrayPk = Rock_Dbt_EntityGet::getArrayPK($entity);
        if (empty($arrayPk)) {
            return false;
        }
        $arrayFields = Rock_Dbt_EntityGet::getFields($entity);
        foreach ($arrayPk as $v) {
            if (empty($arrayFields[$v])) {
                return false;
            }
        }
        return true;
    }

    public function del(Rock_Dbt_EntityBase $entity)
    {
        if ($this->checkPk($entity)) {
            $this->genDelete($entity);
            return $this->runQuery();
        }
        return false;
    }

    public function getByPk(Rock_Dbt_EntityBase $entity)
    {
        if ($this->checkPk($entity)) {
            $this->genSelectByPk($entity);
            $stmt = $this->runQuery();
            $result = $this->fetchList($entity, $stmt);
            if (count($result) == 0) {
                return new $entity();
            }
            return $result[0];
        }
        return new $entity();
    }

    public function truncate(Rock_Dbt_EntityBase $entity)
    {
        $this->genTruncate($entity);
        $this->runQuery();
    }
}
