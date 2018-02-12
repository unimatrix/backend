<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="menu vertical">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link('<i class="fa fa-long-arrow-left" aria-hidden="true"></i>' . __('Back to site'), ['controller' => 'Index', 'action' => 'index', 'plugin' => false, 'prefix' => false], ['escape' => false]) ?></li>
    </ul>
</nav>
<div class="dashboard login form large-9 medium-8 columns content">
    <?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __('Authentication') ?></legend>
        <?= $this->Form->control('username'); ?>
		<?= $this->Form->control('password'); ?>
		<?= $this->Form->control('remember', ['type' => 'checkbox', 'label' => 'Remember me', 'checked' => true]); ?>
    </fieldset>
    <?= $this->Form->button(__('Login')) ?>
    <?= $this->Form->end() ?>
</div>
