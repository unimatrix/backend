<?= $this->Form->create($search, ['class' => 'search']) ?>
<?= $this->Form->control('search', ['placeholder' => __d('Unimatrix/Backend', 'Search here...'), 'label' => false]) ?>
<?= $this->Form->end() ?>
