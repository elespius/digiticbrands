<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2023 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Event\Privacy;

use Joomla\CMS\Event\Result\ResultAware;
use Joomla\CMS\Event\Result\ResultAwareInterface;
use Joomla\CMS\User\User;
use Joomla\Component\Privacy\Administrator\Export\Domain;
use Joomla\Component\Privacy\Administrator\Table\RequestTable;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Class for Privacy events.
 * Example:
 *     new ExportRequestEvent('onEventName', ['subject' => $requestTable, 'user' => $user]);
 *
 * @since  __DEPLOY_VERSION__
 */
class ExportRequestEvent extends PrivacyEvent implements ResultAwareInterface
{
    use ResultAware;

    /**
     * The argument names, in order expected by legacy plugins.
     *
     * @var array
     *
     * @since  __DEPLOY_VERSION__
     * @deprecated 5.0 will be removed in 6.0
     */
    protected $legacyArgumentsOrder = ['subject', 'user'];

    /**
     * Constructor.
     *
     * @param   string  $name       The event name.
     * @param   array   $arguments  The event arguments.
     *
     * @throws  \BadMethodCallException
     *
     * @since   __DEPLOY_VERSION__
     */
    public function __construct($name, array $arguments = [])
    {
        parent::__construct($name, $arguments);

        if (!\array_key_exists('subject', $this->arguments)) {
            throw new \BadMethodCallException("Argument 'subject' of event {$name} is required but has not been provided");
        }

        if (!\array_key_exists('user', $this->arguments)) {
            throw new \BadMethodCallException("Argument 'user' of event {$name} is required but has not been provided");
        }
    }

    /**
     * Setter for the subject argument.
     *
     * @param   RequestTable  $value  The value to set
     *
     * @return  RequestTable
     *
     * @since  __DEPLOY_VERSION__
     */
    protected function setSubject(RequestTable $value): RequestTable
    {
        return $value;
    }

    /**
     * Setter for the user argument.
     *
     * @param   ?User  $value  The value to set
     *
     * @return  ?User
     *
     * @since  __DEPLOY_VERSION__
     */
    protected function setUser(?User $value): ?User
    {
        return $value;
    }

    /**
     * Getter for the request.
     *
     * @return  RequestTable
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getRequest(): RequestTable
    {
        return $this->arguments['subject'];
    }

    /**
     * Getter for the user.
     *
     * @return  ?User
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getUser(): ?User
    {
        return $this->arguments['user'];
    }

    /**
     * Checks the type of the data being appended to the result argument.
     *
     * @param   mixed  $data  The data to type check
     *
     * @return  void
     * @throws  \InvalidArgumentException
     *
     * @internal
     * @since   __DEPLOY_VERSION__
     */
    public function typeCheckResult($data): void
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException(sprintf('Event %s only accepts Array results.', \get_class($this)));
        }

        // Validate items in array
        foreach ($data as $item) {
            if (!$item instanceof Domain) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Event %s only accepts Joomla\Component\Privacy\Administrator\Export\Domain in result array.',
                        \get_class($this)
                    )
                );
            }
        }
    }
}
