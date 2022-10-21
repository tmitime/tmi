<?php

namespace App\Enum;

enum ReportingPeriod: string
{
    case CURRENT_MONTH = 'current_month';
    case PREVIOUS_MONTH = 'previous_month';
    case OVERALL = 'overall';
}
