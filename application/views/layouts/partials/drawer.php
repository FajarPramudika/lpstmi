<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


<a class="skip-link screen-reader-text" href="#main">Skip to content</a><div class="ct-drawer-canvas" data-location="start">
		<div id="search-modal" class="ct-panel" data-behaviour="modal" role="dialog" aria-label="Search modal" inert>
			<div class="ct-panel-actions">
				<button class="ct-toggle-close" data-type="type-1" aria-label="Close search modal">
					<svg class="ct-icon" width="12" height="12" viewBox="0 0 15 15"><path d="M1 15a1 1 0 01-.71-.29 1 1 0 010-1.41l5.8-5.8-5.8-5.8A1 1 0 011.7.29l5.8 5.8 5.8-5.8a1 1 0 011.41 1.41l-5.8 5.8 5.8 5.8a1 1 0 01-1.41 1.41l-5.8-5.8-5.8 5.8A1 1 0 011 15z"/></svg>				</button>
			</div>

			<div class="ct-panel-content">
				

<form role="search" method="get" class="ct-search-form"  action="<?= site_url('') ?>" aria-haspopup="listbox" data-live-results="thumbs">

	<input type="search" class="modal-field" placeholder="Search" value="" name="s" autocomplete="off" title="Search for..." aria-label="Search for...">

	<div class="ct-search-form-controls">
		
		<button type="submit" class="wp-element-button" data-button="icon" aria-label="Search button">
			<svg class="ct-icon ct-search-button-content" aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M14.8,13.7L12,11c0.9-1.2,1.5-2.6,1.5-4.2c0-3.7-3-6.8-6.8-6.8S0,3,0,6.8s3,6.8,6.8,6.8c1.6,0,3.1-0.6,4.2-1.5l2.8,2.8c0.1,0.1,0.3,0.2,0.5,0.2s0.4-0.1,0.5-0.2C15.1,14.5,15.1,14,14.8,13.7z M1.5,6.8c0-2.9,2.4-5.2,5.2-5.2S12,3.9,12,6.8S9.6,12,6.8,12S1.5,9.6,1.5,6.8z"/></svg>
			<span class="ct-ajax-loader">
				<svg viewBox="0 0 24 24">
					<circle cx="12" cy="12" r="10" opacity="0.2" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2"/>

					<path d="m12,2c5.52,0,10,4.48,10,10" fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2">
						<animateTransform
							attributeName="transform"
							attributeType="XML"
							type="rotate"
							dur="0.6s"
							from="0 12 12"
							to="360 12 12"
							repeatCount="indefinite"
						/>
					</path>
				</svg>
			</span>
		</button>

		
					<input type="hidden" name="ct_post_type" value="post:page">
		
		

		<input type="hidden" value="afc3785fa0" class="ct-live-results-nonce">	</div>

			<div class="screen-reader-text" aria-live="polite" role="status">
			No results		</div>
	
</form>


			</div>
		</div>

		<div id="offcanvas" class="ct-panel ct-header" data-behaviour="modal" role="dialog" aria-label="Offcanvas modal" inert="">
		<div class="ct-panel-actions">
			
			<button class="ct-toggle-close" data-type="type-2" aria-label="Close drawer">
				<svg class="ct-icon" width="12" height="12" viewBox="0 0 15 15"><path d="M1 15a1 1 0 01-.71-.29 1 1 0 010-1.41l5.8-5.8-5.8-5.8A1 1 0 011.7.29l5.8 5.8 5.8-5.8a1 1 0 011.41 1.41l-5.8 5.8 5.8 5.8a1 1 0 01-1.41 1.41l-5.8-5.8-5.8 5.8A1 1 0 011 15z"/></svg>
			</button>
		</div>
		<div class="ct-panel-content" data-device="desktop"><div class="ct-panel-content-inner"></div></div><div class="ct-panel-content" data-device="mobile"><div class="ct-panel-content-inner">
<nav
	class="mobile-menu menu-container has-submenu"
	data-id="mobile-menu" data-interaction="click" data-toggle-type="type-2" data-submenu-dots="no"	aria-label="Menu Utama">

	<ul id="menu-menu-utama-1" class=""><li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home menu-item-1794"><a href="<?= site_url('') ?>" class="ct-menu-link">HOME</a></li>
