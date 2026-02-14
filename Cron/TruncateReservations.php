<?php

namespace Lotsofpixels\ReservationTruncate\Cron;

use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

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

    public function __construct(
        ResourceConnection $resource,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->logger = $logger;
    }

    public function execute()
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('inventory_reservation');

        try {
            if (!$connection->isTableExists($tableName)) {
                $this->logger->info(sprintf(
                    '[Vendor_ReservationTruncate] Table does not exist, skipping: %s',
                    $tableName
                ));
                return;
            }

            $connection->truncateTable($tableName);

            $this->logger->info(sprintf(
                '[Vendor_ReservationTruncate] Truncated table: %s',
                $tableName
            ));
        } catch (\Throwable $e) {
            $this->logger->error(
                '[Vendor_ReservationTruncate] Failed to truncate inventory_reservation: ' . $e->getMessage(),
                ['exception' => $e]
            );
        }
    }
}