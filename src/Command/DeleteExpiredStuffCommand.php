<?php
namespace App\Command;

use App\Service\PwResetServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteExpiredStuffCommand extends Command {
    protected static $defaultName = 'app:delrq';
    private PwResetServiceInterface $pwResetService;

    /**
     * DeleteExpiredStuffCommand constructor.
     * @param PwResetServiceInterface $pwResetService
     */
    public function __construct(PwResetServiceInterface $pwResetService)
    {
        parent::__construct();
        $this->pwResetService = $pwResetService;
    }

    protected function configure()
    {
        $this->setDescription("Deletes all expired Password reset requests.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Deleting Expired Password Tokens at ...");

        $deletedTokens = $this->pwResetService->deleteExpieredTokens();

        $output->write("Deleted: $deletedTokens db.");

        return 0;
    }
}