<?php

namespace App\Command;

use App\Service\EmailService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;



#[AsCommand(name: 'app:test-email')]
class TestEmailCommand extends Command
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('ourbatima@gmail.com')
            ->to('waelbensalem02@gmail.com')
            ->subject('TEST FINAL - ' . date('Y-m-d H:i:s'))
            ->text('ya hayaweeen.');

        try {
            $this->mailer->send($email);
            $this->logger->info('Email envoyé avec succès');
            $output->writeln('<info>Email envoyé avec succès!</info>');
            return Command::SUCCESS;
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Échec envoi email', ['error' => $e->getMessage()]);
            $output->writeln('<error>Erreur: '.$e->getMessage().'</error>');
            return Command::FAILURE;
        }
    }
}