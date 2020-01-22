<?php
/**
 * Проверяет ИНН (идентификационный номер налогоплательщика) на корректность
 * 
 * @link     https://ru.wikipedia.org/wiki/ИНН
 * @license  http://creativecommons.org/licenses/by-sa/3.0/
 * @author   https://github.com/rin-nas
 * @charset  UTF-8
 * @version  1.0.4
 */
class INN
{
	/*
	ИНН    10 цифр для юр. лиц, 12 цифр для физ. лиц
	БИК    9 цифр
	Счёт   20 цифр
	КБК    20 цифр
	ОКАТО  от 4 до 11 цифр
	КПП    9 цифр
	*/
	
	const IP = 1; // индивидуальный предприниматель
	const UL = 0; // юридическое лицо


	#запрещаем создание экземпляра класса, вызов методов этого класса только статически!
	private function __construct() {}

	/**
	 *
	 * @param   scalar|null  $n   10-ти или 12-ти значное целое число
	 * @param   int|null     $type - тип плательщика ИП или ЮЛ. Если ЮЛ - то обязательно 10 знаков, если ИП то 12
	 * @return  bool|null    TRUE, если ИНН корректен и FALSE в противном случае
	 */
	public static function isValid($n, , $type = null)
	{
		if ($n === null) return null;

		$n = strval($n);
		if (! ctype_digit($n)) {
			return false;
		}
		
		//все нули удовлетворяют формуле
		if ((int)$n === 0) {
			return false;
		}
		
		//не может быть региона 00
		if (substr($n, 0, 2) === '00') {
			return false;
		}

		$len = strlen($n);
		
		#10 знаков -- организации, для которых обязательно д.б. КПП
		if ($len === 10)
		{
			if ($type !== null && $type !== self::UL) {
				return false;
			}

			$sum = 0;
			foreach ([2, 4, 10, 3, 5, 9, 4, 6, 8] as $i => $weight)
			{
				$sum += $weight * $n[$i];
			}
			return $sum % 11 % 10 === $n[9];
		}

		#12 знаков -- индивидуальные предприниматели, для которых КПП отсутствует
		if ($len === 12)
		{
			if ($type !== null && $type !== self::IP) {
				return false;
			}

			$sum1 = 0;
			foreach ([7, 2, 4, 10, 3, 5, 9, 4, 6, 8] as $i => $weight)
			{
				$sum1 += $weight * $n[$i];
			}
			if (($sum1 % 11 % 10) !== $n[10])
			{
				return false;
			}
			
			$sum2 = 0;
			foreach ([3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8] as $i => $weight)
			{
				$sum2 += $weight * $n[$i];
			}
			if (($sum2 % 11 % 10) !== $n[11])
			{
				return false;
			}
		}
		return false;
	}
}

/*
#7725088527
echo INN::check('7725088527');
echo '<br />';
echo INN::check('7715034360');
echo '<br />';
echo INN::check('773370857141');
echo '<br />';
echo INN::check('344809916052');
echo '<br />';
*/
