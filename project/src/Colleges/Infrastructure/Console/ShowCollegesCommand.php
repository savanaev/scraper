<?php

declare(strict_types=1);

namespace App\Colleges\Infrastructure\Console;

use App\Colleges\Domain\Entity\CollegeDetails;
use App\Colleges\Domain\Service\CollegeDetailsPersistenceServiceInterface;
use App\Colleges\Domain\Service\CollegeListPersistenceServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:show-colleges',
    description: 'View list of colleges',
)]
final class ShowCollegesCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private SymfonyStyle $io;

    /**
     * @param CollegeListPersistenceServiceInterface $collegeListPersistenceService
     * @param CollegeDetailsPersistenceServiceInterface $collegeDetailsPersistenceService
     */
    public function __construct(
        private readonly CollegeListPersistenceServiceInterface $collegeListPersistenceService,
        private readonly CollegeDetailsPersistenceServiceInterface $collegeDetailsPersistenceService
    )
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
        $collegesList = $this->getCollegeList();
        $this->displayCollegeList($collegesList, $input, $output);

        return Command::SUCCESS;
    }

    /**
     * Получение списка колледжей.
     *
     * @return array
     */
    private function getCollegeList(): array
    {
        $colleges = $this->collegeListPersistenceService->findAll();
        $collegesList = [];
        foreach ($colleges as $college) {
            $collegesList[$college->getId()] = sprintf(
                'Name: %s, City: %s, State: %s',
                $college->getName(), $college->getCity(), $college->getState()
            );
        }

        return $collegesList;
    }

    /**
     * Отображение списка колледжей.
     *
     * @param array $collegesList Список колледжей.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function displayCollegeList(array $collegesList, InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select a college:',
            $collegesList
        );
        $question->setErrorMessage('College %s is invalid.');
        $collegeName = $helper->ask($input, $output, $question);
        $this->io->comment('You selected: ' . $collegeName);

        $this->displayCollegeDetails($collegesList, $collegeName);
    }


    /**
     * Отображение деталей колледжа.
     *
     * @param array $collegesList Список колледжей.
     * @param string $collegeName Название колледжа.
     * @return void
     */
    private function displayCollegeDetails(array $collegesList, string $collegeName): void
    {
        $collegeDetails = $this->getCollegeDetails($collegesList, $collegeName);
        $this->io->comment([
            'College Details:',
            'Name: ' . $collegeDetails->getName(),
            'Address: ' . $collegeDetails->getAddress(),
            'Phone: ' . $collegeDetails->getPhone(),
            'Website:  ' . $collegeDetails->getWebsite(),
        ]);
    }

    /**
     * Получение деталей колледжа.
     *
     * @param array $collegesList Список колледжей.
     * @param string $collegeName Название колледжа.
     * @return CollegeDetails|null
     */
    private function getCollegeDetails(array $collegesList, string $collegeName): ?CollegeDetails
    {
        $selectedCollegeId = (int) array_search($collegeName, $collegesList, true);

        if ($selectedCollegeId > 0) {
            return $this->collegeDetailsPersistenceService->find($selectedCollegeId);
        }

        return null;
    }
}
