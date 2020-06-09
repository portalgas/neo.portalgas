<?php
echo $this->Form->create($order);
echo $this->Form->control('name');
echo $this->Form->control('email');
echo $this->Form->control('body');
echo $this->Form->control('sex');
echo $this->Form->control('supplier_organizations', ['options' => $supplier_organizations]);
echo $this->Form->button('Submit');
echo $this->Form->end();
?>