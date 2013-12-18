<?php

namespace Rvdv\Nntp\Tests\Command;

use Rvdv\Nntp\Command\XFeatureCommand;
use Rvdv\Nntp\Response\Response;

/**
 * XFeatureCommandTest
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class XFeatureCommandTest extends CommandTest
{
    public function testItNotExpectsMultilineResponses()
    {
        $command = $this->createCommandInstance();
        $this->assertFalse($command->isMultiLine());
    }

    public function testItHasDefaultResult()
    {
        $command = $this->createCommandInstance();
        $this->assertFalse($command->getResult());
    }

    public function testItReturnsStringWhenExecuting()
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('XFEATURE COMPRESS GZIP', $command->execute());
    }

    public function testItReceivesAResultWhenXFeatureEnabledResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $command->onXFeatureEnabled($response);

        $this->assertTrue($command->getResult());
    }

    /**
     * {@inheritDoc}
     */
    protected function createCommandInstance()
    {
        return new XFeatureCommand(XFeatureCommand::COMPRESS_GZIP);
    }

    /**
     * {@inheritDoc}
     */
    protected function getRFCResponseCodes()
    {
        return array(
            Response::XFEATURE_ENABLED,
        );
    }
}
