<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 | -------------------------------------------------------------------------
 | URI ROUTING
 | -------------------------------------------------------------------------
 | This file lets you re-map URI requests to specific controller functions.
 |
 | Typically there is a one-to-one relationship between a URL string
 | and its corresponding controller class/method. The segments in a
 | URL normally follow this pattern:
 |
 |	example.com/class/method/id/
 |
 | In some instances, however, you may want to remap this relationship
 | so that a different class/function is called than the one
 | corresponding to the URL.
 |
 | Please see the user guide for complete details:
 |
 |	http://codeigniter.com/user_guide/general/routing.html
 |
 | -------------------------------------------------------------------------
 | RESERVED ROUTES
 | -------------------------------------------------------------------------
 |
 | There area two reserved routes:
 |
 |	$route['default_controller'] = 'welcome';
 |
 | This route indicates which controller class should be loaded if the
 | URI contains no data. In the above example, the "welcome" class
 | would be loaded.
 |
 |	$route['404_override'] = 'errors/page_missing';
 |
 | This route will tell the Router what URI segments to use if those provided
 | in the URL cannot be matched to a valid route.
 |
 */
 
$route['default_controller']                            = "home/load_landing_page";
$route['404_override']                                  = '';
$route['js/sigup-validations.js']                       = 'home/validation_js';

$route['email-verification/(:any)']                     = 'home/email_verification';
$route['([a-z-]+)/change-city.html']                    = 'home/change_city';
$route['([a-z-]+)/privacy.html']                        = 'home/privacy';
$route['([a-z-]+)/terms.html']                          = 'home/terms';
$route['([a-z-]+)/about-us.html']                       = 'home/about';
$route['([a-z-]+)/career.html']                       = 'home/career';
$route['([a-z-]+)/help.html']                        = 'home/help';
$route['([a-z-]+)/press.html']                       = 'home/press';

$route['([a-z-]+)/event.html']                      = 'events/details';
$route['([a-z-]+)/event_photos.html']               = 'events/event_photos';
$route['([a-z-]+)/events.html']                     = 'events/event_list';
$route['([a-z-]+)/event-order-confirmation.html']   = 'events/confirm_rsvp';

$route['([a-z-]+)/invite-friends.html']                 = 'user/invite_friends';
$route['([a-z-]+)/signin.html']				            = 'user/sign_in_using_email';

$route['([a-z-]+)/edit-profile.html']                   = 'user/edit_profile';
$route['([a-z-]+)/ideal-match.html']                    = 'user/ideal_match';

$route['([a-z-]+)/forgot-password.html']                = 'user/forgot_password';
$route['([a-z-]+)/password-reset.html/(:any)']          = 'user/password_reset';

$route['([a-z-]+)/process-password-reset.html']         = 'user/processResetPassword';

$route['([a-z-]+)/index.html']                          = 'home/index';
//$route['([a-z-]+)/apply-without-facebook.html']         = 'home/register';
$route['([a-z-]+)/apply.html']         = 'home/register';
$route['([a-z-]+)/eligible-schools.html']               = 'home/eligible_schools';
$route['([a-z-]+)/benifits-of-facebook.html']           = 'home/benifits_of_using_fb';
$route['([a-z-]+)/how-it-works.html']                   = 'home/how_works';
$route['([a-z-]+)/view-map.html']              			= 'home/view_map';
// for sign up process steps
$route['([a-z-]+)/signup-step-(\d).html']               = "signup/step$2";
//$route['([a-z-]+)/new-signup-step-(\d).html']               = "signup/step$2";
$route['([a-z-]+)/signup-confirmation.html']            = "user/confirmation";
$route['([a-z-]+)/switch-language.html']                = 'translation/switch_language';

//Inserted by rajnish
$route['([a-z-]+)/user_info/(:any)']                    = "user/user_info/$2";
$route['([a-z-]+)/mutual_friends/(:any)']               = "user/mutual_friends/$2";

//Intro Page
$route['([a-z-]+)/my-intros.html']                   	= 'my_intros';
$route['([a-z-]+)/intro-photos.html']                   = 'my_intros/intro_photos';
$route['([a-z-]+)/lack-interest.html']                  = 'my_intros/lack_interest';
$route['([a-z-]+)/mutual-friends.html']                 = 'my_intros/mutual_friends';

