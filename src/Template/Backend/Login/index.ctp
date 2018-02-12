<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="menu vertical">
        <li class="heading"><?= __d('Unimatrix/Backend', 'Actions') ?></li>
        <li><?= $this->Html->link('<i class="fa fa-long-arrow-left" aria-hidden="true"></i>' . __d('Unimatrix/Backend', 'Back to site'), ['controller' => 'Index', 'action' => 'index', 'plugin' => false, 'prefix' => false], ['escape' => false]) ?></li>
    </ul>
</nav>
<div class="dashboard login form large-9 medium-8 columns content">
    <?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __d('Unimatrix/Backend', 'Authentication') ?></legend>
        <?= $this->Form->control('username', ['label' => __d('Unimatrix/Backend', 'Username')]); ?>
		<?= $this->Form->control('password', ['label' => __d('Unimatrix/Backend', 'Password')]); ?>
		<?= $this->Form->control('remember', ['type' => 'checkbox', 'label' => __d('Unimatrix/Backend', 'Remember me'), 'checked' => true]); ?>
    </fieldset>
    <?= $this->Form->button(__d('Unimatrix/Backend', 'Login')) ?>
    <?= $this->Form->end() ?>
</div>
