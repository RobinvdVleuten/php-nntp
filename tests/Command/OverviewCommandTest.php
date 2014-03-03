<?php

namespace Rvdv\Nntp\Tests\Command;

use Rvdv\Nntp\Command\OverviewCommand;
use Rvdv\Nntp\Response\Response;

/**
 * OverviewCommandTest
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class OverviewCommandTest extends CommandTest
{
    public function testItExpectsMultilineResponses()
    {
        $command = $this->createCommandInstance();
        $this->assertTrue($command->isMultiLine());
    }

    public function testItHasDefaultResult()
    {
        $command = $this->createCommandInstance();
        $this->assertEmpty($command->getResult());
    }

    public function testItReturnsStringWhenExecuting()
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('XOVER 1-10', $command->execute());
    }

    public function testItReceivesAResultWhenOverviewInformationFollowsResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\MultiLineResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $lines = array(
            "123456789\tRe: Are you checking out NNTP?\trobinvdvleuten@example.com (\"Robin van der Vleuten\")\tSat,3 Aug 2013 13:19:22 -0000\t<nntp123456789@nntp>\t<nntp987654321@nntp>\t321\t123\tXref: nntp:123456789",
        );

        $response->expects($this->once())
            ->method('getLines')
            ->will($this->returnValue($lines));

        $command->onOverviewInformationFollows($response);

        $result = $command->getResult();
        $this->assertCount(1, $result);

        $article = reset($result);
        $this->assertEquals('123456789', $article->number);
        $this->assertEquals('Re: Are you checking out NNTP?', $article->subject);
        $this->assertEquals('robinvdvleuten@example.com ("Robin van der Vleuten")', $article->from);
        $this->assertEquals('Sat,3 Aug 2013 13:19:22 -0000', $article->date);
        $this->assertEquals('<nntp123456789@nntp>', $article->message_id);
        $this->assertEquals('<nntp987654321@nntp>', $article->references);
        $this->assertEquals('321', $article->bytes);
        $this->assertEquals('123', $article->lines);
        $this->assertEquals('nntp:123456789', $article->xref);
    }

    public function testItErrorsWhenNoNewsGroupCurrentSelectedResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        try {
            $command->onNoNewsGroupCurrentSelected($response);
            $this->fail('->onNoNewsGroupCurrentSelected() throws a Rvdv\Nntp\Exception\RuntimeException because a group must be selected first before getting an overview');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onNoNewsGroupCurrentSelected() because a group must be selected first before getting an overview');
        }
    }

    public function testItErrorsWhenNoArticlesSelectedResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        try {
            $command->onNoArticlesSelected($response);
            $this->fail('->onNoArticlesSelected() throws a Rvdv\Nntp\Exception\RuntimeException because no articles selected in the given range');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onNoArticlesSelected() because no articles selected in the given range');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function createCommandInstance()
    {
        return new OverviewCommand(1, 10, array(
            'subject' => false,
            'from' => false,
            'date' => false,
            'message_id' => false,
            'references' => false,
            'bytes' => false,
            'lines' => false,
            'xref' => true,
        ));
    }

    /**
     * {@inheritDoc}
     */
    protected function getRFCResponseCodes()
    {
        return array(
            Response::OVERVIEW_INFORMATION_FOLLOWS,
            Response::NO_NEWSGROUP_CURRENT_SELECTED,
            Response::NO_ARTICLES_SELECTED,
        );
    }
}