//My-Date Page

$route['date'] = 'dates/new_date_step1';
$route['([a-z-]+)/my-date.html']                   		= 'my_dates';
$route['([a-z-]+)/suggest-date-idea.html']              = 'my_dates/suggest_date';
$route['([a-z-]+)/suggestion-sent.html']              	= 'my_dates/suggestion_sent';
$route['([a-z-]+)/accept-date.html']              		= 'my_dates/accept_date';
$route['([a-z-]+)/view-feedback.html']              	= 'my_dates/view_feedback';
$route['([a-z-]+)/submit-feedback.html']              	= 'my_dates/submit_feedback';

//Account
$route['([a-z-]+)/upgrade-account.html']              	= 'account/upgrade';
$route['([a-z-]+)/get-more-tickets.html']              	= 'account/get_more_tickets';
$route['([a-z-]+)/setting.html']              	= 'account/settings';

$route['([a-z-]+)/change-password.html']                = 'account/change_password';

//$route['franchise/(:any)']                    = "franchise/view_ad_details/$1";
$route['apply']  = 'home/register';
$route['signup']  = 'home/register';
$route['join']  = 'home/shortcut_url';
$route['signin']  = 'user/sign_in_using_email';
$route['intros']  = 'my_intros';
$route['upgrade']  = 'account/upgrade';
$route['tix']  = 'account/get_more_tickets';
$route['(:num)']       = 'home/shortcut_url';
$route['event(:num)']  = 'home/shortcut_url';
$route['meetup']  = 'home/shortcut_url';
$route['group']  = 'home/shortcut_url';
$route['aigangpiao']  = 'home/shortcut_url';
$route['gangpiaoquan']  = 'home/shortcut_url';
$route['appledating']  = 'home/shortcut_url';
$route['anniedate']  = 'home/shortcut_url';
$route['kelly']  = 'home/shortcut_url';
$route['network']  = 'home/shortcut_url';
$route['social']  = 'home/shortcut_url';
$route['networking']  = 'home/shortcut_url';
$route['mareene']  = 'home/shortcut_url';
$route['tinavip']  = 'home/shortcut_url';
$route['sue']  = 'home/shortcut_url';
$route['derek']  = 'home/shortcut_url';
$route['ellen']  = 'home/shortcut_url';
$route['tgif']  = 'home/shortcut_url';
$route['mba']  = 'home/shortcut_url';
$route['mnc']  = 'home/shortcut_url';
$route['halloween']  = 'home/shortcut_url';
$route['spring']  = 'home/shortcut_url';
$route['summer']  = 'home/shortcut_url';
$route['xia']  = 'home/shortcut_url';
$route['ny2015']  = 'home/shortcut_url';
$route['bj2015']  = 'home/shortcut_url';
$route['bachelor']  = 'home/shortcut_url';
$route['spring']  = 'home/shortcut_url';
$route['your-mr-miss-right']  = 'home/shortcut_url';
$route['party']  = 'home/shortcut_url';
$route['samyin']  = 'home/shortcut_url';
$route['carrie']  = 'home/shortcut_url';
$route['pw']  = 'home/shortcut_url';
$route['jointu']  = 'home/shortcut_url';
$route['vip']  = 'home/vip';
$route['promo']  = 'home/vip';
$route['upgrade_account']  = 'upgrade_account';
$route['love']  = 'account/get_more_tickets';

$route['change-city.html']                    = 'home/change_city';
$route['privacy.html']                        = 'home/privacy';
$route['terms.html']                          = 'home/terms';
$route['about-us.html']                       = 'home/about';
$route['career.html']                       = 'home/career';
$route['help.html']                        = 'home/help';
$route['press.html']                       = 'home/press';
$route['event.html']                      = 'events/details';
$route['event_photos.html']               = 'events/event_photos';
$route['events.html']                     = 'events/event_list';
$route['event-order-confirmation.html']   = 'events/confirm_rsvp';
$route['eligible-schools.html']               = 'home/eligible_schools';
$route['([a-z-]+).html']                       = 'home/$1';
$route['pms/(:num)']  = 'pms/index/$1';

