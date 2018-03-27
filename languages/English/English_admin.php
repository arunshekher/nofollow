<?php
define('LAN_NOFOLLOW_SIMPLE_HTML_DOM_PARSER', 'Simple HTML DOM Parse Method');
define('LAN_NOFOLLOW_REGEX_PARSER', 'RegEx Parse Method');
define('LAN_NOFOLLOW_PREF_TAB_MAIN', 'Main');
define('LAN_NOFOLLOW_PREF_TAB_EXCLUSIONS', 'Manage Exclusions');

define('LAN_NOFOLLOW_PREF_VAL_CONTEXT_USER', 'User Posted Only');
define('LAN_NOFOLLOW_PREF_VAL_CONTEXT_USER_ADMIN', 'User + Admin Posted');
define('LAN_NOFOLLOW_PREF_VAL_CONTEXT_EVERYTHING', 'Everything');

define('LAN_NOFOLLOW_ACTIVATE', 'Activate NoFollow?');
define('LAN_NOFOLLOW_CONTEXT', '(NoFollow filtering context:)[In what content context should NoFollow filter be applied.])');
define('LAN_NOFOLLOW_GLOBAL_LIB', '(Use global path for simple dom parser lib)[Turn this on if you wish to share Simple DOM parser library with other e107 plugins or themes.]');
define('LAN_NOFOLLOW_EXCLUDE_PAGES', '( Exclude Pages: )[ Pages to exclude from nofollow filtering. Declare one page per line or separate with | sign. eg: ] + { news.php + contact.php} + OR + { news.php|contact.php }');
define('LAN_NOFOLLOW_EXCLUDE_DOMAINS', '( Exclude Domains: ) [Domain names to exclude from NoFollow filtering. One domain per line or use | sign to separate them. Your own domain name is excluded without declaring. eg: ] + { myaffiliate.com + friend.site } + OR + { myaffiliate.com|friend.site }');
define('LAN_NOFOLLOW_PARSE_METHOD', '(Parse Method to use: )[RegEx method has slight temporal advantage in benchmarks and profiling.]');

define('LAN_NOFOLLOW_HINT_EXCLUDE_PAGES', 'List of site pages that you want to exclude from Nofollow parse filter.');
define('LAN_NOFOLLOW_HINT_EXCLUDE_DOMAINS', 'List of domains that you want to exclude from Nofollow parse filter.');
define('LAN_NOFOLLOW_HINT_PARSE_METHOD', 'The method that you want to use for \'Nofollow\' parsing');
define('LAN_NOFOLLOW_HINT_ONPOST', 'Activate conversion of anchor tags with rel=&#39;nofollow&#39; while user makes posts.');
define('LAN_NOFOLLOW_HINT_ACTIVATE', 'Turn Nofollow on or off.');
define('LAN_NOFOLLOW_HINT_CONTEXT', 'In what context NoFollow parse filter is called for.');
define('LAN_NOFOLLOW_HINT_GLOBAL_LIB', 'Use global path for lib');

define('LAN_NOFOLLOW_INFO_MENU_TITLE', 'Project Info');
define('LAN_NOFOLLOW_INFO_MENU_SUBTITLE_GITHUB', 'Project on GitHub');
define('LAN_NOFOLLOW_INFO_MENU_SUBTITLE_ISSUES', 'Report Issues:');
define('LAN_NOFOLLOW_INFO_MENU_SUBTITLE_DEV', 'Thank the Developer!');
define('LAN_NOFOLLOW_INFO_MENU_DEV', 'Arun S. Sekher');
define('LAN_NOFOLLOW_INFO_MENU_SUPPORT_DEV_TEXT', 'If you enjoy this plugin, please consider supporting what I do.');
define('LAN_NOFOLLOW_INFO_MENU_SUPPORT_DEV_TEXT_SIGN', '"Thank you" &mdash; Arun');

define('LAN_NOFOLLOW_HELP_PAGE_CAPTION', 'Help');

