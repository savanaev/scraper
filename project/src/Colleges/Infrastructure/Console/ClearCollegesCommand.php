<?php

declare(strict_types=1);

namespace App\Colleges\Infrastructure\Console;

use App\Colleges\Domain\Service\CollegeListPersistenceServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:clear-colleges',
    description: 'Clears all colleges from the database'
)]
final class ClearCollegesCommand extends Command
{
    private SymfonyStyle $io;

    /**
     * ClearCollegesCommand constructor.
     * @param CollegeListPersistenceServiceInterface $collegeListPersistenceService
     */
    public function __construct(private readonly CollegeListPersistenceServiceInterface $collegeListPersistenceService)
    {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->collegeListPersistenceService->clearAllColleges();
        $this->io->success('All colleges cleared from the database.');

        return Command::SUCCESS;
    }
}
