<?php
namespace App\Service;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;

class WorkerStarter
{
    public function __construct(
        private ConsumeMessagesCommand $consumeCommand
    ) {}

    public function launchWorker(): void
    {
        $input = new ArrayInput([
            'receivers' => ['async'],
            '--limit' => 100,
            '--time-limit' => 3600,
        ]);

        $output = new BufferedOutput();
        $this->consumeCommand->run($input, $output);
    }
}