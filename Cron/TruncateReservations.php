<?php

namespace Lotsofpixels\ReservationTruncate\Cron;

use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;
use Lotsofpixels\ReservationTruncate\Model\Config;

class TruncateReservations
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        ResourceConnection $resource,
        LoggerInterface $logger,
        Config $config
    ) {
        $this->resource = $resource;
        $this->logger = $logger;
        $this->config = $config;
    }

    public function execute()
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('inventory_reservation');

        try {
            if (!$connection->isTableExists($tableName)) {
                $this->logger->info(sprintf(
                    '[ReservationTruncate] Table does not exist, skipping: %s',
                    $tableName
                ));
                return;
            }

            $connection->truncateTable($tableName);

            $this->logger->info(sprintf(
                '[ReservationTruncate] Truncated table: %s',
                $tableName
            ));
        } catch (\Throwable $e) {
            $this->logger->error(
                '[ReservationTruncate] Failed to truncate inventory_reservation: ' . $e->getMessage(),
                ['exception' => $e]
            );
        }
    }
}