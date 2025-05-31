<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $files
 * @var mixed $logs
 * @var array $pagination
 * @var mixed $selectedFiles
 * @var mixed $selectedTypes
 * @var mixed $types
 */

$fileList = [];

if (!empty($files)) {
    foreach ($files as $file) {
        $fileList[$file['name']] = $file['name'] . ' (' . $file['date'] . ')';
    }
}

function getBadgeClass($type)
{
    $type = strtolower($type);

    if ($type == 'info') {
        return 'text-bg-info';
    } elseif ($type == 'error') {
        return 'text-bg-danger';
    } elseif ($type == 'warning') {
        return 'text-bg-warning';
    } elseif ($type == 'notice') {
        return 'text-bg-secondary';
    } elseif ($type == 'debug') {
        return 'text-bg-primary';
    }

    return '';
}

$queryParams = $this->getRequest()->getQueryParams();
?>

<?= $this->Form->create(null, ['method' => 'get']) ?>
    <?= $this->Form->control('files', [
        'label' => 'Files',
        'required' => false,
        'options' => $fileList,
        'multiple' => true,
        'id' => 'files',
        'onchange' => 'this.form.submit()',
        'class' => 'form-control',
        'templates' => [
            'inputContainer' => '<div class="form-group col-md-6 col-lg-6">{{content}}</div>',
        ],
    ]); ?>

    <?= $this->Form->control('types', [
        'label' => 'Types',
        'required' => false,
        'id' => 'types',
        'options' => $types,
        'multiple' => true,
        'onchange' => 'this.form.submit()',
        'class' => 'form-control',
        'templates' => [
            'inputContainer' => '<div class="form-group col-md-4 col-lg-4">{{content}}</div>',
        ],
    ]); ?>

    <?= $this->Form->control('limit', [
        'label' => 'Limit',
        'required' => false,
        'id' => 'limit',
        'options' => ['25' => '25', '50' => '50', '100' => '100', '500' => '500', '1000' => '1000'],
        'default' => '100',
        'value' => $pagination['limit'],
        'onchange' => 'this.form.submit()',
        'class' => 'form-control',
        'templates' => [
            'inputContainer' => '<div class="form-group col-md-2 col-lg-2">{{content}}</div>',
        ],
    ]); ?>
<?= $this->Form->end() ?>

<div class="row">
    <div class="col-md-12">
        <?php if (!empty($selectedFiles)) : ?>
            <div class="table-responsive">
                <table class="table table-white-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th width="200" scope="col">Date</th>
                            <th width="120" scope="col">Type</th>
                            <th width="1100" scope="col">Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log) : ?>
                            <?php $badgeClass = getBadgeClass($log['type']); ?>
                            <tr>
                                <td><?= $log['date'] ?></td>
                                <td class="badge <?= $badgeClass ?>"><?= $log['type'] ?></td>
                                <td><?= $log['message'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php
                        $pageLimit = $pagination['page'] + 10;
                        $i = $pagination['page'] - 10;
                        if ($i < 1) {
                            $i = 1;
                        }
                    ?>

                    <?php if ($pagination['page'] > 1) : ?>
                        <li class="page-item <?= $pagination['page'] == 1 ? 'disabled' : ''; ?>">
                            <?php $queryParams['page'] = 1; ?>
                            <a class="page-link" href="<?= '?' . http_build_query($queryParams); ?>">First</a>
                        </li>
                    <?php endif; ?>

                    <li class="page-item <?= $pagination['page'] == 1 ? 'disabled' : ''; ?>">
                        <?php $queryParams['page'] = $pagination['page'] - 1; ?>
                        <a class="page-link" href="<?= '?' . http_build_query($queryParams); ?>">Previous</a>
                    </li>

                    <?php for ($i; $i <= $pagination['pages']; $i++) : ?>
                        <?php
                            if ($i > $pageLimit) {
                                break;
                            }
                            $queryParams['page'] = $i;
                            $buildParams = '?' . http_build_query($queryParams);
                        ?>
                        <li class="page-item <?= $pagination['page'] == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="<?= $buildParams ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagination['page'] + 1 <= $pagination['pages']) : ?>
                        <?php $queryParams['page'] = ++$pagination['page']; ?>
                        <li class="page-item <?= $pagination['page'] >= $pagination['pages'] ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= '?' . http_build_query($queryParams); ?>">Next</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($pagination['page'] < $pagination['pages']) : ?>
                        <?php $queryParams['page'] = $pagination['pages']; ?>
                        <li class="page-item <?= $pagination['page'] >= $pagination['pages'] ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= '?' . http_build_query($queryParams); ?>">Last</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>

        <?php else : ?>
            <h3><?= __d('LogReader', 'Please select one or more files for viewing') ?></h3>
        <?php endif; ?>
    </div>
</div>
