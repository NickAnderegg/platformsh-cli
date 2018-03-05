<?php

namespace Platformsh\Cli\Command\Variable;

use Platformsh\Cli\Command\CommandBase;
use Platformsh\ConsoleForm\Field\BooleanField;
use Platformsh\ConsoleForm\Field\Field;
use Platformsh\ConsoleForm\Field\OptionsField;
use Platformsh\ConsoleForm\Form;

abstract class VariableCommandBase extends CommandBase
{
    private $form;

    /**
     * @return Form
     */
    protected function getForm()
    {
        return $this->form ?: Form::fromArray($this->getFields());
    }

    /**
     * @return Field[]
     */
    private function getFields()
    {
        return [
            'level' => new OptionsField('Level', [
                'description' => 'The level at which to set the variable',
                'options' => [
                    'project' => 'Project-wide',
                    'environment' => 'Environment-specific',
                ],
            ]),
            'environment' => new OptionsField('Environment', [
                'conditions' => [
                    'level' => 'environment',
                ],
                'optionName' => false,
                'questionLine' => 'On what environment should the variable be set?',
                'optionsCallback' => function () {
                    return array_keys($this->api()->getEnvironments($this->getSelectedProject()));
                },
                'asChoice' => false,
                'includeAsOption' => false,
                'default' => $this->hasSelectedEnvironment() ? $this->getSelectedEnvironment()->id : null,
            ]),
            'name' => new Field('Name', [
                'description' => 'The variable name',
            ]),
            'value' => new Field('Value', [
                'description' => "The variable's value (a string, or JSON)",
            ]),
            'is_json' => new BooleanField('JSON', [
                'description' => 'Whether the variable is JSON-formatted',
                'questionLine' => 'Is the value JSON-formatted',
                'default' => false,
            ]),
            'is_sensitive' => new BooleanField('Sensitive', [
                'conditions' => [
                    'level' => 'environment',
                ],
                'description' => 'Whether the variable is sensitive',
                'questionLine' => 'Is the value sensitive?',
                'default' => false,
            ]),
            'prefix' => new OptionsField('Prefix', [
                'description' => "The variable name's prefix",
                'conditions' => [
                    'name' => function ($name) {
                        return strpos($name, ':') === false;
                    }
                ],
                'options' => [
                    'none' => 'No prefix (wrapped in ' . $this->config()->get('service.env_prefix') . 'VARIABLES)',
                    'env' => 'env: Exposed directly in the environment',
                ],
                'allowOther' => true,
                'default' => 'none',
            ]),
            'is_inheritable' => new BooleanField('Inheritable', [
                'conditions' => [
                    'level' => 'environment',
                ],
                'description' => 'Whether the variable is inheritable by child environments',
                'questionLine' => 'Is the variable inheritable (by child environments)?',
            ]),
            'visible_build' => new BooleanField('Visible at build time', [
                'optionName' => 'visible-build',
                'conditions' => [
                    'level' => 'project',
                ],
                'description' => 'Whether the variable should be visible at build time',
                'questionLine' => 'Should the variable be available at build time?',
            ]),
            'visible_runtime' => new BooleanField('Visible at runtime', [
                'optionName' => 'visible-runtime',
                'conditions' => [
                    'level' => 'project',
                ],
                'description' => 'Whether the variable should be visible at runtime',
                'questionLine' => 'Should the variable be available at runtime?',
            ]),
        ];
    }
}
