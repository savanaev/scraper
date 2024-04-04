<?php

declare(strict_types=1);

namespace App\Colleges\Infrastructure\Console;

use App\Colleges\Domain\Service\CollegeDetailsScraperServiceInterface;
use App\Colleges\Domain\Service\CollegeListPersistenceServiceInterface;
use App\Colleges\Domain\Service\CollegeListScraperServiceInterface;
use App\Colleges\Domain\Service\HashGeneratorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'app:collect-colleges',
    description: 'Collection of colleges for subsequent processing.'
)]
final class CollectCollegesCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private SymfonyStyle $io;

    /**
     * @param CollegeListScraperServiceInterface $collegeListScraperService
     * @param CollegeDetailsScraperServiceInterface $collegeDetailsScraperService
     * @param CollegeListPersistenceServiceInterface $collegeListPersistenceService
     * @param HashGeneratorInterface $hashGenerator
     */
    public function __construct(
        private readonly  CollegeListScraperServiceInterface $collegeListScraperService,
        private readonly  CollegeDetailsScraperServiceInterface $collegeDetailsScraperService,
        private readonly  CollegeListPersistenceServiceInterface $collegeListPersistenceService,
        private readonly  HashGeneratorInterface $hashGenerator
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
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $collegeList = $this->collegeListScraperService->scrapeCollegesList();
        $this->processColleges($collegeList);

        return Command::SUCCESS;
    }

    /**
     * Обработка списка колледжей.
     *
     * @param array $collegeList
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function processColleges(array $collegeList): void
    {
        $collegesToSave = [];

        foreach ($collegeList as $college) {
            $collegeHash = $this->hashGenerator->generateHash($college);
            $existingCollege = $this->collegeListPersistenceService->findByHash($collegeHash);
            if ($existingCollege === null) {
                $this->io->comment("New college added: {$college->getName()}");
                $collegeDetails = $this->collegeDetailsScraperService->scrapeCollegeDetails($college);
                $collegesToSave[] = ['college' => $college, 'college_details' => $collegeDetails];
            }
        }

        $this->updateColleges($collegesToSave);
    }

    /**
     * Обновление новых колледжей.
     *
     * @param array $collegeList Список колледжей для сохранения.
     * @return void
     */
    private function updateColleges(array $collegeList): void
    {
        $newCollegesCount = count($collegeList);
        if ($newCollegesCount > 0) {
            $this->collegeListPersistenceService->removeOldColleges($newCollegesCount);
            $this->collegeListPersistenceService->saveColleges($collegeList);
            $this->io->comment("New colleges added: $newCollegesCount");
            $this->io->success('Colleges successfully collected and saved.');
        } else {
            $this->io->comment('No new colleges found.');
        }
    }
}
