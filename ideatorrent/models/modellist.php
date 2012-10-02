<?php

/*
Copyright (C) 2007-2008 Nicolas Deschildre <ndeschildre@gmail.com>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/



/**
 * This class represents a list of model.
 * This *extends* Model, since it uses the same methods than Model to retrieve SQL data,
 * but while Model will only retrieve one raw, ModelList will retrieve many.
 * So this class has a few extra methos such as SetFilterParameters, to set filters on the
 * data retrieval.
 */
class ModelList extends Model
{


	/**
	 * Set the data filter. This will be used to control the columns of data returned.
	 * Useful to reduce SQL processing time.
	 */
	function setDataFilter($filter_array)
	{

	}

	/**
	 * Set the filter parameters, that will be used to filter data. Giving the GET array is usually fine.
	 * It will sanitize the necessary stuff.
	 */
	function setFilterParameters($getarray)
	{
		return "";
	}

}

?>
