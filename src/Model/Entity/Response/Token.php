<?php
namespace App\Model\Entity\Response;

use Cake\ORM\Entity;

class Token extends Entity
{
    const CODE_ERROR = 500;
    const CODE_ERROR_CODE = 501;
    const CODE_EXPIRED = 502;
    const CODE_VALID = 202;
    const CODE_KO = 501;
    const CODE_OK = 200;
    const CODE_RENEW = 203;

    private $esito = false;
    private $msg = '';
    private $code = '';
    private $results = [];

    public function setEsito($v) {
        $this->esito = $v;
    }

    public function setMsg($v) {
        $this->msg = $v;
    }

    public function setCode($v) {
        $this->code = $v;
    }

    public function setResults($v) {
        $this->results = $v;
    }

    public function getEsito() {
        return $this->esito;
    }

    public function getMsg() {
        return $this->msg;
    }

    public function getCode() {
        return $this->code;
    }

    public function getResults() {
        return $this->results;
    }
}