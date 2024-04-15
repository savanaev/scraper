<?php

namespace App\Command;

use App\Service\ScraperService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Класс по работе со скрапингом колледжей.
 */
#[AsCommand(
    name: 'app:collect-colleges',
    description: 'Collection of colleges for subsequent processing.'
)]
class CollectCollegesCommand extends Command
{
    public function __construct(private readonly ScraperService $scraperService)
    {
        parent::__construct();
    }

    /**
     * Скрапинг колледжей.
     *
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $status = $this->scraperService->collegeList();

        $io = new SymfonyStyle($input, $output);
        if (true === $status) {
            $io->success('Скрапинг окончен успешно');

            return Command::SUCCESS;
        } else {
            $io->error('Не удалось выполнить скрапинг');

            return Command::FAILURE;
        }
    }
}
