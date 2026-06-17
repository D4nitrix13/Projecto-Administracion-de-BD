<?php

declare(strict_types=1);

require_once __DIR__ . "/../bootstrap.php";

class_alias(\App\Service\FacturaService::class, 'FacturaService');
class_alias(\App\Service\FacturaValidationService::class, 'FacturaValidationService');
class_alias(\App\Service\FacturaCalculationService::class, 'FacturaCalculationService');