//add by xiaohui
$route['tn.html'] = 'test_xh/test_notification';

//============================== ROUTES for APIs (v1) =======================================================================

// SignUp, SignIn
$route['api/v(:num)/request_verification_code'] = 'api/v$1/membership_API/request_verification_code';
$route['api/v(:num)/validate_verification_code'] = 'api/v$1/membership_API/validate_verification_code';
$route['api/v(:num)/sign_up'] = 'api/v$1/membership_API/sign_up';
$route['api/v(:num)/sign_in'] = 'api/v$1/membership_API/sign_in';
$route['api/v(:num)/reset_password'] = 'api/v$1/membership_API/reset_password';
$route['api/v(:num)/sign_out'] = 'api/v$1/users_API/sign_out';
$route['api/v(:num)/facebook_sign_in'] = 'api/v$1/membership_API/facebook_sign_in';
$route['api/v(:num)/check_facebook_id'] = 'api/v$1/membership_API/check_facebook_id';
//add by yang
$route['api/v(:num)/report_user'] = 'api/v$1/membership_API/report_user';

// Find Dates
$route['api/v(:num)/dates/find'] = 'api/v$1/dates_API/find';
$route['api/v(:num)/dates/(:num)/apply'] = 'api/v$1/dates_API/dates_apply/$2';
$route['api/v(:num)/dates/(:num)/dislike'] = 'api/v$1/dates_API/dates_dislike/$2';
$route['api/v(:num)/dates/revert_last_dislike'] = 'api/v$1/dates_API/dates_revert_last_dislike';

// My Dates
$route['api/v(:num)/my_dates'] = 'api/v$1/dates_API/my_dates';
$route['api/v(:num)/my_dates/(:num)'] = 'api/v$1/dates_API/my_dates/$2';

$route['api/v(:num)/my_dates/upcoming_dates'] = 'api/v$1/dates_API/my_dates_upcoming_dates';
$route['api/v(:num)/my_dates/upcoming_dates/my_hosts'] = 'api/v$1/dates_API/my_dates_upcoming_dates_my_hosts';

$route['api/v(:num)/my_dates/past_dates'] = 'api/v$1/dates_API/my_dates_past_dates';
$route['api/v(:num)/my_dates/past_dates/my_hosts'] = 'api/v$1/dates_API/my_dates_past_dates_my_hosts';

$route['api/v(:num)/my_dates/(:num)/feedbacks'] = 'api/v$1/dates_API/my_dates_feedbacks/$2';

$route['api/v(:num)/my_dates/(:num)/refund'] = 'api/v$1/dates_API/my_dates_refund/$2';

$route['api/v(:num)/my_dates/(:num)/applicants'] = 'api/v$1/dates_API/my_dates_applicants/$2';
$route['api/v(:num)/my_dates/(:num)/applicants/(:num)/select'] = 'api/v$1/dates_API/my_dates_applicants_select/$2/$3';
$route['api/v(:num)/my_dates/(:num)/cancel_my_applicant'] = 'api/v$1/dates_API/my_dates_cancel_my_applicant/$2';
$route['api/v(:num)/my_dates/(:num)/cancel_my_date'] = 'api/v$1/dates_API/my_dates_cancel_my_date/$2';
$route['api/v(:num)/my_dates/(:num)/invite_matches'] = 'api/v$1/dates_API/my_dates_invite_matches/$2';

// New Date
$route['api/v(:num)/new_date/available_dates_for_new_date'] = 'api/v$1/dates_API/available_dates_for_new_date';
$route['api/v(:num)/new_date/validate_date_time_for_new_date'] = 'api/v$1/dates_API/validate_date_time_for_new_date';
$route['api/v(:num)/new_date/step2'] = 'api/v$1/dates_API/new_date_step2';
$route['api/v(:num)/new_date/step3'] = 'api/v$1/dates_API/new_date_step3';
$route['api/v(:num)/new_date/step4'] = 'api/v$1/dates_API/new_date_step4';
$route['api/v(:num)/new_date/step5'] = 'api/v$1/dates_API/new_date_step5';
$route['api/v(:num)/new_date/step6'] = 'api/v$1/dates_API/new_date_step6';