<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-1795"><span class="ct-sub-menu-parent"><a class="ct-menu-link">PROFILE</a><button class="ct-toggle-dropdown-mobile" aria-label="Expand dropdown menu" aria-haspopup="true" aria-expanded="false"><svg class="ct-icon toggle-icon-3" width="12" height="12" viewBox="0 0 15 15" aria-hidden="true"><path d="M2.6,5.8L2.6,5.8l4.3,5C7,11,7.3,11.1,7.5,11.1S8,11,8.1,10.8l4.2-4.9l0.1-0.1c0.1-0.1,0.1-0.2,0.1-0.3c0-0.3-0.2-0.5-0.5-0.5l0,0H3l0,0c-0.3,0-0.5,0.2-0.5,0.5C2.5,5.7,2.5,5.8,2.6,5.8z"/></svg></button></span>
<ul class="sub-menu">
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1816"><a href="<?= site_url('sejarah-kampus') ?>" class="ct-menu-link">SEJARAH KAMPUS</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1822"><a href="<?= site_url('visi-dan-misi') ?>" class="ct-menu-link">VISI DAN MISI</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1796"><a href="<?= site_url('akademik') ?>" class="ct-menu-link">AKADEMIK</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1820"><a href="<?= site_url('tupoksi') ?>" class="ct-menu-link">TUPOKSI</a></li>
	<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-1824"><span class="ct-sub-menu-parent"><a href="#" class="ct-menu-link">AKREDITASI</a><button class="ct-toggle-dropdown-mobile" aria-label="Expand dropdown menu" aria-haspopup="true" aria-expanded="false"><svg class="ct-icon toggle-icon-3" width="12" height="12" viewBox="0 0 15 15" aria-hidden="true"><path d="M2.6,5.8L2.6,5.8l4.3,5C7,11,7.3,11.1,7.5,11.1S8,11,8.1,10.8l4.2-4.9l0.1-0.1c0.1-0.1,0.1-0.2,0.1-0.3c0-0.3-0.2-0.5-0.5-0.5l0,0H3l0,0c-0.3,0-0.5,0.2-0.5,0.5C2.5,5.7,2.5,5.8,2.6,5.8z"/></svg></button></span>
	<ul class="sub-menu">
		<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1798"><a href="<?= site_url('akreditasi-sekolah-tinggi-manajemen-industri') ?>" class="ct-menu-link">SEKOLAH TINGGI MANAJEMEN INDUSTRI</a></li>
		<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1797"><a href="<?= site_url('akreditasi-politeknik-stmi') ?>" class="ct-menu-link">POLITEKNIK STMI JAKARTA</a></li>
	</ul>
</li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1819"><a href="<?= site_url('struktur-organsasi') ?>" class="ct-menu-link">STRUKTUR ORGANISASI</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1813"><a href="<?= site_url('profile-pejabat') ?>" class="ct-menu-link">PROFILE PEJABAT</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1805"><a href="<?= site_url('lokasi-kampus') ?>" class="ct-menu-link">LOKASI KAMPUS</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1802"><a href="<?= site_url('keanggotaan-senat') ?>" class="ct-menu-link">KEANGGOTAAN SENAT</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1818"><a href="<?= site_url('statistik') ?>" class="ct-menu-link">STATISTIK</a></li>
</ul>
</li>
<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-1825"><span class="ct-sub-menu-parent"><a href="#" class="ct-menu-link">PROGRAM STUDI</a><button class="ct-toggle-dropdown-mobile" aria-label="Expand dropdown menu" aria-haspopup="true" aria-expanded="false"><svg class="ct-icon toggle-icon-3" width="12" height="12" viewBox="0 0 15 15" aria-hidden="true"><path d="M2.6,5.8L2.6,5.8l4.3,5C7,11,7.3,11.1,7.5,11.1S8,11,8.1,10.8l4.2-4.9l0.1-0.1c0.1-0.1,0.1-0.2,0.1-0.3c0-0.3-0.2-0.5-0.5-0.5l0,0H3l0,0c-0.3,0-0.5,0.2-0.5,0.5C2.5,5.7,2.5,5.8,2.6,5.8z"/></svg></button></span>
<ul class="sub-menu">
	<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1826"><a href="http://tio.stmi.ac.id/" class="ct-menu-link">TEKNIK INDUSTRI OTOMOTIF</a></li>
	<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1827"><a href="http://siio.stmi.ac.id/" class="ct-menu-link">SISTEM INFORMASI INDUSTRI OTOMOTIF</a></li>
	<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1828"><a href="http://abo.stmi.ac.id/" class="ct-menu-link">ADMINISTRASI BISNIS OTOMOTIF</a></li>
	<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1829"><a href="http://tkp.stmi.ac.id/" class="ct-menu-link">TEKNIK KIMIA POLIMER</a></li>
	<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1830"><a href="http://tro.stmi.ac.id/" class="ct-menu-link">TEKNOLOGI REKAYASA OTOMOTIF</a></li>
	<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1831"><a class="ct-menu-link">TENAGA PENYULUH LAPANGAN</a></li>
