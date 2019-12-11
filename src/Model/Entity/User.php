<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Authentication\IdentityInterface as AuthenticationIdentity;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Authorization\AuthorizationService;
use Authorization\IdentityInterface as AuthorizationIdentity;
use Cake\ORM\TableRegistry;

class User extends Entity implements AuthorizationIdentity, AuthenticationIdentity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'organization_id' => true,
        'name' => true,
        'username' => true,
        'email' => true,
        'password' => true,
        'usertype' => true,
        'block' => true,
        'sendEmail' => true,
        'registerDate' => true,
        'lastvisitDate' => true,
        'activation' => true,
        'params' => true,
        'lastResetTime' => true,
        'resetCount' => true,
        'organization' => true
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];

     /**
     * Authentication\IdentityInterface method
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * Authentication\IdentityInterface method
     */
    public function getOriginalData()
    {
        return $this;
    }

    /**
     * Authorization\IdentityInterface method
     */
    public function can($action, $resource)
    {
        return $this->authorization->can($this, $action, $resource);
    }

    /**
     * Authorization\IdentityInterface method
     */
    public function applyScope($action, $resource)
    {
        return $this->authorization->applyScope($this, $action, $resource);
    }

    /**
     * Setter to be used by the middleware.
     */
    public function setAuthorization(AuthorizationService $service)
    {
        $this->authorization = $service;
        return $this;
    }
}