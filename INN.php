<?php
/**
 * Проверяет ИНН (идентификационный номер налогоплательщика) на корректность
 * 
 * @link	 https://ru.wikipedia.org/wiki/ИНН
 * @license  http://creativecommons.org/licenses/by-sa/3.0/
 * @author   https://github.com/rin-nas
 * @charset  UTF-8
 * @version  1.0.3
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

	#запрещаем создание экземпляра класса, вызов методов этого класса только статически!
	private function __construct() {}

	/**
	 *
	 * @param   scalar|null  $n   целое число
	 * @return  bool|null    TRUE, если ИНН корректен и FALSE в противном случае
	 */
	public static function valid($n)
	{
		if (! ReflectionTypeHint::isValid()) return false;
		if ($n === null) return null;

		$n = strval($n);
		if (! in_array(strlen($n), array(10, 12)) || ! ctype_digit($n)) return false;

		#10 знаков -- организации, для которых обязательно д.б. КПП
		if (strlen($n) == 10)
		{
			$sum = 0;
			foreach (array(2, 4, 10, 3, 5, 9, 4, 6, 8) as $i => $weight)
			{
				$sum += $weight * substr($n, $i, 1);
			}
			return $sum % 11 % 10 == substr($n, 9, 1);
		}

		#12 знаков -- индивидуальные предприниматели, для которых КПП отсутствует
		$sum1 = $sum2 = 0;
		foreach (array(7, 2, 4, 10, 3, 5, 9, 4, 6, 8) as $i => $weight)
		{
			$sum1 += $weight * substr($n, $i, 1);
		}
		foreach (array(3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8) as $i => $weight)
		{
			$sum2 += $weight * substr($n, $i, 1);
		}
		return ($sum1 % 11 % 10) . ($sum2 % 11 % 10) == substr($n, 10, 2);
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
