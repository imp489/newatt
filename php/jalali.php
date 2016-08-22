<?php
// \"jalali.php\" is convertor to and from Gregorian and Jalali calendars.
// Copyright (C) 2000 Roozbeh Pournader and Mohammad Toossi
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// A copy of the GNU General Public License is available from:
//
// http://www.gnu.org/copyleft/gpl.html
//
function div($a,$b) {
return (int) ($a / $b);
}
function gregorian_to_jalali ($g_y, $g_M, $g_d)
{
$g_days_in_Month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
$j_days_in_Month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
$gy = $g_y-1600;
$gm = $g_M-1;
$gd = $g_d-1;
$g_day_no = 365*$gy+div($gy+3,4)-div($gy+99,100)+div($gy+399,400);
for ($i=0; $i < $gm; ++$i)
$g_day_no += $g_days_in_Month[$i];
if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
/* leap and after Feb */
$g_day_no++;
$g_day_no += $gd;
$j_day_no = $g_day_no-79;
$j_np = div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */
$j_day_no = $j_day_no % 12053;
$jy = 979+33*$j_np+4*div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */
$j_day_no %= 1461;
if ($j_day_no >= 366) {
$jy += div($j_day_no-1, 365);
$j_day_no = ($j_day_no-1)%365;
}
for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_Month[$i]; ++$i)
$j_day_no -= $j_days_in_Month[$i];
$jm = $i+1;
$jd = $j_day_no+1;
return array($jy, $jm, $jd);
}
function jalali_to_gregorian($j_y, $j_M, $j_d)
{
$g_days_in_Month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
$j_days_in_Month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
$jy = $j_y-979;
$jm = $j_M-1;
$jd = $j_d-1;
$j_day_no = 365*$jy + div($jy, 33)*8 + div($jy%33+3, 4);
for ($i=0; $i < $jm; ++$i)
$j_day_no += $j_days_in_Month[$i];
$j_day_no += $jd;
$g_day_no = $j_day_no+79;
$gy = 1600 + 400*div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
$g_day_no = $g_day_no % 146097;
$leap = true;
if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */
{
$g_day_no--;
$gy += 100*div($g_day_no, 36524); /* 36524 = 365*100 + 100/4 - 100/100 */
$g_day_no = $g_day_no % 36524;
if ($g_day_no >= 365)
$g_day_no++;
else
$leap = false;
}
$gy += 4*div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
$g_day_no %= 1461;
if ($g_day_no >= 366) {
$leap = false;
$g_day_no--;
$gy += div($g_day_no, 365);
$g_day_no = $g_day_no % 365;
}
for ($i = 0; $g_day_no >= $g_days_in_Month[$i] + ($i == 1 && $leap); $i++)
$g_day_no -= $g_days_in_Month[$i] + ($i == 1 && $leap);
$gm = $i+1;
$gd = $g_day_no+1;
return array($gy, $gm, $gd);
}
?>