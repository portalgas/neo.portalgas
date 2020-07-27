<?php
namespace App\Response;

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

    const ESITO_ERROR = 500;
    const ESITO_KO = 501;
    const ESITO_OK = 200;

    private $esito = false;
    private $msg = '';
    private $code = '';
    private $token = '';
    private $user = '';

    public function setEsito($v) {
        $this->esito = $v;
    }

    public function setMsg($v) {
        $this->msg = $v;
    }

    public function setCode($v) {
        $this->code = $v;
    }

    public function setToken($v) {
        $this->token = $v;
    }

    public function setUser($v) {
        $this->user = $v;
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

    public function getToken() {
        return $this->token;
    } 

    public function getUser() {
        return $this->user;
    }   
}