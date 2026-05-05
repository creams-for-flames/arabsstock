<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    "accepted"              => "The :attribute must be accepted.",
    "active_url"            => "The :attribute is not a valid URL.",
    "after"                 => "The :attribute must be a date after :date.",
    "alpha"                 => "The :attribute may only contain letters.",
    "alpha_dash"            => "The :attribute may only contain letters, numbers, and dashes.",
    "ascii_only"            => "The :attribute may only contain letters, numbers, and dashes.",
    "alpha_num"             => "The :attribute may only contain letters and numbers.",
    "array"                 => "The :attribute must be an array.",
    "before"                => "The :attribute must be a date before :date.",
    "between"               => [
        "numeric" => "The :attribute must be between :min and :max.",
        "file"    => "The :attribute must be between :min and :max kilobytes.",
        "string"  => "The :attribute must be between :min and :max characters.",
        "array"   => "The :attribute must have between :min and :max items.",
    ],
    "boolean"               => "The :attribute field must be true or false.",
    "confirmed"             => "The :attribute confirmation does not match.",
    "date"                  => "The :attribute is not a valid date.",
    "date_format"           => "The :attribute does not match the format :format.",
    "different"             => "The :attribute and :other must be different.",
    "digits"                => "The :attribute must be :digits digits.",
    "digits_between"        => "The :attribute must be between :min and :max digits.",
    'dimensions'            => 'The :attribute has invalid image dimensions (width) :min_width px (height) :min_height px.',
    "email"                 => "The :attribute must be a valid email address.",
    'mobile'                => 'The :attribute must be a valid mobile number.',
    "filled"                => "The :attribute field is required.",
    "exists"                => "The selected :attribute is invalid.",
    "image"                 => "The :attribute must be an image.",
    "in"                    => "The selected :attribute is invalid.",
    "integer"               => "The :attribute must be an integer.",
    "ip"                    => "The :attribute must be a valid IP address.",
    "max"                   => [
        "numeric" => "The :attribute may not be greater than :max.",
        "file"    => "The :attribute may not be greater than :max kilobytes.",
        "string"  => "The :attribute may not be greater than :max characters.",
        "array"   => "The :attribute may not have more than :max items.",
    ],
    "mimes"                 => "The :attribute must be a file of type: :values.",
    "min"                   => [
        "numeric" => "The :attribute must be at least :min.",
        "file"    => "The :attribute must be at least :min kilobytes.",
        "string"  => "The :attribute must be at least :min characters.",
        "array"   => "The :attribute must have at least :min items.",
    ],
    "not_in"                => "The selected :attribute is invalid.",
    "numeric"               => "The :attribute must be a number.",
    "regex"                 => "The :attribute format is invalid.",
    "required"              => "The :attribute field is required.",
    "required_if"           => "The :attribute field is required when :other is :value.",
    "required_with"         => "The :attribute field is required when :values is present.",
    "required_with_all"     => "The :attribute field is required when :values is present.",
    "required_without"      => "The :attribute field is required when :values is not present.",
    "required_without_all"  => "The :attribute field is required when none of :values are present.",
    "same"                  => "The :attribute and :other must match.",
    "size"                  => [
        "numeric" => "The :attribute must be :size.",
        "file"    => "The :attribute must be :size kilobytes.",
        "string"  => "The :attribute must be :size characters.",
        "array"   => "The :attribute must contain :size items.",
    ],
    "unique"                => "The :attribute has already been taken.",
    "url"                   => "The :attribute format is invalid.",
    "timezone"              => "The :attribute must be a valid zone.",
    "account_not_confirmed" => "Your account is not confirmed, please check your email",
    "user_suspended"        => "Your account has been suspended, please contact us if an error",
    "bounced_email"=>"Your email has been suspended, we cannot send the activation email",
    "letters"               => "The username must contain at least one letter or number",
    "min_mb"    => "The :attribute must be at least :min_mb MB.",
    "max_mb"    => "The :attribute may not be greater than :max_mb MB.",
    "vector_not_support_eps" => "Sorry, the file is not compatible with Adobe",
    'duration_video'=> 'Clips should be between :min and :max seconds.',
    "footage_should_not_contain_any_audio"=>'Footage should not contain any audio.',
    "frame_rates"=>"Frame rates must be within the following :frame_rates",
    "minimal_resolution_allowed"=>"Minimal resolution allowed it's  :dimensions",
    "video_codec_not_supported"=>"Video codec not supported Please check requirements.",
    "file_contributor_exist"=>"Sorry, the file has already been published, please refresh the page",
    "file_danger_copyright"=>"This file violates your copyright policy",
    'error_contact_administrator'=>"An error occurred, please contact your system administrator Arabsstock",
    'files_equal_6'=>"The number of files should be 6 files",
    'categories_equal_6'=>"The number of categories should be 6 categories",
    "Sorry_all_files_must_be_without_background"=>"Sorry, all files must be without background",
    "Sorry_previously_approved_images_cannot_be_remove_background"=>"Sorry, previously approved images cannot be remove background",
    //"avatar_dimensions"  => "The Avatar must have a minimum of 180px width and 180px height.",
    //"cover_dimensions"  => "The Cover must have a minimum of 800px width and 600px height.",
    "no_update_warehouse_check_data"=>"The modifications were not saved. Please review the modifications to ensure that the data is not duplicated",
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */


    'attributes' => [
        'agree_gdpr'            => 'box I agree with the processing of personal data',
        'full_name'             => 'Full Name', // Version 1.8
        'username'              => 'Username',
        'email'                 => 'Email',
        'mobile'                 => 'Mobile',
        'password'              => 'Password',
        'password_confirmation' => 'Password Confirmation',
        'username_email'        => 'Username or Email',
        'website'               => 'Website',
        'location'              => 'Location',
        'country_id'          => 'Country',
        'twitter'               => 'Twitter',
        'facebook'              => 'Facebook',
        'google'                => 'Google',
        'instagram'             => 'Instagram',
        'comment'               => 'Comment',
        'title'                 => 'Title',
        'tags'                  => 'Tags',
        'description'           => 'Description',
        'g-recaptcha-response'  => 'Electronic validation',
        'images'                  => 'field',
        'length'                  => 'length',
        'weight'                  => 'weight',
        'work_field'              =>'work',
        'birth_date'              =>'birth year',
        'sex'              =>'Gender',
        'country_id'              =>'country',
        'city_id'              =>'city',
        'title'              =>'title',
    ],
    'fullname'   => 'The Name Is Required',
    'username'   => 'User Name Is Required',
    'company_name'   => 'Company Name Required',
    'company_address'   => 'Company Address Required',
    'company_email'   => 'Company Email Required',
    'company_email_invalid'   => 'Company Email is invalid',
    'company_tax_id'   => 'Tax No. is Required',
    'company_phone'   => 'Company Phone Required',
    // 'email'      => 'Email Is Requerd',
    'country'    => 'country Is Required',
    'agree_gdpr' => 'The Field Agree is Required',
    'recaptcha_msg' => 'Please try again'
];
