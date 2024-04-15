<?php

namespace App\Command;

use App\Service\CollegeListService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Команда для удаления всех колледжей.
 */
#[AsCommand(
    name: 'app:clear-colleges',
    description: 'Add a short description for your command',
)]
class ClearCollegesCommand extends Command
{
    public function __construct(private readonly CollegeListService $collegeListService)
    {
        parent::__construct();
    }

    /**
     * Удаление всех колледжей.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->collegeListService->clear();
            $io->success('Колледжи удалены');

            return Command::SUCCESS;
        } catch (InvalidArgumentException $e) {
            $io->error('Ошибка при очистке колледжей: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
