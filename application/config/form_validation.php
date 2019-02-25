<?php

$config["register"] = array(
    array(
        'field' => 'email',
        'label' => 'Email',
        'rules' => 'required|trim|xss_clean|valid_email'
    ),
    array(
        'field' => 'first_name',
        'label' => 'First name',
        'rules' => 'required|trim|xss_clean|max_length[255]'
    ),
    array(
        'field' => 'last_name',
        'label' => 'Last name',
        'rules' => 'required|trim|xss_clean|max_length[255]'
    )
);
$config["signup"] = array(
    array(
        'field' => 'gender',
        'label' => 'Gender',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'ethnicity',
        'label' => 'Ethnicity',
        'rules' => 'required|trim|xss_clean'
    ),
    
    array(
        'field' => 'career_stage',
        'label' => 'Career stage',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'current_location',
        'label' => 'Current live in',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'residence_type',
        'label' => 'Residence type',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'year',
        'label' => 'Date of birth',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'month',
        'label' => 'Date of birth',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'date',
        'label' => 'Date of birth',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'country_born',
        'label' => 'Country',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'city_born',
        'label' => 'City',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'nationality_id',
        'label' => 'Nationality',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'user_height',
        'label' => 'Height',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'body_type',
        'label' => 'Body type',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'looks',
        'label' => 'Looks',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'eye_color',
        'label' => 'Eye color',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'hair_color',
        'label' => 'Hair color',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'hair_length',
        'label' => 'Hair length',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'skin_tone',
        'label' => 'Skin tone',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'usually_wear',
        'label' => 'Eye wear',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'relationship_status',
        'label' => 'Relationship status',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'religious_belief',
        'label' => 'Religious belief',
        'rules' => 'required|trim|xss_clean'
    ),
    array(
        'field' => 'spoken_language_id',
        'label' => 'Spoken language',
        'rules' => 'required|trim|xss_clean'
    )
);
$config["change_password"] = array(
    array(
        'field' => 'password',
        'label' => 'Password',
        'rules' => 'required|trim|xss_clean|min_length[6]'
    ),
    array(
        'field' => 'passconf',
        'label' => 'Re type Password',
        'rules' => 'required|trim|xss_clean|matches[password]'
    ),
);
?>