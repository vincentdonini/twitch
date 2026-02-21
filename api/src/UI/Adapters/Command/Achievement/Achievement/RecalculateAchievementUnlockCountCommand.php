<?php

namespace App\UI\Adapters\Command\Achievement\Achievement;

use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Ports\AchievementDALInterface;
use App\Domain\Achievement\Service\Progress\RecalculateAchievementUnlockCountService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class RecalculateAchievementUnlockCountCommand extends Command
{
    protected static $defaultName = 'app:achievement:recalculate';

    public function __construct(
        private readonly AchievementDALInterface                  $achievementDAL,
        private readonly RecalculateAchievementUnlockCountService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'achievement',
                null,
                InputOption::VALUE_OPTIONAL,
                'Achievement UUID'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $achievement = null;

        $achievementCode = $input->getOption('achievement');

        $io = new SymfonyStyle($input, $output);

        if ($achievementCode) {
            /* @var Achievement $achievement */
            $achievement = $this->achievementDAL->findOneBy(['code' => $achievementCode]);
            if (!$achievement) {
                $io->error(sprintf('Achievement "%s" not found.', $achievementCode));
                return Command::FAILURE;
            }
        }

        $this->service->recalculate($achievement);

        // -------------------------------------------------------------------------------------------------------------

        $io->newLine();

        $rows = [];
        if ($achievementCode) {
            $rows[] = ['Achievement', $achievementCode];
        }

        if (!empty($rows)) {
            $table = new Table($output);
            $table
                ->setHeaders(['Option', 'Value'])
                ->setRows($rows)
                ->render();
            $io->newLine();
        }

        $io->success('Achievement recalculation completed');

        return Command::SUCCESS;
    }
}