// People
$route['api/v(:num)/people/find'] = 'api/v$1/users_API/find';
$route['api/v(:num)/people/(:num)/invite'] = 'api/v$1/users_API/invite/$2';
$route['api/v(:num)/people/(:num)/dislike'] = 'api/v$1/users_API/dislike/$2';
$route['api/v(:num)/people/revert_last_dislike'] = 'api/v$1/users_API/revert_last_dislike';
$route['api/v(:num)/people/(:num)/profile'] = 'api/v$1/users_API/user_profile/$2';
$route['api/v(:num)/people/(:num)/common_interests'] = 'api/v$1/users_API/user_common_interests/$2';
$route['api/v(:num)/people/(:num)/mutual_friends'] = 'api/v$1/users_API/user_mutual_friends/$2';

// Chats
$route['api/v(:num)/chats'] = 'api/v$1/chats_API/chats';
$route['api/v(:num)/chats/(:num)/messages'] = 'api/v$1/chats_API/chats_messages/$2';

// Date Tickets
$route['api/v(:num)/date_tickets/purchase'] = 'api/v$1/date_tickets_API/purchase';
$route['api/v(:num)/date_tickets/add'] = 'api/v$1/date_tickets_API/add';
$route['api/v(:num)/date_tickets/use'] = 'api/v$1/date_tickets_API/use';

// My Profile
$route['api/v(:num)/my_profile/basics'] = 'api/v$1/my_profile_API/basics';

$route['api/v(:num)/my_profile/photos'] = 'api/v$1/my_profile_API/photos';
$route['api/v(:num)/my_profile/photos/json'] = 'api/v$1/my_profile_API/photos_json';
$route['api/v(:num)/my_profile/photos/(:num)'] = 'api/v$1/my_profile_API/photos/$2';
$route['api/v(:num)/my_profile/photos/(:num)/set_primary'] = 'api/v$1/my_profile_API/set_primary_photo/$2';

$route['api/v(:num)/my_profile/education'] = 'api/v$1/my_profile_API/education';
$route['api/v(:num)/my_profile/education/schools'] = 'api/v$1/my_profile_API/education_schools';
$route['api/v(:num)/my_profile/education/schools/json'] = 'api/v$1/my_profile_API/education_schools_json';
$route['api/v(:num)/my_profile/education/schools/(:num)'] = 'api/v$1/my_profile_API/education_schools/$2';

$route['api/v(:num)/my_profile/career'] = 'api/v$1/my_profile_API/career';
$route['api/v(:num)/my_profile/career/jobs'] = 'api/v$1/my_profile_API/career_jobs';
$route['api/v(:num)/my_profile/career/jobs/json'] = 'api/v$1/my_profile_API/career_jobs_json';
$route['api/v(:num)/my_profile/career/jobs/(:num)'] = 'api/v$1/my_profile_API/career_jobs/$2';

$route['api/v(:num)/my_profile/other'] = 'api/v$1/my_profile_API/other';
$route['api/v(:num)/my_profile/other/spoken_languages'] = 'api/v$1/my_profile_API/other_spoken_languages';
$route['api/v(:num)/my_profile/other/spoken_languages/(:num)'] = 'api/v$1/my_profile_API/other_spoken_languages/$2';

// Settings
$route['api/v(:num)/settings/dates'] = 'api/v$1/settings_API/dates';

$route['api/v(:num)/settings/dates/user_want_schools'] = 'api/v$1/settings_API/dates_user_want_schools';
$route['api/v(:num)/settings/dates/user_want_schools/(:num)'] = 'api/v$1/settings_API/dates_user_want_schools/$2';

$route['api/v(:num)/settings/dates/user_want_school_subjects'] = 'api/v$1/settings_API/dates_user_want_school_subjects';
$route['api/v(:num)/settings/dates/user_want_school_subjects/(:num)'] = 'api/v$1/settings_API/dates_user_want_school_subjects/$2';

$route['api/v(:num)/settings/dates/user_want_companies'] = 'api/v$1/settings_API/dates_user_want_companies';
$route['api/v(:num)/settings/dates/user_want_companies/(:num)'] = 'api/v$1/settings_API/dates_user_want_companies/$2';

