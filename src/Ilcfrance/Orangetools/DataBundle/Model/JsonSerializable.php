<?php

namespace Ilcfrance\Orangetools\DataBundle\Model;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
interface JsonSerializable
{

	/**
	 *
	 * @param array $ignoreList
	 *
	 * @return array
	 */
	public function getJsonData($ignoreList = array());

	/**
	 *
	 * @return string
	 */
	public function getDataJson($ignoreList = array());

	/**
	 *
	 * @return array
	 */
	public function getAutoIgnoreList();

	/**
	 *
	 * @return array
	 */
	public function getIgnoreList();

}