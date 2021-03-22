<?php

return [
    'admin_dir'                 => env('ADMIN_DIR', 'admin'),
    'default-theme'             => env('DEFAULT_THEME', 'default'),
    'base_name'                 => env('APP_NAME', 'Botble Technologies'),
    'logo'                      => '/vendor/core/core/base/images/logo_white.png',
    'favicon'                   => '/vendor/core/core/base/images/favicon.png',
    'editor'                    => [
        'ckeditor' => [
            'js' => [
                '/vendor/core/core/base/libraries/ckeditor/ckeditor.js',
            ],
        ],
        'tinymce'  => [
            'js' => [
                '/vendor/core/core/base/libraries/tinymce/tinymce.min.js',
            ],
        ],
        'primary'  => env('PRIMARY_EDITOR', 'ckeditor'),
    ],
    'error_reporting'           => [
        'to'           => null,
        'via_slack'    => env('SLACK_REPORT_ENABLED', false),
        'ignored_bots' => [
            'googlebot',        // Googlebot
            'bingbot',          // Microsoft Bingbot
            'slurp',            // Yahoo! Slurp
            'ia_archiver',      // Alexa
        ],
    ],
    'enable_https_support'      => env('ENABLE_HTTPS_SUPPORT', false),
    'date_format'               => [
        'date'      => 'd M, Y',
        'date_time' => 'd M, Y H:i:s',
        'js'        => [
            'date'      => 'd M, yyyy',
            'date_time' => 'd M, yyyy H:i:s',
        ],
    ],
    'cache_site_map'            => env('ENABLE_CACHE_SITE_MAP', false),
    'public_single_ending_url'  => env('PUBLIC_SINGLE_ENDING_URL'),
    'send_mail_using_job_queue' => env('SEND_MAIL_USING_JOB_QUEUE', false),
    'locale'                    => env('APP_LOCALE', 'en'),
    'can_execute_command'       => env('CAN_EXECUTE_COMMAND', true),
    'google_fonts'              => [
        'Aclonica',
        'Allan',
        'Annie Use Your Telescope',
        'Anonymous Pro',
        'Allerta Stencil',
        'Allerta',
        'Amaranth',
        'Anton',
        'Archivo',
        'Architects Daughter',
        'Arimo',
        'Artifika',
        'Arvo',
        'Asset',
        'Astloch',
        'Bangers',
        'Bentham',
        'Bevan',
        'Bigshot One',
        'Bowlby One',
        'Bowlby One SC',
        'Brawler',
        'Buda:300',
        'Cabin',
        'Calligraffitti',
        'Candal',
        'Cantarell',
        'Cardo',
        'Carter One',
        'Caudex',
        'Cedarville Cursive',
        'Cherry Cream Soda',
        'Chewy',
        'Coda',
        'Coming Soon',
        'Copse',
        'Corben:700',
        'Cousine',
        'Covered By Your Grace',
        'Crafty Girls',
        'Crimson Text',
        'Crushed',
        'Cuprum',
        'Damion',
        'Dancing Script',
        'Dawning of a New Day',
        'Didact Gothic',
        'Droid Sans',
        'Droid Sans Mono',
        'Droid Serif',
        'EB Garamond',
        'Expletus Sans',
        'Fontdiner Swanky',
        'Forum',
        'Francois One',
        'Geo',
        'Give You Glory',
        'Goblin One',
        'Goudy Bookletter 1911',
        'Gravitas One',
        'Gruppo',
        'Hammersmith One',
        'Holtwood One SC',
        'Homemade Apple',
        'Inconsolata',
        'Indie Flower',
        'IM Fell DW Pica',
        'IM Fell DW Pica SC',
        'IM Fell Double Pica',
        'IM Fell Double Pica SC',
        'IM Fell English',
        'IM Fell English SC',
        'IM Fell French Canon',
        'IM Fell French Canon SC',
        'IM Fell Great Primer',
        'IM Fell Great Primer SC',
        'Irish Grover',
        'Irish Growler',
        'Istok Web',
        'Josefin Sans',
        'Josefin Slab',
        'Judson',
        'Jura',
        'Jura:500',
        'Jura:600',
        'Just Another Hand',
        'Just Me Again Down Here',
        'Kameron',
        'Kenia',
        'Kranky',
        'Kreon',
        'Kristi',
        'La Belle Aurore',
        'Lato:100',
        'Lato:100italic',
        'Lato:300',
        'Lato',
        'Lato:bold',
        'Lato:900',
        'League Script',
        'Lekton',
        'Limelight',
        'Lobster',
        'Lobster Two',
        'Lora',
        'Love Ya Like A Sister',
        'Loved by the King',
        'Luckiest Guy',
        'Maiden Orange',
        'Mako',
        'Maven Pro',
        'Maven Pro:500',
        'Maven Pro:700',
        'Maven Pro:900',
        'Meddon',
        'MedievalSharp',
        'Megrim',
        'Merriweather',
        'Metrophobic',
        'Michroma',
        'Miltonian Tattoo',
        'Miltonian',
        'Modern Antiqua',
        'Monofett',
        'Molengo',
        'Montserrat',
        'Mountains of Christmas',
        'Muli:300',
        'Muli',
        'Neucha',
        'Neuton',
        'News Cycle',
        'Nixie One',
        'Nobile',
        'Noto Sans',
        'Nova Cut',
        'Nova Flat',
        'Nova Mono',
        'Nova Oval',
        'Nova Round',
        'Nova Script',
        'Nova Slim',
        'Nova Square',
        'Nunito:light',
        'Nunito',
        'Nunito Sans',
        'OFL Sorts Mill Goudy TT',
        'Old Standard TT',
        'Open Sans:300',
        'Open Sans',
        'Open Sans:600',
        'Open Sans:800',
        'Open Sans Condensed:300',
        'Orbitron',
        'Orbitron:500',
        'Orbitron:700',
        'Orbitron:900',
        'Oswald',
        'Over the Rainbow',
        'Reenie Beanie',
        'Pacifico',
        'Patrick Hand',
        'Paytone One',
        'Permanent Marker',
        'Philosopher',
        'Play',
        'Playfair Display',
        'Podkova',
        'Poppins',
        'PT Sans',
        'PT Sans Narrow',
        'PT Sans Narrow:regular,bold',
        'PT Serif',
        'PT Serif Caption',
        'Puritan',
        'Quattrocento',
        'Quattrocento Sans',
        'Radley',
        'Raleway',
        'Raleway:100',
        'Redressed',
        'Rock Salt',
        'Rokkitt',
        'Roboto',
        'Roboto Condensed',
        'Roboto Slab',
        'Ruslan Display',
        'Schoolbell',
        'Shadows Into Light',
        'Shanti',
        'Sigmar One',
        'Six Caps',
        'Slackey',
        'Smythe',
        'Sniglet:800',
        'Special Elite',
        'Stardos Stencil',
        'Sue Ellen Francisco',
        'Sunshiney',
        'Swanky and Moo Moo',
        'Syncopate',
        'Tajawal',
        'Tangerine',
        'Tenor Sans',
        'Terminal Dosis Light',
        'The Girl Next Door',
        'Tinos',
        'Ubuntu',
        'Ultra',
        'Unkempt',
        'UnifrakturCook:bold',
        'UnifrakturMaguntia',
        'Varela',
        'Varela Round',
        'Vibur',
        'Vollkorn',
        'VT323',
        'Waiting for the Sunrise',
        'Wallpoet',
        'Walter Turncoat',
        'Wire One',
        'Work Sans',
        'Yanone Kaffeesatz',
        'Yanone Kaffeesatz:300',
        'Yanone Kaffeesatz:400',
        'Yanone Kaffeesatz:700',
        'Yeseva One',
        'Zeyada',
    ],
];