$route['api/v(:num)/settings/account'] = 'api/v$1/settings_API/account';

// Miscellaneous
$route['api/v(:num)/validate_promotion_code'] = 'api/v$1/miscellaneous_API/validate_promotion_code';
$route['api/v(:num)/upgrade_account'] = 'api/v$1/miscellaneous_API/upgrade_account';
$route['api/v(:num)/invite_friends'] = 'api/v$1/miscellaneous_API/invite_friends';
$route['api/v(:num)/get_my_info'] = 'api/v$1/miscellaneous_API/get_my_info';
$route['api/v(:num)/update_my_info'] = 'api/v$1/miscellaneous_API/update_my_info';
$route['api/v(:num)/send_push_notification'] = 'api/v$1/miscellaneous_API/send_push_notification';
$route['api/v(:num)/super_date_request'] = 'api/v$1/miscellaneous_API/super_date_request';

// Resources
$route['api/v(:num)/date_types'] = 'api/v$1/date_types_API';
$route['api/v(:num)/relationship_types'] = 'api/v$1/relationship_types_API';
$route['api/v(:num)/date_payers'] = 'api/v$1/date_payers_API';
$route['api/v(:num)/genders'] = 'api/v$1/genders_API';
$route['api/v(:num)/ethnicities'] = 'api/v$1/ethnicities_API';
$route['api/v(:num)/budgets'] = 'api/v$1/budgets_API';
$route['api/v(:num)/cuisines'] = 'api/v$1/cuisines_API';
$route['api/v(:num)/cities'] = 'api/v$1/cities_API';
$route['api/v(:num)/body_types'] = 'api/v$1/body_types_API';
$route['api/v(:num)/relationship_statuses'] = 'api/v$1/relationship_statuses_API';
$route['api/v(:num)/religious_beliefs'] = 'api/v$1/religious_beliefs_API';
$route['api/v(:num)/education_levels'] = 'api/v$1/education_levels_API';
$route['api/v(:num)/annual_income_ranges'] = 'api/v$1/annual_income_ranges_API';
$route['api/v(:num)/descriptive_words'] = 'api/v$1/descriptive_words_API';
$route['api/v(:num)/interests'] = 'api/v$1/interests_API';
$route['api/v(:num)/interest_categories'] = 'api/v$1/interest_categories_API';
$route['api/v(:num)/smoking_statuses'] = 'api/v$1/smoking_statuses_API';
$route['api/v(:num)/drinking_statuses'] = 'api/v$1/drinking_statuses_API';
$route['api/v(:num)/exercise_frequencies'] = 'api/v$1/exercise_frequencies_API';
$route['api/v(:num)/residence_types'] = 'api/v$1/residence_types_API';
$route['api/v(:num)/child_statuses'] = 'api/v$1/child_statuses_API';
$route['api/v(:num)/child_plans'] = 'api/v$1/child_plans_API';
$route['api/v(:num)/spoken_language_levels'] = 'api/v$1/spoken_language_levels_API';
$route['api/v(:num)/spoken_languages'] = 'api/v$1/spoken_languages_API';
$route['api/v(:num)/schools'] = 'api/v$1/schools_API';
$route['api/v(:num)/school_subjects'] = 'api/v$1/school_subjects_API';
$route['api/v(:num)/companies'] = 'api/v$1/companies_API';
$route['api/v(:num)/merchants'] = 'api/v$1/merchants_API';
$route['api/v(:num)/merchants/(:num)'] = 'api/v$1/merchants_API/merchant/$2';
$route['api/v(:num)/users/(:num)'] = 'api/v$1/users_API/user/$2';

// Delayed Jobs
$route['api/v(:num)/execute_delayed_jobs'] = 'api/v$1/delayed_jobs_API/execute';
$route['api/v(:num)/execute_test_jobs'] = 'api/v$1/delayed_jobs_API/test';

// Periodic Jobs
$route['api/v(:num)/execute_periodic_jobs'] = 'api/v$1/periodic_jobs_API/execute';

// Test Purposes
$route['api/v(:num)/test'] = 'api/v$1/test_API';