</ul>
</li>
<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-1832"><span class="ct-sub-menu-parent"><a class="ct-menu-link">BERITA</a><button class="ct-toggle-dropdown-mobile" aria-label="Expand dropdown menu" aria-haspopup="true" aria-expanded="false"><svg class="ct-icon toggle-icon-3" width="12" height="12" viewBox="0 0 15 15" aria-hidden="true"><path d="M2.6,5.8L2.6,5.8l4.3,5C7,11,7.3,11.1,7.5,11.1S8,11,8.1,10.8l4.2-4.9l0.1-0.1c0.1-0.1,0.1-0.2,0.1-0.3c0-0.3-0.2-0.5-0.5-0.5l0,0H3l0,0c-0.3,0-0.5,0.2-0.5,0.5C2.5,5.7,2.5,5.8,2.6,5.8z"/></svg></button></span>
<ul class="sub-menu">
	<li class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-1834"><a href="<?= site_url('category/berita-kampus') ?>" class="ct-menu-link">BERITA KAMPUS</a></li>
	<li class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-1833"><a href="<?= site_url('category/pengumuman') ?>" class="ct-menu-link">PENGUMUMAN KAMPUS</a></li>
	<li class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-2727"><a href="<?= site_url('category/article') ?>" class="ct-menu-link">ARTIKEL</a></li>
	<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1835"><a href="<?= site_url('lowongan-kerja') ?>" class="ct-menu-link">LOWONGAN KERJA</a></li>
</ul>
</li>
<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-1836"><span class="ct-sub-menu-parent"><a class="ct-menu-link">PELAYANAN PUBLIK</a><button class="ct-toggle-dropdown-mobile" aria-label="Expand dropdown menu" aria-haspopup="true" aria-expanded="false"><svg class="ct-icon toggle-icon-3" width="12" height="12" viewBox="0 0 15 15" aria-hidden="true"><path d="M2.6,5.8L2.6,5.8l4.3,5C7,11,7.3,11.1,7.5,11.1S8,11,8.1,10.8l4.2-4.9l0.1-0.1c0.1-0.1,0.1-0.2,0.1-0.3c0-0.3-0.2-0.5-0.5-0.5l0,0H3l0,0c-0.3,0-0.5,0.2-0.5,0.5C2.5,5.7,2.5,5.8,2.6,5.8z"/></svg></button></span>
<ul class="sub-menu">
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1799"><a href="<?= site_url('daftar-informasi') ?>" class="ct-menu-link">DAFTAR INFORMASI</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1806"><a href="<?= site_url('maklumat-pelayanan') ?>" class="ct-menu-link">MAKLUMAT PELAYANAN</a></li>
	<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1837"><a class="ct-menu-link">PERINGATAN DINI</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1817"><a href="<?= site_url('sop') ?>" class="ct-menu-link">SOP</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1808"><a href="<?= site_url('mou') ?>" class="ct-menu-link">MOU</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1810"><a href="<?= site_url('penetapan-standar-pelayanan') ?>" class="ct-menu-link">PENETAPAN STANDAR PELAYANAN</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1801"><a href="<?= site_url('hasil-survey-kepuasan') ?>" class="ct-menu-link">HASIL SURVEY KEPUASAN</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1800"><a href="<?= site_url('hasil-indeks-persepsi-korupsi') ?>" class="ct-menu-link">HASIL INDEKS PERSEPSI KORUPSI</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1807"><a href="<?= site_url('materi-workshop') ?>" class="ct-menu-link">MATERI WORKSHOP</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1809"><a href="<?= site_url('pedoman-akademik-dan-non-akademik') ?>" class="ct-menu-link">PEDOMAN AKADEMIK DAN NON AKADEMIK</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-3038"><a href="<?= site_url('laporan-keuangan') ?>" class="ct-menu-link">LAPORAN KEUANGAN</a></li>
