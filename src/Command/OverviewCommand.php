<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\MultiLineResponse;
use Rvdv\Nntp\Response\Response;

/**
 * OverviewCommand
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class OverviewCommand extends Command implements CommandInterface
{
    /**
     * @var int
     */
    private $from;

    /**
     * @var int
     */
    private $to;

    /**
     * @var array
     */
    private $format;

    /**
     * Constructor
     *
     * @param int   $from   The article number where the range begins.
     * @param int   $to     The article number where the range ends.
     * @param array $format The format of the articles in response.
     */
    public function __construct($from, $to, array $format)
    {
        $this->from = $from;
        $this->to = $to;
        $this->format = array_merge(array('number' => false), $format);

        parent::__construct(array(), true);
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return sprintf('XOVER %d-%d', $this->from, $this->to);
    }

    /**
     * {@inheritDoc}
     */
    public function getExpectedResponseCodes()
    {
        return array(
            Response::OVERVIEW_INFORMATION_FOLLOWS => 'onOverviewInformationFollows',
            Response::NO_NEWSGROUP_CURRENT_SELECTED => 'onNoNewsGroupCurrentSelected',
            Response::NO_ARTICLES_SELECTED => 'onNoArticlesSelected',
        );
    }

    public function onOverviewInformationFollows(MultiLineResponse $response)
    {
        $lines = $response->getLines();

        foreach ($lines as $index => $line) {
            $segments = explode("\t", $line);

            $field = 0;
            $article = new \stdClass();

            foreach ($this->format as $name => $full) {
                $value = $full ? ltrim(substr($segments[$field], strpos($segments[$field], ':') + 1), " \t") : $segments[$field];
                $article->{$name} = $value;

                $field++;
            }

            $this->result[] = $article;
        }

        unset($lines);
    }

    public function onNoNewsGroupCurrentSelected(Response $response)
    {
        throw new RuntimeException('A group must be selected first before getting an overview.');
    }

    public function onNoArticlesSelected(Response $response)
    {
        throw new RuntimeException(sprintf('No articles selected in the given range %d-%d.', $this->from, $this->to));
    }
}