//============================== ROUTES for APIs (av2) =======================================================================

// SignUp, SignIn
$route['api/av(:num)/request_verification_code'] = 'api/av$1/membership_API/request_verification_code';
$route['api/av(:num)/validate_verification_code'] = 'api/av$1/membership_API/validate_verification_code';
$route['api/av(:num)/request_email_verification_code'] = 'api/av$1/membership_API/request_email_verification_code';
$route['api/av(:num)/sign_up'] = 'api/av$1/membership_API/sign_up';
$route['api/av(:num)/sign_in'] = 'api/av$1/membership_API/sign_in';
$route['api/av(:num)/reset_password'] = 'api/av$1/membership_API/reset_password';
$route['api/av(:num)/sign_out'] = 'api/av$1/users_API/sign_out';
$route['api/av(:num)/facebook_sign_in'] = 'api/av$1/membership_API/facebook_sign_in';
$route['api/av(:num)/check_facebook_id'] = 'api/av$1/membership_API/check_facebook_id';

// Find Dates
$route['api/av(:num)/dates/find'] = 'api/av$1/dates_API/find';
$route['api/av(:num)/dates/filter_params'] = 'api/av$1/dates_API/find_dates_filter_params';
$route['api/av(:num)/dates/(:num)/apply'] = 'api/av$1/dates_API/dates_apply/$2';
$route['api/av(:num)/dates/(:num)/dislike'] = 'api/av$1/dates_API/dates_dislike/$2';
$route['api/av(:num)/dates/revert_last_dislike'] = 'api/av$1/dates_API/dates_revert_last_dislike';
$route['api/av(:num)/dates/(:num)/follow'] = 'api/av$1/dates_API/follow/$2';
$route['api/av(:num)/dates/(:num)/unfollow'] = 'api/av$1/dates_API/unfollow/$2';

// My Dates
$route['api/av(:num)/my_dates'] = 'api/av$1/dates_API/my_dates';
$route['api/av(:num)/my_dates/(:num)'] = 'api/av$1/dates_API/my_dates/$2';

$route['api/av(:num)/my_dates/upcoming_dates/my_hosts'] = 'api/av$1/dates_API/my_dates_upcoming_dates_my_hosts';
$route['api/av(:num)/my_dates/upcoming_dates/my_applies'] = 'api/av$1/dates_API/my_dates_upcoming_dates_my_applies';

$route['api/av(:num)/my_dates/past_dates/my_hosts'] = 'api/av$1/dates_API/my_dates_past_dates_my_hosts';
$route['api/av(:num)/my_dates/past_dates/my_applies'] = 'api/av$1/dates_API/my_dates_past_dates_my_applies';

$route['api/av(:num)/my_dates/(:num)/feedbacks'] = 'api/av$1/dates_API/my_dates_feedbacks/$2';

$route['api/av(:num)/my_dates/(:num)/refund'] = 'api/av$1/dates_API/my_dates_refund/$2';

$route['api/av(:num)/my_dates/(:num)/applicants'] = 'api/av$1/dates_API/my_dates_applicants/$2';
$route['api/av(:num)/my_dates/(:num)/applicants/(:num)/select'] = 'api/av$1/dates_API/my_dates_applicants_select/$2/$3';
$route['api/av(:num)/my_dates/(:num)/cancel_my_applicant'] = 'api/av$1/dates_API/my_dates_cancel_my_applicant/$2';
$route['api/av(:num)/my_dates/(:num)/cancel_my_date'] = 'api/av$1/dates_API/my_dates_cancel_my_date/$2';
$route['api/av(:num)/my_dates/(:num)/invite_matches'] = 'api/av$1/dates_API/my_dates_invite_matches/$2';

// New Date
$route['api/av(:num)/new_date/validate_date_time_for_new_date'] = 'api/av$1/dates_API/validate_date_time_for_new_date';
$route['api/av(:num)/new_date/step2'] = 'api/av$1/dates_API/new_date_step2';
$route['api/av(:num)/new_date/step3'] = 'api/av$1/dates_API/new_date_step3';

