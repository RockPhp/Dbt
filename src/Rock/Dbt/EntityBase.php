<?php

abstract class Rock_Dbt_EntityBase
{

    const ORDER_BY_ASC = 1;

    const ORDER_BY_DESC = 2;

    protected $__tableName = '';

    protected $__arrayFilter = array();

    protected $__arrayPK = array();

    protected $__arrayOrderBy = array();

    protected $__encoding = null;

    protected function _addOrderBy($field, $orderBy)
    {
        if (empty($field)) {
            throw new Exception(' campo orderby vazio ');
        }
        $this->__arrayOrderBy[$field] = $orderBy;
    }

    /**
     * Correção para PHP < 5.3 <br/> sem reflection
     *
     * @return array:
     */
    protected function _getArrayFilter()
    {
        $arrayTmp = array();
        foreach ($this->__arrayFilter as $k => $v) {
            $arrayTmp[$v] = $this->$k;
        }
        return $arrayTmp;
    }

    /**
     * Correção para PHP < 5.3 <br/> sem reflection
     *
     * @return array:
     */
    protected function _setArrayFilter(array $arrayFilter)
    {
        foreach ($this->__arrayFilter as $k => $v) {
            $this->$k = $arrayFilter[$v];
        }
    }

    /**
     * Correção para PHP < 5.3 <br/> sem reflection
     *
     * @return array:
     */
    protected function _getArrayOrderBy()
    {
        return $this->__arrayOrderBy;
    }

    /**
     * Correção para PHP < 5.3 <br/> sem reflection
     *
     * @param stdClass $dbObj            
     */
    protected function _setValues(stdClass $dbObj)
    {
        foreach ($dbObj as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * Correção para PHP < 5.3 <br/> sem reflection
     */
    protected function _getArrayPK()
    {
        return $this->__arrayPK;
    }

    /**
     * Correção para PHP < 5.3 <br/> sem reflection
     */
    protected function _getEncoding()
    {
        return $this->__encoding;
    }

    /**
     * Correção para PHP < 5.3 <br/> sem reflection
     *
     * @return string
     */
    protected function _getTable()
    {
        return $this->__tableName;
    }

    /**
     * Correção para PHP < 5.3 <br/> sem reflection
     *
     * @return array:
     */
    protected function _getFields()
    {
        return get_object_vars($this);
    }

    /**
     * Correção para PHP < 5.3 <br/> sem reflection
     *
     * @return array:
     */
    protected function _setPk(array $pk)
    {
        foreach ($pk as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * Método magico para PHP < 5.3 <br/> sem reflection
     */
    public function __call($method, $args)
    {
        $arrayPerm = array(
            DIRECTORY_SEPARATOR . 'Dbt' . DIRECTORY_SEPARATOR . 'EntityGet.php',
            DIRECTORY_SEPARATOR . 'Dbt' . DIRECTORY_SEPARATOR . 'EntitySet.php'
        );
        $callerFile = debug_backtrace();
        $callerFile = $callerFile[1]['file'];
        $permOk = false;
        foreach ($arrayPerm as $v) {
            if (substr_count($callerFile, $v) == 1) {
                $permOk = true;
            }
        }
        if (! $permOk) {
            $exceptMsg = 'Acesso ao metodo ' . $method;
            $exceptMsg .= ' somente via Rock_Dbt_EntityGet ou Rock_Dbt_EntitySet.';
            throw new Exception($exceptMsg);
        }
        $args = empty($args[0]) ? null : $args[0];
        if (substr_count($method, '__') > 0) {
            $method = str_replace('__', '_', $method);
            return $this->$method($args);
        } else {
            throw new Exception('Metodo ' . $method . ' nao encontrado.');
        }
    }
}
