<?php
/**
 * Air Framework
 * Copyright (C) 2018 Abderrahman Daif and Lionel Tordjman
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>. *
 */

namespace Air\Extensions;

use Air\Helper\TranslationHelper;

class ATwigExtension extends \Twig_Extension
{
	public function getFilters() {
		return [
			new \Twig_SimpleFilter(
				'trans', array($this, 'trans')
			)
		];
	}

	public function trans($string)
	{
		return TranslationHelper::loadTranslation($string);
	}
}