// People
$route['api/av(:num)/people/find'] = 'api/av$1/users_API/find';
$route['api/av(:num)/people/filter_params'] = 'api/av$1/users_API/find_people_filter_params';
$route['api/av(:num)/people/(:num)/invite'] = 'api/av$1/users_API/invite/$2';
$route['api/av(:num)/people/(:num)/dislike'] = 'api/av$1/users_API/dislike/$2';
$route['api/av(:num)/people/revert_last_dislike'] = 'api/av$1/users_API/revert_last_dislike';
$route['api/av(:num)/people/(:num)/super_date'] = 'api/av$1/users_API/super_date/$2';
$route['api/av(:num)/people/(:num)/profile'] = 'api/av$1/users_API/user_profile/$2';
$route['api/av(:num)/people/(:num)/common_interests'] = 'api/av$1/users_API/user_common_interests/$2';
$route['api/av(:num)/people/(:num)/mutual_friends'] = 'api/av$1/users_API/user_mutual_friends/$2';
$route['api/av(:num)/people/(:num)/follow'] = 'api/av$1/users_API/follow/$2';
$route['api/av(:num)/people/(:num)/unfollow'] = 'api/av$1/users_API/unfollow/$2';

// Chats
$route['api/av(:num)/chats'] = 'api/av$1/chats_API/chats';
$route['api/av(:num)/chats/(:num)/messages'] = 'api/av$1/chats_API/chats_messages/$2';

// Date Tickets
$route['api/av(:num)/date_tickets/purchase'] = 'api/av$1/date_tickets_API/purchase';
$route['api/av(:num)/date_tickets/add'] = 'api/av$1/date_tickets_API/add';
$route['api/av(:num)/date_tickets/use'] = 'api/av$1/date_tickets_API/use';

// My Profile
$route['api/av(:num)/my_profile'] = 'api/av$1/my_profile_API';
$route['api/av(:num)/my_profile/basics'] = 'api/av$1/my_profile_API/basics';
$route['api/av(:num)/my_profile/preferences'] = 'api/av$1/my_profile_API/preferences';
$route['api/av(:num)/my_profile/photos'] = 'api/av$1/my_profile_API/photos';
$route['api/av(:num)/my_profile/photos/json'] = 'api/av$1/my_profile_API/photos_json';

// Settings
$route['api/av(:num)/settings'] = 'api/av$1/settings_API';

// Merchants
$route['api/av(:num)/merchants'] = 'api/av$1/merchants_API';
$route['api/av(:num)/merchants/(:num)'] = 'api/av$1/merchants_API/merchant/$2';
$route['api/av(:num)/merchants/(:num)/follow'] = 'api/av$1/merchants_API/follow/$2';
$route['api/av(:num)/merchants/(:num)/unfollow'] = 'api/av$1/merchants_API/unfollow/$2';

// My Follows/Visitors
$route['api/av(:num)/my_follows/dates'] = 'api/av$1/users_API/my_follows_dates';
$route['api/av(:num)/my_follows/people'] = 'api/av$1/users_API/my_follows_people';
$route['api/av(:num)/my_follows/merchants'] = 'api/av$1/users_API/my_follows_merchants';
$route['api/av(:num)/my_visitors'] = 'api/av$1/users_API/my_visitors';

// Miscellaneous
$route['api/av(:num)/validate_promotion_code'] = 'api/av$1/miscellaneous_API/validate_promotion_code';
$route['api/av(:num)/upgrade_account'] = 'api/av$1/miscellaneous_API/upgrade_account';
$route['api/av(:num)/invite_friends'] = 'api/av$1/miscellaneous_API/invite_friends';
$route['api/av(:num)/get_my_info'] = 'api/av$1/miscellaneous_API/get_my_info';
$route['api/av(:num)/update_my_info'] = 'api/av$1/miscellaneous_API/update_my_info';
$route['api/av(:num)/update_my_device_token'] = 'api/av$1/miscellaneous_API/update_my_device_token';
$route['api/av(:num)/update_my_location'] = 'api/av$1/miscellaneous_API/update_my_location';
$route['api/av(:num)/send_push_notification'] = 'api/av$1/miscellaneous_API/send_push_notification';

