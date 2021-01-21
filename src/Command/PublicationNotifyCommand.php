<?php

namespace App\Command;

use App\Repository\PublicationRepository;
use App\Service\Notifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PublicationNotifyCommand extends Command
{
    protected static $defaultName = 'app:publication-notify';

    /** @var PublicationRepository */
    private $repository;

    /** @var Notifier */
    private $notifier;

    public function __construct(
        PublicationRepository $repository,
        Notifier $notifier
    ) {
        parent::__construct();

        $this->repository = $repository;
        $this->notifier = $notifier;
    }

    protected function configure()
    {
        $this
            ->setDescription('Sends notification for the given publication ID')
            ->addArgument('id', InputArgument::REQUIRED, 'The publication ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getArgument('id');

        $publication = $this->repository->find($id);
        if (!$publication) {
            $output->writeln("<error>Publication introuvable</error>");

            return Command::FAILURE;
        }

        if (!$this->notifier->notify($publication)) {
            $output->writeln("<error>Echec lors de l'envoi de la notification</error>");

            return Command::FAILURE;
        }

        $output->writeln("<info>Notification envoyée</info>");

        return Command::SUCCESS;
    }
}
