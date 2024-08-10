<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\SymfonyCommand;

use App\NotificationPublisher\Application\RetryManager\RetryManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:retry_notification_sending', description: 'Run retry notification sending')]
class RetryNotificationSendingCommand extends Command
{
    public function __construct(
        private RetryManager $retryManager
    ) {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->retryManager->retrySending();
        return Command::SUCCESS;
    }
}
