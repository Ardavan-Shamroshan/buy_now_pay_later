<?php

use Morilog\Jalali\Jalalian;

/**
 *
 * Jalali calendar is a solar calendar that was used in Persia, variants of which today are still in use in Iran as well as Afghanistan. Read more on Wikipedia or see Calendar Converter.
 * Calendar conversion is based on the algorithm provided by Kazimierz M. Borkowski and has a very good performance.
 * CalendarUtils class was ported from jalaali/jalaali-js
 *
 * Jalalian::forge('today')->format('%A, %d %B %y'); // جمعه، 23 اسفند 97
 *
 */
function jalaliDate( $date = 'today', $format = '%A, %d %B %Y', $ago = false ) {
	if ( $ago ) {
		return Jalalian::forge( $date )->ago();
	} else {
		return Jalalian::forge( $date )->format( $format );
	}
}

function convertPersianToEnglish( $number ) {
	$number = str_replace( '۰', '0', $number );
	$number = str_replace( '١', '1', $number );
	$number = str_replace( '۲', '2', $number );
	$number = str_replace( '۳', '3', $number );
	$number = str_replace( '۴', '4', $number );
	$number = str_replace( '۵', '5', $number );
	$number = str_replace( '۶', '6', $number );
	$number = str_replace( '۷', '7', $number );
	$number = str_replace( '۸', '8', $number );
	$number = str_replace( '۹', '9', $number );

	return $number;
}

function convertArabicToEnglish( $number ) {
	$number = str_replace( '۰', '0', $number );
	$number = str_replace( '١', '1', $number );
	$number = str_replace( '۲', '2', $number );
	$number = str_replace( '۳', '3', $number );
	$number = str_replace( '۴', '4', $number );
	$number = str_replace( '۵', '5', $number );
	$number = str_replace( '۶', '6', $number );
	$number = str_replace( '۷', '7', $number );
	$number = str_replace( '۸', '8', $number );
	$number = str_replace( '۹', '9', $number );

	return $number;
}

function convertEnglishToPersian( $number ) {

	$number = str_replace( '0', '۰', $number );
	$number = str_replace( '1', '۱', $number );
	$number = str_replace( '2', '۲', $number );
	$number = str_replace( '3', '۳', $number );
	$number = str_replace( '4', '۴', $number );
	$number = str_replace( '5', '۵', $number );
	$number = str_replace( '6', '۶', $number );
	$number = str_replace( '7', '۷', $number );
	$number = str_replace( '8', '۸', $number );
	$number = str_replace( '9', '۹', $number );

	return $number;
}

function priceFormat( $price ) {
	$price = number_format( $price, 0, '/', ',' );
	$price = convertEnglishToPersian( $price );
	$price .= ' تومان';

	return $price;
}

function discountFormat( $percentage ) {
	$percentage = convertEnglishToPersian( $percentage );
	$percentage .= '٪';

	return $percentage;
}

function validateNationalCode( $nationalCode ) {
	$nationalCode = trim( $nationalCode, ' .' );
	$nationalCode = convertArabicToEnglish( $nationalCode );
	$nationalCode = convertPersianToEnglish( $nationalCode );
	$bannedArray  = [
		'0000000000',
		'1111111111',
		'2222222222',
		'3333333333',
		'4444444444',
		'5555555555',
		'6666666666',
		'7777777777',
		'8888888888',
		'9999999999'
	];

	if ( empty( $nationalCode ) ) {
		return false;
	} elseif ( count( str_split( $nationalCode ) ) != 10 ) {
		return false;
	} elseif ( in_array( $nationalCode, $bannedArray, true ) ) {
		return false;
	} else {
		$sum = 0;

		for ( $i = 0; $i < 9; $i ++ ) {
			$sum += (int) $nationalCode[ $i ] * ( 10 - $i );
		}

		$divideRemaining = $sum % 11;

		if ( $divideRemaining < 2 ) {
			$lastDigit = $divideRemaining;
		} else {
			$lastDigit = 11 - ( $divideRemaining );
		}

		if ( (int) $nationalCode[9] == $lastDigit ) {
			return true;
		} else {
			return false;
		}
	}

}




