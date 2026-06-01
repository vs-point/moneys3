<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Enum;

use VsPoint\MoneyS3\GraphQL\GraphQLEnumValue;

/**
 * Type of employee absence within a wage (`absences[].type`).
 *
 * Tokens verified against the live Money S3 schema (`AbsenceType`).
 */
enum AbsenceType: string implements GraphQLEnumValue
{
    case vacation = 'VACATION';
    case paidVacation = 'PAID_VACATION';
    case sickDays = 'SICK_DAYS';
    case paragraph = 'PARAGRAPH';
    case unpaidLeave = 'UNPAID_LEAVE';
    case unexcusedAbsence = 'UNEXCUSED_ABSENCE';
    case illness = 'ILLNESS';
    case quarantine = 'QUARANTINE';
    case workInjury = 'WORK_INJURY';
    case paragraphWithoutCompensation = 'PARAGRAPH_WITHOUT_COMPENSATION';
    case nursing = 'NURSING';
    case longTermNursing = 'LONG_TERM_NURSING';
    case maternityLeave = 'MATERNITY_LEAVE';
    case additionalMaternityLeave = 'ADDITIONAL_MATERNITY_LEAVE';
    case paternityLeave = 'PATERNITY_LEAVE';
    case legalStrike = 'LEGAL_STRIKE';
    case obstaclesEmployer = 'OBSTACLES_EMPLOYER';
    case longTermLeave = 'LONG_TERM_LEAVE';
    case kurzarbeit = 'KURZARBEIT';
    case shortTimeWork = 'SHORT_TIME_WORK';
    case detention = 'DETENTION';
    case militaryService = 'MILITARY_SERVICE';
    case daysBeforeStarting = 'DAYS_BEFORE_STARTING';
    case plannedVacation = 'PLANNED_VACATION';
    case otherAbsence = 'OTHER_ABSENCE';

    public function graphQLValue(): string
    {
        return $this->value;
    }
}
