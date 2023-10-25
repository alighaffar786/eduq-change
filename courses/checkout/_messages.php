<?php
$_MESSEGES = [
    "ERRORS" => [
        "student_info" => "<strong>Studentinformatie </strong> ontbreekt.",
        "course_date_info" => "<strong>Cursusdatum </strong> ontbreekt.",
        "course_info" => "<strong>Course Info </strong> ontbreekt.",
        "firstname_cursist" => "<strong>Cursist {{index}} Voornaam </strong> is een vereist veld.",
        "firstname_cursist_valid" => "<strong>Cursist {{index}} Voornaam </strong> is niet geldig.",
        "lastname_cursist" => "<strong>Cursist {{index}} Achternaam  </strong> is een vereist veld.",
        "lastname_cursist_valid" => "<strong>Cursist {{index}} Achternaam  </strong> is niet geldig.",
        "email_cursist" => "<strong>Cursist {{index}} E-mailadres  </strong> is een vereist veld.",
        "email_cursist_valid" => "<strong>E-mailadres van Cursist {{index}} </strong> is niet geldig.",
        "phone_cursist" => "<strong>Cursist {{index}} Telefoonnummer  </strong> is een vereist veld.",
        "phone_cursist_valid" => "<strong>Cursist {{index}} Telefoonnummer  </strong> is niet geldig.",
        "phone_cursist_valid1" => "<strong>Cursist {{index}} Telefoonnummer  </strong> Telefoonnummer mag niet met nul beginnen.",
        "placeofbrith_cursist" => "<strong>Cursist {{index}} Geboorteplaats  </strong> is een vereist veld.",
        "placeofbrith_cursist_valid" => "<strong>Cursist {{index}} Geboorteplaats  </strong> is niet geldig.",
        "birtdate_cursist" => "<strong>Cursist {{index}} Geboortedatum  </strong> is een vereist veld.",
        "birtdate_cursist_valid" => "<strong>Cursist {{index}} Geboortedatum  </strong> is niet geldig.",
        "calendar_course_unavailable" => "",

        "user_first_name" => "<strong>Contactpersoon Voornaam</strong> is een vereist veld.",
        "user_first_name_valid" => "<strong>Contactpersoon Voornaam</strong> is niet geldig.",

        "user_last_name" => "<strong>Contactpersoon Achternaam</strong> is een vereist veld.",
        "user_last_name_valid" => "<strong>Contactpersoon Achternaam</strong> is niet geldig.",

        "user_email" => "<strong>Contactpersoon E-mailadres </strong> is een vereist veld.",
        "user_email_valid" => "<strong>Contactpersoon E-mailadres </strong> is niet geldig.",

        "user_phone" => "<strong>Contactpersoon Telefoon  </strong> is een vereist veld.",
        "user_phone_valid" => "<strong>Contactpersoon Telefoon  </strong> is niet geldig.",

        "billing_phone" => "<strong>Facturering Telefoon  </strong> is een vereist veld.",
        "billing_phone_valid" => "<strong>Facturering Telefoon  </strong> is niet geldig.",

        "billing_custom_email" => "<strong>Facturering E-mailadres  </strong> is een vereist veld.",
        "billing_custom_email_valid" => "<strong>Facturering E-mailadres  </strong> is niet geldig.",

        "company_name" => "<strong> Bedrijfsnaam  </strong> is een vereist veld.",
        "company_name_valid" => "<strong>Bedrijfsnaam  </strong> is niet geldig.",

        "multisafepay_ideal_valid" => "<strong>Selecteer bank</strong>.",



    ],
    "messgae_replacements" => [
        "Ongeldig facturatie-e-mailadres" => "<strong>Facturering E-mailadres</strong> is niet geldig."
    ],
    "labels" => [
        "listening_exam" => "Luisterexamen + €50",
        "code_95" => "CCV registratie voor CODE 95 + €19",
        "code_95_tooltip" => "De CCV registratie van nascholingsuren is voor beroepschauffeurs.",
        "sob" => "SOOB-SUBSIDIE? Klik hier",
        "sob_tool_tip" => "Werk jij in de sector transport en logistiek? Wie weet voldoe jij aan de voorwaarden om opleidingsbudget aan te vragen via SOOB. EDUQ Opleidingen kan je daarover adviseren",
        "not available" => "niet beschikbaar",
        "student" => [
            "name" => "Naam *",
            "title" => "Cursist ",
            "firstname_cursist" => "Voornaam * ",
            "lastname_cursist" => "Achternaam * ",
            "email_cursist" => "E-mailadres *",
            "phone_cursist" => "Telefoonnummer *",
            "placeofbrith_cursist" => "Geboorteplaats *",
            "birtdate_cursist" => "dd/mm/jjjj",
            "birtdate_cursist_lbl" => "dd/mm/jjjj (geboortedatum) *",
            "delete_button" => "Cursist verwijderen ",
            "clear_fields" => "Maak velden leeg ",
            "fields" => [
                "firstname_cursist", "lastname_cursist", "email_cursist",
                "phone_cursist", "placeofbrith_cursist", "birtdate_cursist",
                "middlename_cursist"
            ],

        ],
        "cart" => [
            "redirect_btn_lbl" => "+ Cursus toevoegen",
            "back_btn_lbl" => "Terug naar cursus",
            "subtotal_lbl" => "Totaal winkelwagen",
            "no_of_students_lbl" => "Aantal",
            "edit_course_button" => "Cursus bewerken",
        ],
        "course" => [
            "buy_course_title" => "Cursus",
            "buy_course_lbl" => "Inschrijven",
            "SUMMARY" => "OVERZICHT",
            "total" => "Totaal",
            "submit_btn" => "Bevestigen",
            "course_lb_select" => "Cursus",
            "select_first_option" => "Selecteer",
            "student_panel_heading" => "GEGEVENS CURSIST(EN)",
            "calendar" => [
                "course_not_available_lbl" => "Contact Opnemen",
                "location" => "Locatie",
                "duration" => "Cursusduur",
                "price" => "Prijs",
                "inclusive" => "Inclusief",
                "options" => "Opties"
            ]
        ],
        "checkout" => [
            "no_of_courses" => "Aantal cursisten"
        ],

    ]
];
$contact_page = home_url('/contact');
$optional_courses = ["luisterexamen", CODE_95_SLUG, "licentie-lesmateriaal-ccv", "soob-subbsidie-klik-hier"];
$no_calender_msg = "Er zijn momenteel geen data bekend voor deze opleiding. Neem 
        <a id='redirect_btn' href='" . $contact_page . "'>contact</a> met ons op voor de mogelijkheden.";
