<?php
declare(strict_types=1);

namespace LogReader;

use DateTime;
use DirectoryIterator;
use SplFileInfo;

/**
 * Reader class
 */
class Reader
{
    /**
     * @param array
     */
    private array $logTypes = [
        'info' => 'Info',
        'emergency' => 'Emergency',
        'critical' => 'Critical',
        'alert' => 'Alert',
        'error' => 'Error',
        'warning' => 'Warning',
        'notice' => 'Notice',
        'debug' => 'Debug',
    ];

    /**
     * Get the date of the files
     *
     * @return array List of different dates of files
     */
    public function getFileDates(): array
    {
        $dates = [];
        $it = new DirectoryIterator(LOGS);
        foreach ($it as $node) {
            if ($node->isDot()) {
                continue;
            }

            if ($node->isFile()) {
                dump($node);
            }
        }

        return array_unique($dates);
    }

    /**
     * Main reader function
     * The files and types that are parsed need to be set in config
     *
     * @param array $selectedFiles
     * @param array $selectedTypes
     * @return array List of logs
     */
    public function read(array $selectedFiles = [], array $selectedTypes = []): array
    {
        $data = $this->getLogFile($selectedFiles);
        $logs = [];

        if ($data) {
            foreach ($data as $d) {
                $matches = $this->_parseData($d);

                if ($matches) {
                    foreach ($matches as $match) {
                        if (
                            (count($selectedTypes) > 0)
                            && (!in_array(strtolower($match['type']), $selectedTypes))
                        ) {
                            continue;
                        }

                        $logs[] = $match;
                    }
                }
            }
        }

        return $logs;
    }

    /**
     * Get logs inside file or files
     *
     * @param array $selectedFiles List of files to get the logs from
     * @return array Content of the selected files
     */
    private function getLogFile(array $selectedFiles = []): array
    {
        $data = [];

        foreach (glob(sprintf('%s*.log', LOGS)) as $file) {
            $info = new SplFileInfo($file);
            $filename = $info->getFilename();

            if (count($selectedFiles) > 0) {
                if (!in_array($filename, $selectedFiles)) {
                    continue;
                }
            }

            if (strpos($filename, 'cli-debug') !== false) {
                $type = 'cli-debug';
            } elseif (strpos($filename, 'cli-error') !== false) {
                $type = 'cli-error';
            } elseif (strpos($filename, 'error') !== false) {
                $type = 'error';
            } elseif (strpos($filename, 'debug') !== false) {
                $type = 'debug';
            } else {
                $type = 'unknown';
            }

            if (!isset($data[$type])) {
                $data[$type] = '';
            }

            $data[$type] .= file_get_contents($info->getPathname());
        }

        return $data;
    }

    /**
     * Get list of log files inside the logs folder
     *
     * @return array List of files
     */
    public function getFiles(): array
    {
        $filesList = [];
        foreach (glob(sprintf('%s*.log', LOGS)) as $file) {
            $info = new SplFileInfo($file);
            $filename = $info->getFilename();

            $filesList[] = [
                'name' => $filename,
                'date' => $info->getCTime(),
                'type' => strpos($filename, 'cli-debug') !== false
                    || strpos($filename, 'cli-error') !== false
                ? 'cli' : 'app',
            ];
        }

        return $filesList;
    }

    /**
     * Parse log file content
     * Move this to use regex later
     *
     * @param string $data Content of log file
     * @return array Parsed data with type, date and content
     */
    private function _parseData(string $data): array
    {
        $data = preg_split("/\r\n|\n|\r/", $data);
        $buildData = [];

        if ($data) {
            foreach ($data as $d) {
                $d = explode(' ', $d);

                if (isset($d[0]) && isset($d[1])) {
                    $date = $d[0] . ' ' . $d[1];

                    if (DateTime::createFromFormat('Y-m-d H:i:s', $date) !== false) {
                        $type = str_replace(':', '', $d[2]);
                        unset($d[0]);
                        unset($d[1]);
                        unset($d[2]);
                        $newLine = true;
                    } else {
                        // not a date
                        $newLine = false;
                    }
                } else {
                    $newLine = false;
                }

                $message = implode(' ', $d);

                if ($newLine) {
                    $buildData[] = [
                        'date' => $date,
                        'type' => $type,
                        'message' => $message,
                    ];
                } else {
                    $key = array_key_last($buildData);
                    $buildData[$key]['message'] .= ' ' . $message;
                }
            }
        }

        return $buildData;
    }

    /**
     * Return available log file types
     *
     * @return array
     */
    public function getLogTypes(): array
    {
        return $this->logTypes;
    }
}
