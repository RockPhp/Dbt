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
            $arrayBindStr = implode(",", $this->arrayBind);
            $exceptionMsg = 'Erro na query [' . $this->query . "]\n";
            $exceptionMsg .= 'arrayBind: [' . $arrayBindStr . "]\n";
            $exceptionMsg .= 'errorCode: [' . $this->conn->getErrorCode() . "]\n";
            $exceptionMsg .= 'errorMsg: [' . $this->conn->getErrorMsg() . "]\n";
            $exceptionMsg .= 'exceptionCode: [' . $e->getCode() . "]\n";
            $exceptionMsg .= 'exceptionMsg: [' . $e->getMessage() . ']';
            throw new Exception($exceptionMsg);
        }
        return $stmt;
    }
}
