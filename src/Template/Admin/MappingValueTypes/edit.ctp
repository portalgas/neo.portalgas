<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MappingValueType $mappingValueType
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $mappingValueType->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $mappingValueType->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Mapping Value Types'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Mappings'), ['controller' => 'Mappings', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Mapping'), ['controller' => 'Mappings', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="mappingValueTypes form large-9 medium-8 columns content">
    <?= $this->Form->create($mappingValueType) ?>
    <fieldset>
        <legend><?= __('Edit Mapping Value Type') ?></legend>
        <?php
            echo $this->Form->control('code');
            echo $this->Form->control('name');
            echo $this->Form->control('match');
            echo $this->Form->control('factory_force_value');
            echo $this->Form->control('is_force_value');
            echo $this->Form->control('is_system');
            echo $this->Form->control('is_active');
            echo $this->Form->control('sort');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
