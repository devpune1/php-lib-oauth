<?php

namespace fkooman\OAuth;

interface TemplateInterface
{
    /**
     * Render the template.
     *
     * @param string $templateName      the name of the template
     * @param array  $templateVariables the variables used in the template
     *
     * @return string the rendered template
     */
    public function render($templateName, array $templateVariables);
}
