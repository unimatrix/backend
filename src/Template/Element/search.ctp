<?= $this->Form->create($search, ['class' => 'search']) ?>
<?= $this->Form->control('search', ['placeholder' => __('Search here...'), 'label' => false]) ?>
<?= $this->Form->end() ?>
