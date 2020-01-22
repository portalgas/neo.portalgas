<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MappingValueType[]|\Cake\Collection\CollectionInterface $mappingValueTypes
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Mapping Value Type'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Mappings'), ['controller' => 'Mappings', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Mapping'), ['controller' => 'Mappings', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="mappingValueTypes index large-9 medium-8 columns content">
    <h3><?= __('Mapping Value Types') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('code') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('factory_force_value') ?></th>
                <th scope="col"><?= $this->Paginator->sort('is_force_value') ?></th>
                <th scope="col"><?= $this->Paginator->sort('is_system') ?></th>
                <th scope="col"><?= $this->Paginator->sort('is_active') ?></th>
                <th scope="col"><?= $this->Paginator->sort('sort') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mappingValueTypes as $mappingValueType): ?>
            <tr>
                <td><?= $this->Number->format($mappingValueType->id) ?></td>
                <td><?= h($mappingValueType->code) ?></td>
                <td><?= h($mappingValueType->name) ?></td>
                <td><?= h($mappingValueType->factory_force_value) ?></td>
                <td><?= h($mappingValueType->is_force_value) ?></td>
                <td><?= h($mappingValueType->is_system) ?></td>
                <td><?= h($mappingValueType->is_active) ?></td>
                <td><?= $this->Number->format($mappingValueType->sort) ?></td>
                <td><?= h($mappingValueType->created) ?></td>
                <td><?= h($mappingValueType->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $mappingValueType->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $mappingValueType->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $mappingValueType->id], ['confirm' => __('Are you sure you want to delete # {0}?', $mappingValueType->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
