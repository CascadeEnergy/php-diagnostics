<?php

namespace CascadeEnergy\Diagnostics;

class Series extends AbstractDiagnostic
{
    /** @var DiagnosticInterface[] */
    private $diagnosticList = [];

    public function addDiagnostic(DiagnosticInterface $diagnostic)
    {
        $this->diagnosticList[] = $diagnostic;
    }

    public function isOk()
    {
        foreach ($this->diagnosticList as $diagnostic) {
            if (!$diagnostic->isOk()) {
                return false;
            }
        }

        return true;
    }
}
