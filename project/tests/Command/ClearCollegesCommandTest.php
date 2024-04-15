<?php

namespace App\Tests\Command;

use App\Command\ClearCollegesCommand;
use App\Service\CollegeListService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ClearCollegesCommandTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testExecute(): void
    {
        $collegeListService = $this->createMock(CollegeListService::class);

        $collegeListService->expects($this->once())
            ->method('clear')
            ->willReturn(true);

        $command = new ClearCollegesCommand($collegeListService);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}
