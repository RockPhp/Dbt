<?php

abstract class Rock_Dbt_RunQuery
{

    /**
     *
     * @var Rock_DbAl_Iface_IConn
     */
    protected $conn = null;

    protected $quoteFields = false;

    protected $quoteTableNames = false;

    protected $arrayBind;

    protected $query = '';

    /**
     *
     * @param int $start            
     * @param int $limit            
     * @throws Exception
     * @return Rock_DbAl_Iface_IStmt
     */
    protected function runQuery($start = NULL, $limit = NULL)
    {
        if (empty($this->arrayBind)) {
            $this->arrayBind = array();
        }
        try {
            $stmt = $this->conn->runQuery($this->query, $this->arrayBind, $start, $limit);
        } catch (Exception $e) {
            throw new Exception('Erro na query [' . $this->query . ']' . ' [' . $this->conn->getErrorMsg() . '] [' . $this->conn->getErrorCode() . ']');
        }
        return $stmt;
    }
}
