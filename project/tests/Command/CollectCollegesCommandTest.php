<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CollectCollegesCommand;
use App\Service\ScraperService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CollectCollegesCommandTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testExecuteSuccess(): void
    {
        $scraperService = $this->createMock(ScraperService::class);

        $scraperService->expects($this->once())
            ->method('collegeList')
            ->willReturn(true);

        $command = new CollectCollegesCommand($scraperService);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Скрапинг окончен успешно', $output);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testExecuteFailure(): void
    {
        $scraperService = $this->createMock(ScraperService::class);

        $scraperService->expects($this->once())
            ->method('collegeList')
            ->willReturn(false);

        $command = new CollectCollegesCommand($scraperService);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Не удалось выполнить скрапинг', $output);

        $this->assertEquals(1, $commandTester->getStatusCode());
    }
}