</ul>
</li>
<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-1838"><span class="ct-sub-menu-parent"><a class="ct-menu-link">PERATURAN</a><button class="ct-toggle-dropdown-mobile" aria-label="Expand dropdown menu" aria-haspopup="true" aria-expanded="false"><svg class="ct-icon toggle-icon-3" width="12" height="12" viewBox="0 0 15 15" aria-hidden="true"><path d="M2.6,5.8L2.6,5.8l4.3,5C7,11,7.3,11.1,7.5,11.1S8,11,8.1,10.8l4.2-4.9l0.1-0.1c0.1-0.1,0.1-0.2,0.1-0.3c0-0.3-0.2-0.5-0.5-0.5l0,0H3l0,0c-0.3,0-0.5,0.2-0.5,0.5C2.5,5.7,2.5,5.8,2.6,5.8z"/></svg></button></span>
<ul class="sub-menu">
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1811"><a href="<?= site_url('peraturan') ?>" class="ct-menu-link">PERATURAN</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1823"><a href="<?= site_url('zona-integritas') ?>" class="ct-menu-link">ZONA INTEGRITAS</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1815"><a href="<?= site_url('rencana-strategis') ?>" class="ct-menu-link">RENCANA STRATEGIS</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1814"><a href="<?= site_url('rencana-kinerja') ?>" class="ct-menu-link">RENCANA KINERJA</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1812"><a href="<?= site_url('perkin') ?>" class="ct-menu-link">PERKIN</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1803"><a href="<?= site_url('lakip') ?>" class="ct-menu-link">LAKIP</a></li>
	<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1804"><a href="<?= site_url('laporan-tri-wulan') ?>" class="ct-menu-link">LAPORAN TRI WULAN</a></li>
</ul>
</li>
<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1821"><a href="<?= site_url('unit-mahasiswa') ?>" class="ct-menu-link">UNIT MAHASISWA</a></li>
</ul></nav>

