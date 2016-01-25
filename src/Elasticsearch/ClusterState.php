<?php

namespace CascadeEnergy\Diagnostics\Elasticsearch;

use CascadeEnergy\Diagnostics\AbstractDiagnostic;
use Elasticsearch\Client;

class ClusterState extends AbstractDiagnostic
{
    /** @var Client */
    private $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function isOk()
    {
        try {
            $health = $this->elasticsearch->cluster()->health();
        } catch (\Exception $ex) {
            $this->logger->error("Elasticsearch cluster status could not be retrieved");
            return false;
        }

        if (!is_array($health) || !array_key_exists('status', $health)) {
            $this->logger->error("Elasticsearch cluster status was not returned in the expected format");
            return false;
        }

        $clusterStatus = $health['status'];

        if ($clusterStatus !== 'green') {
            $this->logger->warning("Elasticsearch cluster status: $clusterStatus", ['clusterHealth' => $health]);
            return false;
        }

        return true;
    }
}
