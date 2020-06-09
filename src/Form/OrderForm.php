<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use App\Model\Entity\User;
/*
 * https://book.cakephp.org/3/en/core-libraries/form.html
 */
class OrderForm extends Form
{
    protected $_user = null;

    public function __construct(User $user)
    {
        // debug($user);
        $this->_user = $user;
        return $this;
    }

    /*
     * is used to define the schema data that is used by FormHelper to create an HTML form. You can define field type, length, and precision.
     */
    public function _buildSchema(Schema $schema)
    {
        return $schema->addField('name', ['type' => 'string', 'default' => ''])
                       ->addField('email', ['type' => 'string'])
                       ->addField('body', ['type' => 'text'])
                       ->addField('sex', ['type' => 'checkbox', 'options' => ['M', 'F']]);

/*
        $this->loadModel('SuppliersOrganizations');
        $supplier_organizations = $this->SuppliersOrganization->gets($this->_user);
        debug($supplier_organizations);
        $schema->addField('supplier_organizations', ['options' => $supplier_organizations]);
*/
    }

    public function validationDefault(Validator $validator)
    {
        $validator->add('name', 'length', [
                'rule' => ['minLength', 10],
                'message' => 'A name is required'
            ])->add('email', 'format', [
                'rule' => 'email',
                'message' => 'A valid email address is required',
            ])
            ->requirePresence('body', true)
            /*
            ->add('consumer.card_number', 'validFormat', [
                'rule' => array('custom', '/[0-9]{16}/'),
                'message' => 'A valid card number is required',
            ])
            ->add('consumer.expire_month', 'validFormat', [
                'rule' => array('custom', '/[01]?[0-9]/'),
                'message' => 'A valid expire month is required',
            ])
            ->add('consumer.expire_year', 'validFormat', [
                'rule' => array('custom', '/[0-9]{2}/'),
                'message' => 'A valid expire year is required',
            ])
            ->add('consumer.cvv', 'validFormat', [
                'rule' => array('custom', '/[0-9]{3}/'),
                'message' => 'A valid CVV is required',
            ]) 
            */           
            ;

        return $validator;
    }

    public function setErrors($errors)
    {
        $this->_errors = $errors;
    }

    protected function _execute(array $data)
    {
        debug('Send an email');
        /*
        $email = new Email('default');
        return $email->from([$data['email'] => $data['name']])
            ->to('mail@example.com', 'Mail Example')
            ->subject('Contact Form')
            ->message($data['body'])
            ->send();
        */
        return true;
    }
}