<div class="ct-contact-info" data-id="contacts">
		<ul data-icons-type="rounded:outline">
											<li class="">
					<span class="ct-icon-container"><svg aria-hidden="true"width='15' height='15' viewBox='0 0 15 15'><path d='M12.8 2.2C11.4.8 9.5 0 7.5 0S3.6.8 2.2 2.2C.8 3.6 0 5.5 0 7.5 0 11.6 3.4 15 7.5 15c1.6 0 3.3-.5 4.6-1.5.3-.2.4-.7.1-1-.2-.3-.7-.4-1-.1-1.1.8-2.4 1.3-3.7 1.3-3.4 0-6.1-2.8-6.1-6.1 0-1.6.6-3.2 1.8-4.3C4.3 2 5.9 1.4 7.5 1.4c1.6 0 3.2.6 4.3 1.8 1.2 1.2 1.8 2.7 1.8 4.3v.7c0 .8-.6 1.4-1.4 1.4s-1.4-.6-1.4-1.4V4.8c0-.4-.3-.7-.7-.7-.4 0-.7.3-.7.7-.4-.4-1.1-.7-1.9-.7-1.9 0-3.4 1.5-3.4 3.4s1.5 3.4 3.4 3.4c1 0 1.9-.5 2.5-1.2.5.7 1.3 1.2 2.2 1.2 1.5 0 2.7-1.2 2.7-2.7v-.7c.1-2-.7-3.9-2.1-5.3zM7.5 9.5c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z'/></svg></span>
											<div class="contact-info">
															<span class="contact-title">
									Email:								</span>
							
															<span class="contact-text">
									
									<?= html_escape($contacts['email']) ?>
																	</span>
													</div>
									</li>
											<li class="">
					<span class="ct-icon-container"><svg aria-hidden="true"width='15' height='15' viewBox='0 0 15 15'><path d='M12.3 15h-.2c-2.1-.2-4.1-1-5.9-2.1-1.6-1-3.1-2.5-4.1-4.1C1 7 .2 5 0 2.9-.1 1.8.7.8 1.8.7H4c1 0 1.9.7 2 1.7.1.6.2 1.1.4 1.7.3.7.1 1.6-.5 2.1l-.4.4c.7 1.1 1.7 2.1 2.9 2.9l.4-.5c.6-.6 1.4-.7 2.1-.5.6.3 1.1.4 1.7.5 1 .1 1.8 1 1.7 2v2c0 .5-.2 1-.6 1.4-.3.4-.8.6-1.4.6zM4 2.1H2c-.2 0-.3.1-.4.2-.1.1-.1.3-.1.4.2 1.9.8 3.7 1.8 5.3.9 1.5 2.2 2.7 3.7 3.7 1.6 1 3.4 1.7 5.3 1.9.2 0 .3-.1.4-.2.1-.1.2-.2.2-.4v-2c0-.3-.2-.5-.5-.6-.7-.1-1.3-.3-2-.5-.2-.1-.4 0-.6.1l-.8.9c-.2.2-.6.3-.9.1C6.4 10 5 8.6 4 6.9c-.2-.3-.1-.7.1-.9l.8-.8c.2-.2.2-.4.1-.6-.2-.6-.4-1.3-.5-2 0-.3-.2-.5-.5-.5zm7.7 4.5c-.4 0-.7-.2-.7-.6-.2-1-1-1.8-2-2-.4 0-.7-.4-.6-.8.1-.4.5-.7.9-.6 1.6.3 2.8 1.5 3.1 3.1.1.4-.2.8-.6.9h-.1zm2.6 0c-.4 0-.7-.3-.7-.6-.3-2.4-2.2-4.3-4.6-4.5-.4-.1-.7-.5-.6-.9 0-.4.4-.6.8-.6 3.1.3 5.4 2.7 5.8 5.8 0 .4-.3.7-.7.8z'/></svg></span>
											<div class="contact-info">
															<span class="contact-title">
									Phone:								</span>
							
															<span class="contact-text">
									
									<?= html_escape($contacts['phone']) ?>
																	</span>
													</div>
									</li>
											<li class="">
					<span class="ct-icon-container"><svg aria-hidden="true"width="20" height="20" viewBox="0,0,448,512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" /></svg></span>
											<div class="contact-info">
															<span class="contact-title">
									Whatsapp								</span>
							
															<span class="contact-text">
									
									<?= html_escape($contacts['whatsapp']) ?> 
																	</span>
													</div>
									</li>
					</ul>

		</div>
