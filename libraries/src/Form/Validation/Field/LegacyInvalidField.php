<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2024 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Form\Validation\Field;

use Joomla\CMS\Form\Constraint\ConstraintInterface;
use Joomla\CMS\Form\Validation\FieldValidationResponseInterface;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Temporary class to handle fields that return \Exceptions for compatibility with old validation responses
 *
 * @since       __DEPLOY_VERSION__
 * @deprecated  7.0  Migrate the response of form fields to {@link \Joomla\CMS\Form\Validation\FieldValidationResponse}
 *                   and constraints.
 */
class LegacyInvalidField implements FieldValidationResponseInterface
{
    /**
     * The name of the field
     *
     * @var    string
     * @since  __DEPLOY_VERSION__
     */
    private string $name = '';

    /**
     * The group of the field
     *
     * @var    string
     * @since  __DEPLOY_VERSION__
     */
    private string $group = '';

    /**
     * Field label for the error message
     *
     * @var    string
     * @since  __DEPLOY_VERSION__
     */
    private string $fieldLabel;

    /**
     * Validation exception
     *
     * @var    \Exception
     * @since  __DEPLOY_VERSION__
     */
    private \Exception $validationException;

    /**
     * Method to instantiate the object.
     *
     * @param   string      $fieldLabel           The human friendly field label.
     * @param   string      $name                 The name of the field.
     * @param   string      $group                The group of the field.
     * @param   \Exception  $validationException  The exception thrown by the validate method.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function __construct(string $name, string $group, string $fieldLabel, \Exception $validationException)
    {
        $this->name                = $name;
        $this->group               = $group;
        $this->fieldLabel          = $fieldLabel;
        $this->validationException = $validationException;
    }

    /**
     * Count constraints tested. Note for this class specifically then this cannot be relied on!!
     *
     * @return int<0,max> The custom count as an integer.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function count(): int
    {
        return 1;
    }

    /**
     * Was the field data valid or not. If there are no constraints on the field this should return true.
     *
     * @return  bool
     *
     * @since   __DEPLOY_VERSION__
     */
    public function isValid(): bool
    {
        return false;
    }

    /**
     * List of validation properties tested that returned a negative result
     *
     * @return  string[]
     *
     * @since   __DEPLOY_VERSION__
     */
    public function getInvalidConstraints(): array
    {
        return ['invalidLegacyFieldResponse'];
    }

    /**
     * Method to get a constraint result for the field.
     *
     * @param string $name The name of the property
     *
     * @return  ConstraintInterface
     *
     * @since   __DEPLOY_VERSION__
     */
    public function getConstraint(string $name): ConstraintInterface
    {
        return new class ($this->fieldLabel, $this->validationException) implements ConstraintInterface {
            /**
             * Field label for the error message
             *
             * @var    string
             * @since  __DEPLOY_VERSION__
             */
            private string $fieldLabel;

            /**
             * Validation exception
             *
             * @var    \Exception
             * @since  __DEPLOY_VERSION__
             */
            private \Exception $validationException;

            /**
             * Method to instantiate the object.
             *
             * @param   string      $fieldLabel           The human friendly field label.
             * @param   \Exception  $validationException  The human friendly field label.
             *
             * @since   __DEPLOY_VERSION__
             */
            public function __construct(string $fieldLabel, \Exception $validationException)
            {
                $this->fieldLabel          = $fieldLabel;
                $this->validationException = $validationException;
            }

            /**
             * Was the data validated in the constraint.
             *
             * @return  bool
             *
             * @since   __DEPLOY_VERSION__
             */
            public function isValid(): bool
            {
                return false;
            }

            /**
             * Name of the constraint - note this is for machine access and should be unique for a form field.
             *
             * @return  string
             *
             * @since   __DEPLOY_VERSION__
             */
            public function getName(): string
            {
                return 'invalidLegacyFieldResponse';
            }

            /**
             * Get an error message that can be displayed to a user about how to remediate the constraint.
             *
             * @return  string
             *
             * @since   __DEPLOY_VERSION__
             */
            public function getErrorMessage(): string
            {
                return $this->validationException->getMessage();
            }
        };
    }

    /**
     * Name of the field.
     *
     * @return  string
     *
     * @since   __DEPLOY_VERSION__
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Group of the field.
     *
     * @return  string
     *
     * @since   __DEPLOY_VERSION__
     */
    public function getGroup(): string
    {
        return $this->group;
    }
}
