<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/8
 * Time: 17:49
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class BusinessAttachment
 * @package catchAdmin\basisinfo\model
 */
class BusinessAttachment extends CatchModel
{
    protected $name = 'business_attachment';

    protected $pk = 'id';

    protected $field = [
        'id',
        'business_license_id',
        'business_license_url',
        'check_business_license',
        'production_license_url',
        'check_production_license',
        'record_certificate_url',
        'check_record_certificate',
        'invoice_information_url',
        'check_invoice_information',
        'person_authorization_url',
        'check_person_authorization',
        'assurance_agreement_url',
        'check_assurance_agreement',
        'delivery_template_url',
        'check_delivery_template',
        'seal_filing_template_url',
        'check_seal_filing_template',
        'system_survey_form_url',
        'check_system_survey_form',
        'annual_report_url',
        'check_annual_report'
    ];
}