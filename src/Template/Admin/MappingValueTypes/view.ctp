<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MappingValueType $mappingValueType
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Mapping Value Type'), ['action' => 'edit', $mappingValueType->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Mapping Value Type'), ['action' => 'delete', $mappingValueType->id], ['confirm' => __('Are you sure you want to delete # {0}?', $mappingValueType->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Mapping Value Types'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Mapping Value Type'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Mappings'), ['controller' => 'Mappings', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Mapping'), ['controller' => 'Mappings', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="mappingValueTypes view large-9 medium-8 columns content">
    <h3><?= h($mappingValueType->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($mappingValueType->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($mappingValueType->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Factory Force Value') ?></th>
            <td><?= h($mappingValueType->factory_force_value) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($mappingValueType->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Sort') ?></th>
            <td><?= $this->Number->format($mappingValueType->sort) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($mappingValueType->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($mappingValueType->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Force Value') ?></th>
            <td><?= $mappingValueType->is_force_value ? __('Yes') : __('No'); ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is System') ?></th>
            <td><?= $mappingValueType->is_system ? __('Yes') : __('No'); ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Active') ?></th>
            <td><?= $mappingValueType->is_active ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Match') ?></h4>
        <?= $this->Text->autoParagraph(h($mappingValueType->match)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Mappings') ?></h4>
        <?php if (!empty($mappingValueType->mappings)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Queue Id') ?></th>
                <th scope="col"><?= __('Name') ?></th>
                <th scope="col"><?= __('Descri') ?></th>
                <th scope="col"><?= __('Master Scope Id') ?></th>
                <th scope="col"><?= __('Master Table Id') ?></th>
                <th scope="col"><?= __('Master Column') ?></th>
                <th scope="col"><?= __('Master Json Path') ?></th>
                <th scope="col"><?= __('Master Xml Xpath') ?></th>
                <th scope="col"><?= __('Master Csv Num Col') ?></th>
                <th scope="col"><?= __('Slave Scope Id') ?></th>
                <th scope="col"><?= __('Slave Table Id') ?></th>
                <th scope="col"><?= __('Slave Column') ?></th>
                <th scope="col"><?= __('Mapping Type Id') ?></th>
                <th scope="col"><?= __('Queue Table Id') ?></th>
                <th scope="col"><?= __('Value') ?></th>
                <th scope="col"><?= __('Value Default') ?></th>
                <th scope="col"><?= __('Mapping Value Type Id') ?></th>
                <th scope="col"><?= __('Parameters') ?></th>
                <th scope="col"><?= __('Is Required') ?></th>
                <th scope="col"><?= __('Is Active') ?></th>
                <th scope="col"><?= __('Sort') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($mappingValueType->mappings as $mappings): ?>
            <tr>
                <td><?= h($mappings->id) ?></td>
                <td><?= h($mappings->queue_id) ?></td>
                <td><?= h($mappings->name) ?></td>
                <td><?= h($mappings->descri) ?></td>
                <td><?= h($mappings->master_scope_id) ?></td>
                <td><?= h($mappings->master_table_id) ?></td>
                <td><?= h($mappings->master_column) ?></td>
                <td><?= h($mappings->master_json_path) ?></td>
                <td><?= h($mappings->master_xml_xpath) ?></td>
                <td><?= h($mappings->master_csv_num_col) ?></td>
                <td><?= h($mappings->slave_scope_id) ?></td>
                <td><?= h($mappings->slave_table_id) ?></td>
                <td><?= h($mappings->slave_column) ?></td>
                <td><?= h($mappings->mapping_type_id) ?></td>
                <td><?= h($mappings->queue_table_id) ?></td>
                <td><?= h($mappings->value) ?></td>
                <td><?= h($mappings->value_default) ?></td>
                <td><?= h($mappings->mapping_value_type_id) ?></td>
                <td><?= h($mappings->parameters) ?></td>
                <td><?= h($mappings->is_required) ?></td>
                <td><?= h($mappings->is_active) ?></td>
                <td><?= h($mappings->sort) ?></td>
                <td><?= h($mappings->created) ?></td>
                <td><?= h($mappings->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Mappings', 'action' => 'view', $mappings->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Mappings', 'action' => 'edit', $mappings->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Mappings', 'action' => 'delete', $mappings->id], ['confirm' => __('Are you sure you want to delete # {0}?', $mappings->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
