<?php

namespace CascadeEnergy\Diagnostics;

use Psr\Log\LoggerAwareTrait;

abstract class AbstractDiagnostic implements DiagnosticInterface
{
    use LoggerAwareTrait;
}