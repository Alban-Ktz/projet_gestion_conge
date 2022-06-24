<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;


#[AsCommand(
    name: 'app:create-user',
    description: 'Create an user with the ADMIN role',
)]
class CreateUserCommand extends Command
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator,
        ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $helper = $this->getHelper('question');

        $output->writeln([
            '',
            '===========',
            'Création Administrateur',
            '===========',
            '',
    ]);

        $output->writeln('Saississez le prénom de l\'utilisateur');
        $questionPrenom = new Question('>');
        $prenom = $helper->ask($input, $output, $questionPrenom);

        $output->writeln('Saississez le nom de l\'utilisateur');
        $questionNom = new Question('>');
        $nom = $helper->ask($input, $output, $questionNom);

        $output->writeln('Saississez l\'adresse e-mail de l\'utilisateur');
        $questionMail = new Question('>');
        $email = $helper->ask($input, $output, $questionMail);

        $output->writeln('Saississez le mot de passe de l\'utilisateur');
        $questionPassword = new Question('>');
        $questionPassword->setHidden(true);
        $password = $helper->ask($input, $output, $questionPassword);

        $user = new User();
        $user->setPrenom($prenom);
        $user->setNom($nom);
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $io->error('Merci de saisir une e-mail valide!');
            return Command::FAILURE;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Création de l\'administrateur '. $user->getFullName() . ' réalisé avec succès !');

        return Command::SUCCESS;
    }
}