// Resources
$route['api/av(:num)/date_types'] = 'api/av$1/date_types_API';
$route['api/av(:num)/relationship_types'] = 'api/av$1/relationship_types_API';
$route['api/av(:num)/date_payers'] = 'api/av$1/date_payers_API';
$route['api/av(:num)/genders'] = 'api/av$1/genders_API';
$route['api/av(:num)/ethnicities'] = 'api/av$1/ethnicities_API';
$route['api/av(:num)/budgets'] = 'api/av$1/budgets_API';
$route['api/av(:num)/cuisines'] = 'api/av$1/cuisines_API';
$route['api/av(:num)/cities'] = 'api/av$1/cities_API';
$route['api/av(:num)/body_types'] = 'api/av$1/body_types_API';
$route['api/av(:num)/relationship_statuses'] = 'api/av$1/relationship_statuses_API';
$route['api/av(:num)/religious_beliefs'] = 'api/av$1/religious_beliefs_API';
$route['api/av(:num)/education_levels'] = 'api/av$1/education_levels_API';
$route['api/av(:num)/annual_income_ranges'] = 'api/av$1/annual_income_ranges_API';
$route['api/av(:num)/descriptive_words'] = 'api/av$1/descriptive_words_API';
$route['api/av(:num)/interests'] = 'api/av$1/interests_API';
$route['api/av(:num)/interest_categories'] = 'api/av$1/interest_categories_API';
$route['api/av(:num)/smoking_statuses'] = 'api/av$1/smoking_statuses_API';
$route['api/av(:num)/drinking_statuses'] = 'api/av$1/drinking_statuses_API';
$route['api/av(:num)/exercise_frequencies'] = 'api/av$1/exercise_frequencies_API';
$route['api/av(:num)/residence_types'] = 'api/av$1/residence_types_API';
$route['api/av(:num)/child_statuses'] = 'api/av$1/child_statuses_API';
$route['api/av(:num)/child_plans'] = 'api/av$1/child_plans_API';
$route['api/av(:num)/spoken_language_levels'] = 'api/av$1/spoken_language_levels_API';
$route['api/av(:num)/spoken_languages'] = 'api/av$1/spoken_languages_API';
$route['api/av(:num)/schools'] = 'api/av$1/schools_API';
$route['api/av(:num)/school_subjects'] = 'api/av$1/school_subjects_API';
$route['api/av(:num)/companies'] = 'api/av$1/companies_API';
$route['api/av(:num)/users/(:num)'] = 'api/av$1/users_API/user/$2';
$route['api/av(:num)/display_languages'] = 'api/av$1/display_languages_API';
$route['api/av(:num)/neighborhoods'] = 'api/av$1/neighborhoods_API';

// Delayed Jobs
$route['api/av(:num)/execute_delayed_jobs'] = 'api/av$1/delayed_jobs_API/execute';
$route['api/av(:num)/execute_test_jobs'] = 'api/av$1/delayed_jobs_API/test';

// Periodic Jobs
$route['api/av(:num)/execute_periodic_jobs'] = 'api/av$1/periodic_jobs_API/execute';

// Test Purposes
$route['api/av(:num)/test'] = 'api/av$1/test_API';
$route['api/av(:num)/test_delayed_jobs/send_new_date_push_notifications_to_my_followers'] = 'api/av$1/delayed_jobs_API/send_new_date_push_notifications_to_my_followers';
$route['api/av(:num)/test_delayed_jobs/send_new_date_push_notifications_to_merchant_followers'] = 'api/av$1/delayed_jobs_API/send_new_date_push_notifications_to_merchant_followers';
$route['api/av(:num)/test_delayed_jobs/send_cancelled_date_push_notifications_to_date_followers'] = 'api/av$1/delayed_jobs_API/send_cancelled_date_push_notifications_to_date_followers';
$route['api/av(:num)/test_delayed_jobs/send_cancelled_date_push_notifications_to_date_applicants'] = 'api/av$1/delayed_jobs_API/send_cancelled_date_push_notifications_to_date_applicants';
$route['api/av(:num)/test_periodic_jobs/test'] = 'api/av$1/periodic_jobs_API/test';


/* End of file routes.php */
/* Location: ./application/config/routes.php */
