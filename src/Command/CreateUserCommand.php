<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    /** @var UserRepository */
    private $repository;

    /** @var EntityManagerInterface */
    private $manager;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(
        UserRepository $repository,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder
    ) {
        parent::__construct();

        $this->repository = $repository;
        $this->manager = $manager;
        $this->encoder = $encoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a new user')
            ->setHelp(
                "Usage: \n" .
                "  php bin/console app:create-user <email> <password>\n"
            )
            ->addArgument('email', InputArgument::REQUIRED, 'The user email address')
            ->addArgument('password', InputArgument::REQUIRED, 'The user password')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Whether to add ADMIN role');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $admin = (bool)$input->getOption('admin');

        // Vérifier qu'un utilisateur n'existe pas déjà pour cet email
        if ($this->userExist($email)) {
            $output->writeln("<error>A user already exists with the email $email</error>");

            return Command::FAILURE;
        }

        // Enregistrer dans la base de données.
        $this->createUser($email, $password, $admin);

        $output->writeln("<info>User $email created !</info>");

        return Command::SUCCESS;
    }

    private function createUser(string $email, string $password, bool $admin = false): void
    {
        // Create instance de User.
        $user = new User();

        // Encoder le mot de passe.
        $encoded = $this->encoder->encodePassword($user, $password);

        // Set email and encoded password
        $user
            ->setEmail($email)
            ->setPassword($encoded);

        if ($admin) {
            $user->setRoles(['ROLE_ADMIN']);
        }

        // Persist the user
        $this->manager->persist($user);;
        $this->manager->flush();
    }

    private function userExist(string $email): bool
    {
        return null !== $this->repository->findOneBy(['email' => $email]);
    }
}
