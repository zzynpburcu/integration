-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 16 Kas 2022, 08:34:09
-- Sunucu sürümü: 10.4.21-MariaDB
-- PHP Sürümü: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `integration_db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `pazaryeri`
--

CREATE TABLE `pazaryeri` (
  `id` int(11) NOT NULL,
  `isim` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Tablo döküm verisi `pazaryeri`
--

INSERT INTO `pazaryeri` (`id`, `isim`, `slug`, `image`) VALUES
(1, 'N11', 'n11', 'n11'),
(2, 'Hepsiburada', 'hepsiburada', 'hb'),
(4, 'Trendyol', 'trendyol', 'trendyol'),
(8, 'Çicek Sepeti', 'ciceksepeti', 'ciceksepeti');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `pazaryeri_magaza`
--

CREATE TABLE `pazaryeri_magaza` (
  `id` int(11) NOT NULL,
  `pazar_id` int(11) DEFAULT NULL,
  `api_anahtar` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `api_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `api_sifre` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `roleName` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `rolePass` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `api_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `api_url_kullanici` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `aciklama` text CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `kargo` int(15) DEFAULT NULL,
  `createdDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `isim` text CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tablo döküm verisi `pazaryeri_magaza`
--

INSERT INTO `pazaryeri_magaza` (`id`, `pazar_id`, `api_anahtar`, `api_id`, `api_sifre`, `roleName`, `rolePass`, `api_url`, `api_url_kullanici`, `aciklama`, `kargo`, `createdDate`, `isim`) VALUES
(27, 1, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-13 14:31:35', 'n11'),
(30, 4, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-13 14:33:41', 'trendyol'),
(33, 2, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-25 11:18:44', 'hepsiburada');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `pazaryeri_sorucevap`
--

CREATE TABLE `pazaryeri_sorucevap` (
  `id` int(11) NOT NULL,
  `soru_metni` varchar(1000) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `soru_id` varchar(50) NOT NULL,
  `urun_adi` varchar(500) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `musteri_adi` varchar(100) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `durum` varchar(100) NOT NULL,
  `urun_url` varchar(100) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `urun_resim_url` varchar(500) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `soru_tarih` varchar(50) NOT NULL,
  `cevap_id` varchar(10) DEFAULT NULL,
  `cevap_metni` varchar(500) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `cevap_tarih` varchar(10) DEFAULT NULL,
  `yanit_sure` varchar(50) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `createdDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `pazar_id` int(10) DEFAULT NULL,
  `mesaj_id` varchar(200) DEFAULT NULL,
  `musteri_adi_goster` varchar(10) DEFAULT '0' COMMENT '1: adı gözüküyor; 0: gözükmüyor'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siparis`
--

CREATE TABLE `siparis` (
  `id` int(11) NOT NULL,
  `siparisno` longtext COLLATE utf8_turkish_ci NOT NULL,
  `odemetipi` longtext COLLATE utf8_turkish_ci NOT NULL,
  `urunid` longtext COLLATE utf8_turkish_ci NOT NULL,
  `adet` longtext COLLATE utf8_turkish_ci NOT NULL,
  `ekozellikid` longtext COLLATE utf8_turkish_ci NOT NULL,
  `ekozellikadet` longtext COLLATE utf8_turkish_ci NOT NULL,
  `uyetip` int(11) NOT NULL DEFAULT 0,
  `vergibilgileri` longtext COLLATE utf8_turkish_ci NOT NULL,
  `adsoyad` longtext COLLATE utf8_turkish_ci DEFAULT NULL,
  `telefon` longtext COLLATE utf8_turkish_ci DEFAULT NULL,
  `email` longtext COLLATE utf8_turkish_ci DEFAULT NULL,
  `adres` longtext COLLATE utf8_turkish_ci DEFAULT NULL,
  `il` longtext COLLATE utf8_turkish_ci DEFAULT NULL,
  `ilce` longtext COLLATE utf8_turkish_ci DEFAULT NULL,
  `faturatip` int(11) NOT NULL DEFAULT 0,
  `faturaadres` longtext COLLATE utf8_turkish_ci DEFAULT NULL,
  `faturail` longtext COLLATE utf8_turkish_ci DEFAULT NULL,
  `faturailce` longtext COLLATE utf8_turkish_ci NOT NULL,
  `mesaj` longtext COLLATE utf8_turkish_ci NOT NULL,
  `onay` int(11) NOT NULL,
  `aratutar` decimal(10,2) DEFAULT NULL,
  `kdvtutari` decimal(10,2) DEFAULT NULL,
  `kargotutari` decimal(10,2) DEFAULT NULL,
  `havaleindirimtutari` decimal(10,2) DEFAULT 0.00,
  `kuponid` int(11) NOT NULL DEFAULT 0,
  `kupontutari` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cekid` int(11) NOT NULL DEFAULT 0,
  `cektutari` decimal(10,2) NOT NULL DEFAULT 0.00,
  `kapitutar` decimal(10,2) NOT NULL DEFAULT 0.00,
  `toplamtutar` decimal(10,2) NOT NULL DEFAULT 0.00,
  `uye` varchar(11) COLLATE utf8_turkish_ci DEFAULT '0',
  `kim` longtext COLLATE utf8_turkish_ci DEFAULT NULL,
  `gelenkim` longtext COLLATE utf8_turkish_ci NOT NULL,
  `uruntablo` longtext COLLATE utf8_turkish_ci NOT NULL,
  `mailtablo` longtext COLLATE utf8_turkish_ci NOT NULL,
  `durum` longtext COLLATE utf8_turkish_ci NOT NULL,
  `tarih` datetime NOT NULL,
  `ekdurum` int(11) NOT NULL DEFAULT 0,
  `eknot` longtext COLLATE utf8_turkish_ci NOT NULL,
  `kargoid` int(11) NOT NULL DEFAULT 0,
  `takipno` longtext COLLATE utf8_turkish_ci NOT NULL,
  `pazar_id` varchar(10) COLLATE utf8_turkish_ci DEFAULT '0',
  `tarihson` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `siparis`
--

INSERT INTO `siparis` (`id`, `siparisno`, `odemetipi`, `urunid`, `adet`, `ekozellikid`, `ekozellikadet`, `uyetip`, `vergibilgileri`, `adsoyad`, `telefon`, `email`, `adres`, `il`, `ilce`, `faturatip`, `faturaadres`, `faturail`, `faturailce`, `mesaj`, `onay`, `aratutar`, `kdvtutari`, `kargotutari`, `havaleindirimtutari`, `kuponid`, `kupontutari`, `cekid`, `cektutari`, `kapitutar`, `toplamtutar`, `uye`, `kim`, `gelenkim`, `uruntablo`, `mailtablo`, `durum`, `tarih`, `ekdurum`, `eknot`, `kargoid`, `takipno`, `pazar_id`, `tarihson`) VALUES
(1, '', 'Kredi Kartı', '', '1', '0', '0', 0, 'TC No :', 'test', '', '', 'PURSAKLAR', 'istanbul', 'ANKARA', 0, 'PURSAKLAR', 'istanbul', 'ANKARA', '', 1, '291.29', '52.43', '27.50', '0.00', 0, '0.00', 0, '0.00', '0.00', '371.22', '0', '', '', '\n			                             <tr>\n										 <td class=\'product-col\'>\n												<div class=\'product\'>\n													<figure class=\'product-media\'>\n														<a href=\'https://parcauzmani.com/urun/bobin-crx236-palio-albea-punto-1-2-16v-tempra-1-6\'>\n															<img style=\'width: 60px;height: 60px;\' src=\'https://parcauzmani.com/resimler/urunler/674116027f3d31c498ae9135473652505f5042e986.jpg\'>\n														</a>\n													</figure>\n													<h3 class=\'product-title\'>\n														<a href=\'https://parcauzmani.com/urun/bobin-crx236-palio-albea-punto-1-2-16v-tempra-1-6\'><b style=\'font-weight: 400;\'>BOBİN CRX236 PALİO ALBEA PUNTO 1.2-16V TEMPRA 1.6</b></br>\n														<div style=\'font-weight: 400;font-size: 12px;margin-left: 2px;\'>\n														</div>\n														</a>\n														<div class=\'badges\' style=\'margin-top: 10px;display: inline-block;width: 300px;\'>\n														 \n														 \n														 \n														</div>\n													</h3><!-- End .product-title -->\n												</div><!-- End .product -->\n											</td>\n											<td class=\'price-col\'>291.29 TL</td>\n											<td class=\'price-col\' style=\'font-size: 14px;\'>(% 18)</br>\n											 52.43 TL</td>\n											<td class=\'quantity-col\'>\n                                                <div class=\'cart-product-quantity\'>   \n											     1\n												</div><!-- End .cart-product-quantity -->\n                                            </td>\n											<td class=\'total-col\'>343.72 TL</td>\n										</tr>\n                                        ', '\n <tr style=\'direction:ltr!important\'>\n <td style=\'border-bottom-color:#ccc; border-bottom-\n style:dotted; border-right-color:#ccc; border-right-style:dotted; direction:ltr!importa\n nt; border-collapse:collapse; font-weight:normal; padding:10px; border-width:0 1px 1px 0\' valign=\'top\' align=\'left\'>\n <table style=\'direction:ltr!important; border-collapse:collap\n se; border:0\' width=\'100%\' cellspacing=\'0\' cellpadding=\'0\' border=\'0\'>\n <tbody>\n <tr style=\'direction:ltr!important\'>\n <td class=\'x_product-img\' style=\'direction:ltr!important; \n border-collapse:collapse; font-weight:normal; padding:0 10px 0 0;\n border:0\' width=\'80\' valign=\'top\' align=\'left\'>\n <a href=\'https://parcauzmani.com/urun/bobin-crx236-palio-albea-punto-1-2-16v-tempra-1-6\' target=\'_blank\' rel=\'noopener noreferrer\' data-auth=\'NotApplicable\' style=\'direction:ltr!important\' data-linkindex=\'1\'>\n <img data-imagetype=\'External\' src=\'https://parcauzmani.com/resimler/urunler/674116027f3d31c498ae9135473652505f5042e986.jpg\' style=\'display:block; direction:ltr!important;\n height:auto; line-height:100%; outline:none; text-decoration:none; border:0\' width=\'80\' border=\'0\'></a>\n </td>\n <td style=\'font-family:Arial; font-size:13px; line-height:18px; color:#484848;\n direction:ltr!important; border-collapse:collapse; font-weight:normal; padding:0;\n border:0\' valign=\'top\' align=\'left\'>\n <a href=\'https://parcauzmani.com/urun/bobin-crx236-palio-albea-punto-1-2-16v-tempra-1-6\' target=\'_blank\' rel=\'noopener noreferrer\' data-auth=\'NotApplicable\' style=\'font-family:Arial; font-size:13px; \n line-height:18px; color:#484848; text-decoration:none; direction:ltr!important\' data-linkindex=\'2\'>BOBİN CRX236 PALİO ALBEA PUNTO 1.2-16V TEMPRA 1.6\n <div style=\'font-weight: 400;font-size: 12px;margin-left: 2px;\'>\n </div>\n <br style=\'direction:ltr!important\'>\n <span style=\'color:#8c8c8c; font-size:11px; direction:ltr!important;display: inline-block;width: 300px;\'>\n <div class=\'badges\' style=\'margin-top: 10px;\'>\n \n \n </div></span>\n </a>\n </td>\n </tr>\n </tbody>\n </table>\n </td>\n  <td style=\'border-bottom-color:#ccc; border-bottom-style:dotted;\n border-right-color:#ccc; border-right-style:dotted; font-family:Arial;\n font-size:13px; line-height:18px; color:#484848; direction:ltr!important;\n border-collapse:collapse; font-weight:normal; padding:10px; border-width:0 1px 1px 0\'\n valign=\'top\' align=\'center\'>291.29 TL</td>\n <td style=\'border-bottom-color:#ccc; border-bottom-style:dotted;\n border-right-color:#ccc; border-right-style:dotted; font-family:Arial;\n font-size:13px; line-height:18px; color:#484848; direction:ltr!important;\n border-collapse:collapse; font-weight:normal; padding:10px; border-width:0 1px 1px 0\'\n valign=\'top\' align=\'center\'>(% 18)</br>\n 52.43 TL</td>\n <td style=\'border-bottom-color:#ccc; border-bottom-style:dotted;\n border-right-color:#ccc; border-right-style:dotted; font-family:Arial;\n font-size:13px; line-height:18px; color:#484848; direction:ltr!important;\n border-collapse:collapse; font-weight:normal; padding:10px; border-width:0 1px 1px 0\'\n valign=\'top\' align=\'center\'>1</td>\n <td style=\'border-bottom-color:#ccc; border-bottom-style:dotted;\n font-family:Arial; font-size:13px; line-height:18px; color:#484848;\n direction:ltr!important; border-collapse:collapse; font-weight:normal;\n padding:10px; border-width:0 0 1px\' valign=\'top\' align=\'center\'>343.72 TL</td>\n </tr>\n\n ', 'Sipariş Onaylandı', '0000-00-00 00:00:00', 0, '', 0, '', '1', '2022-09-24 14:43:43');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siparis_urun`
--

CREATE TABLE `siparis_urun` (
  `id` int(11) NOT NULL,
  `urun_id` varchar(100) NOT NULL,
  `siparis_id` int(50) NOT NULL,
  `urun_adi` varchar(500) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `birim_fiyat` varchar(10) DEFAULT NULL,
  `kdv` varchar(10) DEFAULT NULL,
  `toplam_tutar` varchar(10) DEFAULT NULL,
  `adet` varchar(10) DEFAULT NULL,
  `createdDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `urun_kodu` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `pazaryeri`
--
ALTER TABLE `pazaryeri`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Tablo için indeksler `pazaryeri_magaza`
--
ALTER TABLE `pazaryeri_magaza`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `pazaryeri_sorucevap`
--
ALTER TABLE `pazaryeri_sorucevap`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `siparis`
--
ALTER TABLE `siparis`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `siparis_urun`
--
ALTER TABLE `siparis_urun`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `pazaryeri`
--
ALTER TABLE `pazaryeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Tablo için AUTO_INCREMENT değeri `pazaryeri_magaza`
--
ALTER TABLE `pazaryeri_magaza`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Tablo için AUTO_INCREMENT değeri `pazaryeri_sorucevap`
--
ALTER TABLE `pazaryeri_sorucevap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `siparis`
--
ALTER TABLE `siparis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `siparis_urun`
--
ALTER TABLE `siparis_urun`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
