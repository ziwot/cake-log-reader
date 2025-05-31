<?php
declare(strict_types=1);

namespace LogReader\Test\TestCase;

use Cake\TestSuite\TestCase;
use LogReader\Reader;

class ReaderTest extends TestCase
{
    public function testInit()
    {
        $reader = new Reader();

        $this->assertInstanceOf(Reader::class, $reader);
    }
}
