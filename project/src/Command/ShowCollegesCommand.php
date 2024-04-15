<?php

namespace App\Command;

use App\Service\CollegeListService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Класс по отображению информации о колледжах.
 */
#[AsCommand(
    name: 'app:show-colleges',
    description: 'Add a short description for your command',
)]
class ShowCollegesCommand extends Command
{
    public function __construct(private CollegeListService $collegeListService)
    {
        parent::__construct();
    }

    /**
     * Отображение информации о колледжах.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->displayColleges($io);

        return Command::SUCCESS;
    }

    /**
     * Отображение списка колледжей.
     *
     * @param SymfonyStyle $io Объект для вывода информации
     */
    private function displayColleges(SymfonyStyle $io): void
    {
        $page = 1;
        $pageSize = 10;

        do {
            $colleges = $this->collegeListService->getColleges($page, $pageSize);
            foreach ($colleges as $college) {
                $io->writeln(sprintf('%s - %s', $college->getName(), $college->getUrl()));
            }

            $action = $this->getActionFromUser($io);

            if ('next' === $action) {
                ++$page;
            } elseif ('prev' === $action) {
                $page = max(1, $page - 1);
            }
        } while ('exit' !== $action);
    }

    /**
     * Запрос действия пользователя.
     *
     * @param SymfonyStyle $io Объект для вывода информации
     */
    private function getActionFromUser(SymfonyStyle $io): string
    {
        return $io->ask(
            'Введите "next" для следующей страницы, "prev" для предыдущей страницы или "exit" для выхода:',
            null,
            function ($value) {
                if (!in_array(strtolower($value), ['next', 'prev', 'exit'])) {
                    throw new \RuntimeException('Неверное действие.');
                }

                return $value;
            }
        );
    }
}
