<?php

namespace App\UI\Adapters\Command\Achievement\Achievement;

use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Ports\AchievementDALInterface;
use App\Domain\Achievement\Service\Progress\AchievementProgressRecalculationService;
use App\Domain\User\Entity\User;
use App\Domain\User\Ports\UserDALInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class UpdateUserAchievementsCommand extends Command
{
    protected static $defaultName = 'app:user:achievement:recalculate';

    public function __construct(
        private readonly UserDALInterface                        $userDAL,
        private readonly AchievementDALInterface                 $achievementDAL,
        private readonly AchievementProgressRecalculationService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'user',
                null,
                InputOption::VALUE_OPTIONAL,
                'User UUID'
            )
            ->addOption(
                'achievement',
                null,
                InputOption::VALUE_OPTIONAL,
                'Achievement UUID'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user        = null;
        $achievement = null;

        $userId          = $input->getOption('user');
        $achievementCode = $input->getOption('achievement');

        $io = new SymfonyStyle($input, $output);

        if ($userId) {
            /* @var User $user */
            $user = $this->userDAL->getById($userId);
            if (!$user) {
                $io->error(sprintf('User "%s" not found.', $userId));
                return Command::FAILURE;
            }
        }

        if ($achievementCode) {
            /* @var Achievement $achievement */
            $achievement = $this->achievementDAL->findOneBy(['code' => $achievementCode]);
            if (!$achievement) {
                $io->error(sprintf('Achievement "%s" not found.', $achievementCode));
                return Command::FAILURE;
            }
        }

        $this->service->recalculate($user, $achievement);

        // -------------------------------------------------------------------------------------------------------------

        $io->newLine();

        $rows = [];
        if ($userId) {
            $rows[] = ['User', $userId . ' ( ' . $user->getEmail() . ' )'];
        }
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
