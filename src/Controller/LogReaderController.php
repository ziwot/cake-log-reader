<?php
declare(strict_types=1);

namespace LogReader\Controller;

use LogReader\Reader;

class LogReaderController extends AppController
{
    /**
     * initialize method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index(): void
    {
        // SEARCH
        $selectedFiles = $this->request->getQuery('files') ?: [];
        $selectedTypes = $this->request->getQuery('types') ?: [];

        $pagination = [
            'limit' => toInt($this->request->getQuery('limit', 100)),
            'page' => toInt($this->request->getQuery('page', 1)),
        ];

        $reader = new Reader();
        $logs = $reader->read($selectedFiles, $selectedTypes);
        $total = count($logs);

        // paginate
        $pagination = [
            ...$pagination,
            'total' => $total,
            'pages' => toInt(ceil($total / $pagination['limit'])),
            'offset' => ($pagination['page'] * $pagination['limit']) - $pagination['limit'],
        ];

        $logs = array_slice($logs, $pagination['offset'], $pagination['limit'], true);

        $this->set(compact('logs', 'pagination', 'selectedFiles', 'selectedTypes'));
        $this->set('files', $reader->getFiles());
        $this->set('types', $reader->getLogTypes());
    }
}
