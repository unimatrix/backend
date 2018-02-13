<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="menu vertical">
        <li class="heading"><?= __d('Unimatrix/backend', 'Actions') ?></li>
        <li><?= $this->Html->link('<i class="fa fa-long-arrow-left" aria-hidden="true"></i>' . __d('Unimatrix/backend', 'Back to site'), ['controller' => 'Index', 'action' => 'index', 'plugin' => false, 'prefix' => false], ['escape' => false]) ?></li>
    </ul>
</nav>
<div class="dashboard login form large-9 medium-8 columns content">
    <?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __d('Unimatrix/backend', 'Authentication') ?></legend>
        <?= $this->Form->control('username', ['label' => __d('Unimatrix/backend', 'Username')]); ?>
		<?= $this->Form->control('password', ['label' => __d('Unimatrix/backend', 'Password')]); ?>
		<?= $this->Form->control('remember', ['type' => 'checkbox', 'label' => __d('Unimatrix/backend', 'Remember me'), 'checked' => true]); ?>
    </fieldset>
    <?= $this->Form->button(__d('Unimatrix/backend', 'Login')) ?>
    <?= $this->Form->end() ?>
</div>
