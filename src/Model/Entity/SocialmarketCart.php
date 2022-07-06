<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SocialmarketCart Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $user_id
 * @property int $user_organization_id
 * @property int $order_id
 * @property string $article_name
 * @property float $article_prezzo
 * @property int $cart_qta
 * @property float $cart_importo_finale
 * @property string|null $nota
 * @property string $is_active
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\UserOrganization $user_organization
 * @property \App\Model\Entity\Order $order
 */
class SocialmarketCart extends Entity
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
        'user_id' => true,
        'user_organization_id' => true,
        'order_id' => true,
        'article_name' => true,
        'article_prezzo' => true,
        'cart_qta' => true,
        'cart_importo_finale' => true,
        'nota' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'user' => true,
        'user_organization' => true,
        'order' => true,
    ];
}