$edit_warning_text = "Let op! Deze cursus zit al in je winkelwagen. Ga voor het wijzigen van je bestelling of het toevoegen van kandidaten naar je besteloverzicht. Als je doorgaat en de cursus opnieuw selecteert, gaan eerder ingevoerde gegevens voor deze cursus verloren.";
define('COURSE_ERROR_MESSAGES', $_MESSEGES);
define('COURSE_ERROR_MESSAGES_REPLACES', $_MESSEGES["messgae_replacements"]);

define('CALENDAR_ERROR_MESSAGES', $no_calender_msg);
define('COURSE_LISTENING_LBL', $_MESSEGES["labels"]["listening_exam"]);
define('COURSE_STUDENTS_LBL', $_MESSEGES["labels"]["student"]);
define('COURSE_DETAIL_LBL', $_MESSEGES["labels"]["course"]);
define('COURSE_DETAIL_CALENDAR_LBL', $_MESSEGES["labels"]["course"]["calendar"]);
define('COURSE_CHECKOUT_LBL', $_MESSEGES["labels"]["checkout"]);
define('COURSE_CART_LBL', $_MESSEGES["labels"]["cart"]);
define('CODE_95_LBL', $_MESSEGES["labels"]["code_95"]);
define('CODE_95_TOOLTIP', $_MESSEGES["labels"]["code_95"]);
define('SOB_TOOL_LBL', $_MESSEGES["labels"]["sob"]);
define('SOB_TOOL_TIP', $_MESSEGES["labels"]["sob_tool_tip"]);
define('COMPANY_NAME_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["company_name"]);
define('COMPANY_NAME_INVALID_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["company_name_valid"]);

define('USER_FIRST_NAME_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["user_first_name"]);
define('USER_FIRST_NAME_INVALID_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["user_first_name_valid"]);

define('USER_LAST_NAME_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["user_last_name"]);
define('USER_LAST_NAME_INVALID_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["user_last_name_valid"]);

define('USER_EMAIL_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["user_email"]);
define('USER_EMAIL_INVALID_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["user_email_valid"]);

define('USER_PHONE_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["user_phone"]);
define('USER_PHONE_INVALID_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["user_phone_valid"]);

define('BILLING_PHONE_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["billing_phone"]);
define('BILLING_PHONE_INVALID_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["billing_phone_valid"]);


define('BILLING_CUSTOM_EMAIL_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["billing_custom_email"]);
define('BILLING_CUSTOM_EMAIL_INVALID_ERROR_MESSAGE', $_MESSEGES["ERRORS"]["billing_custom_email_valid"]);

define('MULTISAFEPAY_IDEAL_VALID_MESSAGE', $_MESSEGES["ERRORS"]["multisafepay_ideal_valid"]);



define("OPTIONAL_COURSES", $optional_courses);
define('CURSIST_PHONE_LENGTH', 15);
define('CURSIST_PHONE_MIN_LENGTH', 7);
define('CURSIST_PHONE_MAX_LENGTH', 16);
define("NOT_AVAILABLE_LBL", $_MESSEGES["labels"]["not available"]);
define('INVALID_PAYMENT_TEXT', 'Invalid payment method.');
define('TRANSLATED_TEXT', 'Selecteer betaalmethode.');
define('THANKYOU_HEADING', 'Afrekenen');
define('THANKYOU_TITLE', 'Afrekenen');
define('MOST_CHOSEN', 'Meest gekozen:');
define('OTHER', 'Overig:');
define('EDIT_WARNING_TEXT', $edit_warning_text);
