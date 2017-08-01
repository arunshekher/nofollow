<?php
define('LAN_NOFOLLOW_SIMPLE_HTML_DOM_PARSER', 'Simple HTML DOM Parse Method');
define('LAN_NOFOLLOW_REGEX_PARSER', 'RegEx Parse Method');
define('LAN_NOFOLLOW_PREF_TAB_MAIN', 'Main');
define('LAN_NOFOLLOW_PREF_TAB_EXCLUSIONS', 'Manage Exclusions');

define('LAN_NOFOLLOW_PREF_VAL_CONTEXT_USER', 'User Posted Only');
define('LAN_NOFOLLOW_PREF_VAL_CONTEXT_USER_ADMIN', 'User + Admin Posted');
define('LAN_NOFOLLOW_PREF_VAL_CONTEXT_EVERYTHING', 'Everything');

define('LAN_NOFOLLOW_ACTIVATE', 'Activate NoFollow?');
define('LAN_NOFOLLOW_CONTEXT', '<p>NoFollow filtering context:</p><small>In what content context should NoFollow filter be applied.</small>');
define('LAN_NOFOLLOW_GLOBAL_LIB', '<p>Use global path for simple dom parser lib</p><small>Turn this on if you wish to share Simple DOM parser library with other e107 plugins or themes.</small>');
define('LAN_NOFOLLOW_EXCLUDE_PAGES', '<p>Exclude Pages: </p><small>Pages to exclude from nofollow filtering. Declare one page per line or separate with | sign. eg:</small><br><kbd> news.php<br> contact.php</kbd>');
define('LAN_NOFOLLOW_EXCLUDE_DOMAINS', '<p>Exclude Domains: </p><small>Domain names to exclude from NoFollow filtering. One domain per line or use | sign to separate them. Your domain name is excluded without declaring. eg:</small><br><kbd>myaffiliate.com<br>friend.site</kbd>');
define('LAN_NOFOLLOW_PARSE_METHOD_TO_USE', '<p>Parse Method to use: </p><small>RegEx method has slight temporal advantage in benchmarks and profiling.</small>');

define('LAN_NOFOLLOW_HINT_EXCLUDE_PAGES', 'List of site pages that you want to exclude from Nofollow parse filter.');
define('LAN_NOFOLLOW_HINT_EXCLUDE_DOMAINS', 'List of domains that you want to exclude from Nofollow parse filter.');
define('LAN_NOFOLLOW_HINT_PARSE_METHOD', 'The method that you want to use for \'Nofollow\' parsing');
define('LAN_NOFOLLOW_HINT_ONPOST', 'Activate conversion of anchor tags with rel=&#39;nofollow&#39; while user makes posts.');
define('LAN_NOFOLLOW_HINT_ACTIVATE', 'Turn Nofollow on or off.');
define('LAN_NOFOLLOW_HINT_CONTEXT', 'In what context NoFollow parse filter is called for.');
define('LAN_NOFOLLOW_HINT_GLOBAL_LIB', 'Use global path for lib');

define('LAN_NOFOLLOW_INFO_MENU_TITLE', 'Project Info');
define('LAN_NOFOLLOW_INFO_MENU_SUBTITLE_GITHUB', '<br><h5>Project repo on GitHub:</h5>');
define('LAN_NOFOLLOW_INFO_MENU_LOGO', '<div style="text-align: center"><img src="http://www.e107.space/projects/nofollow/svg" alt="Nofollow" width="128" height="128"></div>');
define('LAN_NOFOLLOW_INFO_MENU_SUBTITLE_DEV', '<h5>Developer:</h5>');
define('LAN_NOFOLLOW_INFO_MENU_DEV', '<p><small>Arun S. Sekher</small></p>');
define('LAN_NOFOLLOW_INFO_MENU_GITHUB_BUTTONS_SCRIPT', '<script async defer src="https://buttons.github.io/buttons.js"></script>');
define('LAN_NOFOLLOW_INFO_MENU_REPO_BUTTON_FOLLOW', '<a class="github-button" href="https://github.com/arunshekher" aria-label="Follow @arunshekher on GitHub">Follow</a>');
define('LAN_NOFOLLOW_INFO_MENU_REPO_URL', '<p><kbd style="word-wrap: break-word"><a href="http://github.com/arunshekher/nofollow" target="_blank">http://github.com/arunshekher/nofollow</a></kbd></p>');
define('LAN_NOFOLLOW_INFO_MENU_REPO_BUTTON_WATCH', '<a class="github-button" href="https://github.com/arunshekher/nofollow/subscription" data-icon="octicon-eye" aria-label="Watch arunshekher/nofollow on GitHub">Watch</a>');
define('LAN_NOFOLLOW_INFO_MENU_REPO_BUTTON_STAR', ' <a class="github-button" href="https://github.com/arunshekher/nofollow" data-icon="octicon-star" aria-label="Star arunshekher/nofollow on GitHub">Star</a>');
define('LAN_NOFOLLOW_INFO_MENU_REPO_BUTTON_ISSUE', ' <a class="github-button" href="https://github.com/arunshekher/nofollow/issues" data-icon="octicon-issue-opened" aria-label="Issue arunshekher/nofollow on GitHub">Issue</a>');

