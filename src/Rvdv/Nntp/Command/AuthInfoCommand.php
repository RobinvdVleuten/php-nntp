<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\Response;

/**
 * AuthInfoCommand
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class AuthInfoCommand extends Command implements CommandInterface
{
    const AUTHINFO_USER = 'USER';
    const AUTHINFO_PASS = 'PASS';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $value;

    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return sprintf('AUTHINFO %s %s', $this->type, $this->value);
    }

    /**
     * {@inheritDoc}
     */
    public function getExpectedResponseCodes()
    {
        return array(
            Response::AUTHENTICATION_ACCEPTED => 'onAuthenticationAccepted',
            Response::PASSWORD_REQUIRED => 'onPasswordRequired',
            Response::AUTHENTICATION_REJECTED => 'onAuthenticationRejected',
            Response::AUTHENTICATION_OUTOFSEQUENCE => 'onAuthenticationOutOfSequence',
        );
    }

    public function onAuthenticationAccepted(Response $response)
    {
        return;
    }

    public function onPasswordRequired(Response $response)
    {
        return;
    }

    public function onAuthenticationRejected(Response $response)
    {
        throw new RuntimeException(sprintf('Authentication failed with given value for type %s', $this->type));
    }

    public function onAuthenticationOutOfSequence(Response $response)
    {
        throw new RuntimeException(sprintf('Authentication is out of sequence for type %s', $this->type));
    }
}
