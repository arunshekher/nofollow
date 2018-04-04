<?php
if (!defined('e107_INIT')) { exit; }

if (deftrue('BOOTSTRAP') && deftrue('FONTAWESOME')) {
	define('NOFOLLOW_GITHUB_ICON',
		e107::getParser()->toGlyph('fa-github-alt', ['size' => '2x']));
	define('NOFOLLOW_HEART_ICON', e107::getParser()->toGlyph('fa-heart'));
	define('NOFOLLOW_SMILE_ICON', e107::getParser()->toGlyph('fa-smile-o', ['size' => '2x']));
} else {
	define('NOFOLLOW_GITHUB_ICON', '&nbsp;');
	define('NOFOLLOW_HEART_ICON', '&nbsp;');
	define('NOFOLLOW_SMILE_ICON', ':)');
}

$PROJECT_INFO_MENU_TEMPLATE = '
<div style="text-align: center">
<img src="https://www.e107.space/projects/nofollow/svg" alt="Mentions" width="128" height="128">
</div>
<ul class="list-unstyled">
	<li>
		<h5>' . NOFOLLOW_GITHUB_ICON . '&nbsp;' . LAN_NOFOLLOW_INFO_MENU_SUBTITLE_GITHUB . '</h5>
	</li>
	<li>
		<kbd style="word-wrap: break-word;font-size: x-small">
			<a href="http://github.com/arunshekher/nofollow" target="_blank">http://github.com/arunshekher/nofollow</a>
		</kbd>
	</li>
	<li>&nbsp;</li>
	<li class="text-center">
		<a class="github-button" href="https://github.com/arunshekher/nofollow/subscription" data-icon="octicon-eye" aria-label="Watch arunshekher/nofollow on GitHub">Watch</a>
		<a class="github-button" href="https://github.com/arunshekher/nofollow" data-icon="octicon-star" aria-label="Star arunshekher/nofollow on GitHub">Star</a>
	</li>
	<li>
		<h5>' . LAN_NOFOLLOW_INFO_MENU_SUBTITLE_ISSUES . '</h5>
	</li>
	<li class="text-center">
		<a class="github-button" href="https://github.com/arunshekher/nofollow/issues" data-icon="octicon-issue-opened" data-size="large" data-show-count="true" aria-label="Issue arunshekher/nofollow on GitHub">Issue</a>
	</li>
	<li style="border-bottom: solid 1px dimgrey" class="divider">&nbsp;</li>
	<li>
		<h5>' . NOFOLLOW_HEART_ICON . '&nbsp;' . LAN_NOFOLLOW_INFO_MENU_SUBTITLE_DEV . '</h5>
	</li>
	<li>
		<p>
			<small>{DEV_SUPPORT}</small>
		</p>
	</li>
	<li class="text-center">
		<script type="text/javascript" src="https://ko-fi.com/widgets/widget_2.js"></script>
		<script type="text/javascript">kofiwidget2.init("Buy Me a Coffee", "#46b798", "E1E4B43T");kofiwidget2.draw();</script>  
	</li>
	<li>&nbsp;</li>
	<li class="text-center">
		<a class="github-button" href="https://github.com/arunshekher" aria-label="Follow @arunshekher on GitHub">Follow @arunshekher</a>
	</li>
	<li class="text-center">
		<p style="padding-top:10px;">
			<small>{SIGN}</small>
		</p>
		<p>' . NOFOLLOW_SMILE_ICON .'</p>
	</li>
</ul>
<script async defer src="https://buttons.github.io/buttons.js"></script>';
