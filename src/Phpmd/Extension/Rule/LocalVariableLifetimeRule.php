<?php

namespace Phpmd\Extension\Rule;

use PHPMD\AbstractNode;
use PHPMD\Rule;
use PHPMD\Rule\AbstractLocalVariable;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

class LocalVariableLifetimeRule extends AbstractLocalVariable implements FunctionAware, MethodAware
{
    const VARIABLE_CHILDREN_TYPE = 'Variable';

    const ALLOWED_LINES_INTERVAL_PROPERTY = 'allowedLinesInterval';

    /**
     * {@inheritdoc}
     */
    public function apply(AbstractNode $node)
    {
        $localVariables = [];

        $this->collectLocalVariables($node, $localVariables);

        $this->removeParameters($node, $localVariables);

        $this->checkLocalVariablesLifeTime($localVariables);
    }

    private function collectLocalVariables(AbstractNode $node, array &$localVariables)
    {
        foreach ($node->findChildrenOfType(self::VARIABLE_CHILDREN_TYPE) as $variable) {
            if($this->isLocal($variable)) {
                $localVariables[$variable->getImage()]['node'] = $variable;
                $localVariables[$variable->getImage()]['usage'][] = $variable->getEndLine();
            }
        }

        return $localVariables;
    }

    private function removeParameters(AbstractNode $node, array &$localVariables)
    {
        $parameters = $node->getFirstChildOfType('FormalParameters');

        $declarators = $parameters->findChildrenOfType('VariableDeclarator');

        foreach ($declarators as $declarator) {
            unset($localVariables[$declarator->getImage()]);
        }
    }

    private function checkLocalVariablesLifeTime(array $localVariables)
    {
        foreach ($localVariables as $localVariable) {
            if($this->localVariableUsedMoreThanOneTime($localVariable) && $this->localVariableSecondUsageViolation($localVariable)) {
                $this->addViolation(
                    $localVariable['node'],
                    [
                        $localVariable['node']->getName(),
                        $localVariable['usage'][1] - $localVariable['usage'][0],
                        $this->getIntProperty(self::ALLOWED_LINES_INTERVAL_PROPERTY),
                    ]
                );
            }
        }
    }

    private function localVariableSecondUsageViolation($localVariable)
    {
        return $localVariable['usage'][1] - $localVariable['usage'][0] > $this->getIntProperty(self::ALLOWED_LINES_INTERVAL_PROPERTY);
    }

    private function localVariableUsedMoreThanOneTime($localVariable)
    {
        return isset($localVariable['usage'][1]);
    }
}

