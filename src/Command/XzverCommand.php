<?php

namespace Rvdv\Nntp\Command;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class XzverCommand extends XoverCommand implements CommandInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return sprintf('XZVER %d-%d', $this->from, $this->to);
    }

    /**
     * {@inheritdoc}
     */
    public function isCompressed()
    {
        return true;
    }
}
