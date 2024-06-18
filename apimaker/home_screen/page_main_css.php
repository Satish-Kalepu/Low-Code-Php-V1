<style>
	:before,
	:after,
	a,
	body,
	button,
	div,
	h1,
	h2,
	header,
	html,
	li,
	p,
	span,
	ul,
	video {
		font: inherit;
		vertical-align: baseline;
		border: none;
		outline: 0;
		margin: 0;
		padding: 0;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box
	}

	header {
		display: block
	}

	body {
		line-height: 1em
	}

	a {
		text-decoration: none
	}

	svg {
		width: 100%;
		height: auto;
		overflow: visible
	}

	ul {
		list-style: none
	}

	button {
		background: none
	}

	body {
		font: normal 400 18px/28px 'Poppins';
		color: #717985
	}

	@media (max-width:899px) {
		body {
			font-size: 16px;
			line-height: 24px
		}
	}

	h1 {
		font: normal 700 54px/64px 'Poppins';
		color: #121217
	}

	@media (max-width:899px) {
		h1 {
			font-size: 46px;
			line-height: 48px
		}
	}

	@media (max-width:600px) {
		h1 {
			font-size: 28px;
			line-height: 32px
		}
	}

	h2 {
		font: normal 700 36px/54px 'Poppins';
		color: #121217
	}

	@media (max-width:899px) {
		h2 {
			font-size: 24px;
			line-height: 32px
		}
	}

	@media (max-width:600px) {
		h2 {
			font-size: 21px;
			line-height: 28px
		}
	}

	.container {
		padding-left: 160px;
		padding-right: 160px;
		margin-left: auto;
		margin-right: auto;
		width: 1920px
	}

	@media (max-width:1899px) {
		.container {
			width: 1600px;
			padding-left: 100px;
			padding-right: 100px
		}
	}

	@media (max-width:1599px) {
		.container {
			width: 1200px;
			padding-left: 60px;
			padding-right: 60px
		}
	}

	@media (max-width:1199px) {
		.container {
			width: 768px;
			padding-left: 20px;
			padding-right: 20px
		}
	}

	@media (max-width:767px) {
		.container {
			width: auto;
			padding-left: 10px;
			padding-right: 10px
		}
	}

	#fancybox-close {
		display: none !important
	}

	.wrapper {
		overflow: hidden
	}

	.button {
		display: inline-block;
		background: #5056e5;
		border: 2px solid #5056e5;
		border-radius: 4px;
		color: #ffffff;
		padding: 11px 20px 12px;
		font-weight: 700;
		font-size: 24px;
		line-height: 140%;
		text-align: center
	}

	@media (max-width:767px) {
		.button {
			font-size: 14px;
			line-height: 20px
		}
	}

	.button--transparent {
		background: transparent;
		color: inherit
	}

	.header {
		background: #121217;
		z-index: 100;
		position: relative;
		height: 100px
	}

	@media (max-width:1599px) {
		.header .header__aside__button {
			margin-left: 20px;
			padding: 6px 11px 7px;
			width: 130px;
			font-size: 20px
		}
	}

	@media (max-width:767px) {
		.header {
			height: 45px
		}
	}

	.header--transparent {
		background: transparent
	}

	.header--transparent .header-wrapper {
		background: transparent
	}

	.header--transparent .header-wrapper.show {
		background: #ffffff
	}

	.header--transparent .header-wrapper.show.top {
		background: transparent
	}

	.header--transparent .header__menu>.menu>.menu-item-has-children>a::after {
		border-color: #121217
	}

	.header--transparent .header__logo-link {
		background-image: url(https://backendless.com/wp-content/themes/backendless/assets/images/logos/logo.svg)
	}

	.header--transparent .header__aside__button.button--transparent {
		color: #5056e5
	}

	.header--transparent .header__burger:before,
	.header--transparent .header__burger:after,
	.header--transparent .header__burger span:before,
	.header--transparent .header__burger span:after {
		background: #303042
	}

	.header--transparent .header__menu .menu-item a {
		color: #121217
	}

	.header__light {
		position: absolute;
		right: 0;
		top: 0;
		width: 100%;
		height: 700px;
		z-index: -1;
		overflow: hidden
	}

	.header__light:after,
	.header__light:before {
		content: '';
		display: block;
		position: absolute;
		width: 505px;
		height: 505px;
		border-radius: 50%;
		background: #5056e5;
		mix-blend-mode: hard-light;
		opacity: 0.5;
		filter: blur(200px)
	}

	.header__light:after {
		right: 100px;
		top: -50px
	}

	.header__light:before {
		right: -138px;
		top: -122px
	}

	.header-wrapper {
		border-bottom: 1px solid rgba(80, 86, 229, 0.5)
	}

	.header-wrapper>.container {
		position: relative
	}

	.header-wrapper>.container:after {
		content: '';
		clear: both;
		display: table
	}

	.header:after {
		content: '';
		clear: both;
		display: table
	}

	.header__logo {
		width: 195px;
		float: left;
		position: relative;
		z-index: 101;
		padding-top: 13px;
		padding-bottom: 13px
	}

	@media (min-width:767px) {
		.header__logo {
			padding-top: 37px;
			padding-bottom: 37px
		}
	}

	.header__logo-link {
		display: block;
		text-indent: -999em;
		background: url(https://backendless.com/wp-content/themes/backendless/assets/images/logos/logo_white.svg) no-repeat center left;
		background-size: contain;
		padding-bottom: 9%;
		height: 0
	}

	@media (min-width:767px) {
		.header__logo-link {
			padding-bottom: 13%
		}
	}

	.header__aside {
		color: #bfc7d0;
		padding-top: 25px;
		float: right;
		position: relative;
		z-index: 101
	}

	@media (max-width:767px) {
		.header__aside {
			display: none
		}
	}

	.header__aside--mobile {
		display: none;
		position: relative;
		border-top: 1px solid rgba(80, 86, 229, 0.5);
		float: none;
		text-align: center;
		z-index: 100;
		padding-top: 30px;
		padding-bottom: 30px
	}

	@media (max-width:767px) {
		.header__aside--mobile {
			display: block
		}
	}

	.header__aside__button {
		margin-left: 25px;
		padding: 6px 20px 7px;
		width: 190px
	}

	@media (max-width:767px) {
		.header__aside__button {
			width: 160px
		}
	}

	@media (max-width:767px) {
		.header__aside__button--login {
			margin-left: 0
		}
	}

	.header__aside:after {
		content: '';
		clear: both;
		display: table
	}

	.header__burger {
		float: right;
		width: 44px;
		height: 44px;
		margin-left: 30px;
		position: relative;
		margin-top: 0;
		z-index: 104;
		display: none
	}

	@media (min-width:767px) {
		.header__burger {
			margin-top: 29px
		}
	}

	@media (max-width:1199px) {
		.header__burger {
			display: block
		}
	}

	.header__burger:before,
	.header__burger:after,
	.header__burger span:before,
	.header__burger span:after {
		content: '';
		display: block;
		height: 2px;
		background: #ffffff;
		position: absolute;
		left: 11px;
		right: 11px;
		margin: auto;
		transform-origin: center
	}

	.header__burger:before {
		top: 12px
	}

	.header__burger:after {
		bottom: 12px
	}

	.header__burger span:before,
	.header__burger span:after {
		top: 0;
		bottom: 0
	}

	.header__menu {
		float: right;
		padding-top: 43px
	}

	@media (max-width:1199px) {
		.header__menu {
			display: none
		}
	}

	.header__menu>.menu>.menu-item-has-children>.sub-menu:after {
		content: '';
		display: block;
		width: 13px;
		height: 13px;
		transform: rotate(45deg);
		background: #ffffff;
		position: absolute;
		left: 0;
		right: 0;
		top: -7px;
		margin: auto
	}

	.header__menu>.menu>.menu-item-has-children>a:after {
		content: '';
		width: 8px;
		height: 8px;
		border-left: 1px solid #ffffff;
		border-bottom: 1px solid #ffffff;
		display: inline-block;
		vertical-align: middle;
		transform-origin: center;
		transform: rotate(-45deg);
		margin-left: 8px;
		margin-top: 0px
	}

	.header__menu>.menu .sub-menu {
		visibility: hidden;
		transform: scaleY(0) translateX(-50%);
		transform-origin: center top;
		position: absolute;
		background: #ffffff;
		padding: 40px 30px;
		border-radius: 4px;
		box-shadow: 1px 1px 20px rgba(0, 0, 0, 0.1);
		left: 50%;
		top: 100%
	}

	.header__menu>.menu .sub-menu .menu-item {
		margin-left: 0;
		float: none;
		white-space: nowrap
	}

	.header__menu>.menu .sub-menu .menu-item:last-child {
		margin-bottom: 0
	}

	.header__menu>.menu .sub-menu .menu-item a {
		font-size: 24px;
		line-height: 32px;
		color: #121217
	}

	.header__menu>.menu .sub-menu .menu-item .sub-menu {
		position: static;
		box-shadow: none;
		background: transparent;
		padding: 20px 0 0;
		transform: none;
		overflow: hidden
	}

	.header__menu>.menu .sub-menu .menu-item .sub-menu .menu-item {
		width: 50%;
		min-width: 200px;
		float: left;
		white-space: normal
	}

	.header__menu>.menu .sub-menu .menu-item .sub-menu .menu-item:nth-child(2):after {
		content: '';
		width: 400px;
		display: block
	}

	.header__menu>.menu .sub-menu .menu-item .sub-menu .menu-item a {
		font-size: 14px;
		line-height: 24px;
		color: #303042
	}

	.header__menu>.menu .sub-menu .menu-item .sub-menu:after {
		content: '';
		clear: both;
		display: table
	}

	.header__menu>.menu .sub-menu:after {
		content: '';
		clear: both;
		display: table
	}

	.header__menu .menu-item {
		display: block;
		float: left;
		margin-left: 40px;
		padding-bottom: 25px;
		position: relative
	}

	@media (max-width:1599px) {
		.header__menu .menu-item {
			margin-left: 30px
		}
	}

	.header__menu .menu-item a {
		color: #ffffff;
		font-size: 16px;
		line-height: 16px;
		display: block
	}

	.header__menu .menu-item a .icon {
		display: inline-block;
		width: 16px;
		height: 16px;
		background: no-repeat center;
		background-size: contain;
		margin-right: 10px;
		vertical-align: middle
	}

	.header__menu:after {
		content: '';
		clear: both;
		display: table
	}

	:root {
		--wp-admin-theme-color: #007cba;
		--wp-admin-theme-color-darker-10: #006ba1;
		--wp-admin-theme-color-darker-20: #005a87
	}

	#fancybox-loading,
	#fancybox-loading div,
	#fancybox-overlay,
	#fancybox-wrap,
	.fancybox-bg,
	#fancybox-outer,
	#fancybox-content,
	#fancybox-close,
	#fancybox-title,
	#fancybox-left,
	#fancybox-right,
	.fancy-ico {
		box-sizing: content-box;
		-moz-box-sizing: content-box
	}

	#fancybox-loading {
		position: fixed;
		top: 50%;
		left: 50%;
		width: 40px;
		height: 40px;
		margin-top: -20px;
		margin-left: -20px;
		overflow: hidden;
		z-index: 111104;
		display: none
	}

	#fancybox-loading div {
		position: absolute;
		top: 0;
		left: 0;
		width: 40px;
		height: 480px;
	}

	#fancybox-overlay {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		z-index: 111100;
		display: none
	}

	#fancybox-tmp {
		padding: 0;
		margin: 0;
		border: 0;
		overflow: auto;
		display: none
	}

	#fancybox-wrap {
		position: absolute;
		top: 0;
		left: 0;
		padding: 20px;
		z-index: 111101;
		display: none
	}

	#fancybox-outer {
		position: relative;
		width: 100%;
		height: 100%;
		background: #fff;
		box-shadow: 0 0 20px #111;
		-moz-box-shadow: 0 0 20px #111;
		-webkit-box-shadow: 0 0 20px #111
	}

	#fancybox-content {
		width: 0;
		height: 0;
		padding: 0;
		position: relative;
		-webkit-overflow-scrolling: touch;
		overflow-y: auto;
		z-index: 111102;
		border: 0 solid #fff;
		background: #fff;
		-moz-background-clip: padding;
		-webkit-background-clip: padding;
		background-clip: padding-box
	}

	#fancybox-close {
		position: absolute;
		top: -15px;
		right: -15px;
		width: 30px;
		height: 30px;
		background: transparent url(https://backendless.com/wp-content/plugins/easy-fancybox/images/fancybox.png) -40px 0;
		z-index: 111103;
		display: none
	}

	#fancybox-left,
	#fancybox-right {
		position: absolute;
		bottom: 0;
		height: 100%;
		width: 35%;
		background: initial;
		z-index: 111102;
		display: none
	}

	#fancybox-left {
		left: 0
	}

	#fancybox-right {
		right: 0
	}

	#fancybox-left-ico,
	#fancybox-right-ico {
		position: absolute;
		top: 50%;
		left: -9999px;
		width: 30px;
		height: 30px;
		margin-top: -15px;
		z-index: 111102;
		display: block
	}

	#fancybox-title {
		z-index: 111102
	}

	.home-page {
		overflow: hidden;
		color: #121217
	}

	.home-page__section {
		position: relative;
		padding-top: 0px;
		padding-bottom: 80px
	}

	@media (max-width:1199px) {
		.home-page__section {
			padding-top: 40px
		}
	}

	@media (max-width:1599px) and (min-width:767px) {
		.home-page__section--price {
			display: none
		}
	}

	.home-page__section--header {
		padding-bottom: 0px;
		margin-bottom: 80px;
		background: linear-gradient(180deg, rgba(80, 86, 229, 0) 0%, rgba(80, 86, 229, 0.1) 70.65%)
	}

	.home-page__section-header {
		margin: 0 auto
	}

	.home-page__section-title {
		text-align: center;
		color: #121217;
		margin-bottom: 15px;
		font-size: 48px;
		line-height: 72px
	}

	@media (max-width:767px) {
		.home-page__section-title {
			font-size: 36px;
			line-height: 54px
		}
	}

	.home-page__section-title--price {
		text-align: left
	}

	.home-page__section-description {
		margin-top: 20px
	}

	.home-page__header {
		position: relative;
		margin-bottom: 100px;
		padding-top: 100px
	}

	@media (max-width:767px) {
		.home-page__header {
			margin-bottom: 0
		}
	}

	.home-page__header-video {
		width: 100%
	}

	.home-page__header-wrapper {
		width: 1195px
	}

	@media (max-width:1899px) {
		.home-page__header-wrapper {
			width: 1040px
		}
	}

	@media (max-width:1599px) {
		.home-page__header-wrapper {
			width: 100%
		}
	}

	.home-page__header-title {
		font-weight: 800;
		font-size: 80px;
		line-height: 90px;
		margin-bottom: 15px
	}

	@media (max-width:767px) {
		.home-page__header-title {
			font-size: 48px;
			line-height: 64px
		}
	}

	.home-page__header-description {
		font-weight: 400;
		font-size: 24px;
		line-height: 40px
	}

	@media (max-width:1599px) and (min-width:1199px) {
		.home-page__header-description {
			padding-right: 250px
		}
	}

	.home-page__header-link {
		position: absolute;
		font-weight: 600;
		font-size: 32px;
		line-height: 32px;
		color: #5056e5;
		border-bottom: 1px solid transparent;
		display: block;
		margin-bottom: 30px;
		white-space: nowrap;
		right: 0;
		bottom: 0
	}

	@media (max-width:1199px) {
		.home-page__header-link {
			margin-top: 50px;
			position: static;
			display: inline-block
		}
	}

	@media (max-width:767px) {
		.home-page__header-link {
			font-size: 24px;
			line-height: 30px
		}
	}

	.home-page__header-link>svg {
		fill: #5056e5;
		display: inline-block;
		width: 16px;
		vertical-align: middle;
		margin-top: -3px
	}

	.home-page__frbc__content {
		float: left;
		width: 60%;
		padding-left: 40px;
		padding-top: 140px
	}

	@media (max-width:1599px) {
		.home-page__frbc__content {
			display: none
		}
	}

	.home-page__frbc--invert .home-page__frbc__content {
		float: right;
		padding-left: 0;
		padding-right: 40px
	}

	.home-page__frbc__item-description {
		font-weight: 400;
		font-size: 17px;
		line-height: 26px;
		display: none
	}

	.home-page__frbc__item-image {
		padding-bottom: 56.25%;
		border-radius: 4px;
		background: no-repeat center;
		background-size: cover
	}

	.home-page__frbc__item-image--sub {
		display: none;
		margin-top: 40px
	}

	.home-page__frbc__item-info {
		display: none
	}

	.home-page__frbc__item-info.active {
		display: block
	}

	.home-page__price-package {
		width: 33.3333%;
		padding: 40px 50px 40px 40px;
		margin-top: 30px;
		margin-bottom: 40px;
		border-radius: 20px;
		background: #edeefc
	}

	.home-page__price-package:after {
		content: '';
		clear: both;
		display: table
	}

	@media (max-width:899px) {
		.home-page__price-package {
			width: 100%;
			margin-bottom: 20px;
			margin-top: 20px
		}
	}

	.home-page__price-package-items {
		display: flex;
		gap: 20px
	}

	@media (max-width:899px) {
		.home-page__price-package-items {
			display: block
		}
	}

	.home-page__price-package-items:after {
		content: '';
		clear: both;
		display: table
	}

	.home-page__price-package__image {
		background: no-repeat center;
		background-size: contain;
		margin-bottom: 20px;
		width: 46px;
		height: 46px
	}

	.home-page__price-package__title {
		margin-bottom: 10px;
		font-weight: 700;
		font-size: 20px;
		line-height: 30px
	}

	.home-page__price-package__description {
		margin-bottom: 40px;
		font-weight: 400;
		font-size: 17px;
		line-height: 26px
	}

	.home-page__price-package__link {
		font-weight: 600;
		font-size: 20px;
		line-height: 30px;
		color: #5056e5;
		border-bottom: 1px solid transparent
	}

	.home-page__price-package__link>svg {
		fill: #5056e5;
		display: inline-block;
		width: 16px;
		vertical-align: middle;
		margin-top: -3px
	}
</style>