<div
	class="ct-header-socials "
	data-id="socials">

	
		<div class="ct-social-box" data-color="custom" data-icon-size="custom" data-icons-type="rounded:outline" >
			
			
							
				<a href="<?= html_escape($contacts['social_twitter']) ?>" data-network="twitter" aria-label="X (Twitter)" target="_blank" rel="noopener noreferrer">
					<span class="ct-icon-container">
					<svg
					width="20px"
					height="20px"
					viewBox="0 0 20 20"
					aria-hidden="true">
						<path d="M2.9 0C1.3 0 0 1.3 0 2.9v14.3C0 18.7 1.3 20 2.9 20h14.3c1.6 0 2.9-1.3 2.9-2.9V2.9C20 1.3 18.7 0 17.1 0H2.9zm13.2 3.8L11.5 9l5.5 7.2h-4.3l-3.3-4.4-3.8 4.4H3.4l5-5.7-5.3-6.7h4.4l3 4 3.5-4h2.1zM14.4 15 6.8 5H5.6l7.7 10h1.1z"/>
					</svg>
				</span>				</a>
							
				<a href="<?= html_escape($contacts['social_instagram']) ?>" data-network="instagram" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
					<span class="ct-icon-container">
					<svg
					width="20"
					height="20"
					viewBox="0 0 20 20"
					aria-hidden="true">
						<circle cx="10" cy="10" r="3.3"/>
						<path d="M14.2,0H5.8C2.6,0,0,2.6,0,5.8v8.3C0,17.4,2.6,20,5.8,20h8.3c3.2,0,5.8-2.6,5.8-5.8V5.8C20,2.6,17.4,0,14.2,0zM10,15c-2.8,0-5-2.2-5-5s2.2-5,5-5s5,2.2,5,5S12.8,15,10,15z M15.8,5C15.4,5,15,4.6,15,4.2s0.4-0.8,0.8-0.8s0.8,0.4,0.8,0.8S16.3,5,15.8,5z"/>
					</svg>
				</span>				</a>
							
				<a href="<?= html_escape($contacts['social_facebook']) ?>" data-network="facebook" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
					<span class="ct-icon-container">
					<svg
					width="20px"
					height="20px"
					viewBox="0 0 20 20"
					aria-hidden="true">
						<path d="M20,10.1c0-5.5-4.5-10-10-10S0,4.5,0,10.1c0,5,3.7,9.1,8.4,9.9v-7H5.9v-2.9h2.5V7.9C8.4,5.4,9.9,4,12.2,4c1.1,0,2.2,0.2,2.2,0.2v2.5h-1.3c-1.2,0-1.6,0.8-1.6,1.6v1.9h2.8L13.9,13h-2.3v7C16.3,19.2,20,15.1,20,10.1z"/>
					</svg>
				</span>				</a>
							
				<a href="<?= html_escape($contacts['social_youtube']) ?>" data-network="youtube" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
					<span class="ct-icon-container">
					<svg
					width="20"
					height="20"
					viewbox="0 0 20 20"
					aria-hidden="true">
						<path d="M15,0H5C2.2,0,0,2.2,0,5v10c0,2.8,2.2,5,5,5h10c2.8,0,5-2.2,5-5V5C20,2.2,17.8,0,15,0z M14.5,10.9l-6.8,3.8c-0.1,0.1-0.3,0.1-0.5,0.1c-0.5,0-1-0.4-1-1l0,0V6.2c0-0.5,0.4-1,1-1c0.2,0,0.3,0,0.5,0.1l6.8,3.8c0.5,0.3,0.7,0.8,0.4,1.3C14.8,10.6,14.6,10.8,14.5,10.9z"/>
					</svg>
				</span>				</a>
			
			
					</div>

	
</div>

<div
	class="ct-search-box "
	data-id="search-input">

	

<form role="search" method="get" class="ct-search-form" data-form-controls="inside" data-taxonomy-filter="false" data-submit-button="icon" action="<?= site_url('') ?>" aria-haspopup="listbox" >

	<input type="search"  placeholder="Search" value="" name="s" autocomplete="off" title="Search for..." aria-label="Search for...">

	<div class="ct-search-form-controls">
		
		<button type="submit" class="wp-element-button" data-button="inside:icon" aria-label="Search button">
			<svg class="ct-icon ct-search-button-content" aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M14.8,13.7L12,11c0.9-1.2,1.5-2.6,1.5-4.2c0-3.7-3-6.8-6.8-6.8S0,3,0,6.8s3,6.8,6.8,6.8c1.6,0,3.1-0.6,4.2-1.5l2.8,2.8c0.1,0.1,0.3,0.2,0.5,0.2s0.4-0.1,0.5-0.2C15.1,14.5,15.1,14,14.8,13.7z M1.5,6.8c0-2.9,2.4-5.2,5.2-5.2S12,3.9,12,6.8S9.6,12,6.8,12S1.5,9.6,1.5,6.8z"/></svg>
			<span class="ct-ajax-loader">
				<svg viewBox="0 0 24 24">
					<circle cx="12" cy="12" r="10" opacity="0.2" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2"/>

					<path d="m12,2c5.52,0,10,4.48,10,10" fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2">
						<animateTransform
							attributeName="transform"
							attributeType="XML"
							type="rotate"
							dur="0.6s"
							from="0 12 12"
							to="360 12 12"
							repeatCount="indefinite"
						/>
					</path>
				</svg>
			</span>
		</button>

		
					<input type="hidden" name="ct_post_type" value="post:page:book">
		
		

			</div>

	
</form>


</div>
</div></div></div></div>
<div id="main-container">
	