<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->prev('<i class="fa fa-angle-double-left" aria-hidden="true"></i> ' . __d('Unimatrix/Backend', 'prev'), ['escape' => false]) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__d('Unimatrix/Backend', 'next') . ' <i class="fa fa-angle-double-right" aria-hidden="true"></i>', ['escape' => false]) ?>
    </ul>
    <p><?= $this->Paginator->counter() ?></p>
</div>
