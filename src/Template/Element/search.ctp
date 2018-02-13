<?= $this->Form->create($search, ['class' => 'search']) ?>
<?= $this->Form->control('search', ['placeholder' => __d('Unimatrix/backend', 'Search here...'), 'label' => false]) ?>
<?= $this->Form->end() ?>
