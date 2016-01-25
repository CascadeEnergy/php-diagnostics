<?php

namespace CascadeEnergy\Tests\Diagnostics\Elasticsearch;

use CascadeEnergy\Diagnostics\Elasticsearch\ClusterState;

class ClusterStateTest extends \PHPUnit_Framework_TestCase
{
    /** @var ClusterState */
    private $clusterState;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $elasticsearchClient;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $cluster;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    public function setUp()
    {
        $this->elasticsearchClient = $this
            ->getMockBuilder('Elasticsearch\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $this->cluster = $this
            ->getMockBuilder('Elasticsearch\Namespaces\ClusterNamespace')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elasticsearchClient->expects($this->once())->method('cluster')->willReturn($this->cluster);

        $this->logger = $this->getMock('Psr\Log\LoggerInterface');

        /** @noinspection PhpParamsInspection */
        $this->clusterState = new ClusterState($this->elasticsearchClient);
        $this->clusterState->setLogger($this->logger);
    }

    public function testItShouldFailIfTheClusterStatusCannotBeRetrieved()
    {
        $ex = new \Exception('Foo');

        $this->cluster->expects($this->once())->method('health')->willThrowException($ex);
        $this->logger->expects($this->once())->method('error');

        $this->assertFalse($this->clusterState->isOk());
    }

    public function testItShouldFailIfTheClusterStatusIsInAnUnexpectedFormat()
    {
        $this->cluster->expects($this->once())->method('health')->willReturn('foo-bar');
        $this->logger->expects($this->once())->method('error');

        $this->assertFalse($this->clusterState->isOk());
    }

    public function testItShouldFailIfTheClusterStatusIsNotGreen()
    {
        $this->cluster->expects($this->once())->method('health')->willReturn(['status' => 'red']);
        $this->logger->expects($this->once())->method('warning');

        $this->assertFalse($this->clusterState->isOk());
    }

    public function testItShouldPassIfTheClusterStatusIsGreen()
    {
        $this->cluster->expects($this->once())->method('health')->willReturn(['status' => 'green']);
        $this->logger->expects($this->never())->method('error');
        $this->logger->expects($this->never())->method('warning');

        $this->assertTrue($this->clusterState->isOk());
    }
}
