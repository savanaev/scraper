<?php

declare(strict_types=1);

namespace App\Tests\Colleges\Infrastructure\Console;

use App\Colleges\Domain\Service\CollegeListPersistenceServiceInterface;
use App\Colleges\Infrastructure\Console\ClearCollegesCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ClearCollegesCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $collegePersistenceService = $this->createMock(CollegeListPersistenceServiceInterface::class);

        $collegePersistenceService->expects($this->once())
            ->method('clearAllColleges');

        $application = new Application();
        $application->add(new ClearCollegesCommand($collegePersistenceService));

        $command = $application->find('app:clear-colleges');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('All colleges cleared from the database.', $output);
    }
}
