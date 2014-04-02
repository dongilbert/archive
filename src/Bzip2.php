<?php
/**
 * Part of the Joomla Framework Archive Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive;

/**
 * Bzip2 format adapter for the Archive package
 *
 * @since  1.0
 */
class Bzip2 implements ExtractableInterface
{
	/**
	 * Bzip2 file data buffer
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $data = null;

	/**
	 * Holds the options array.
	 *
	 * @var    mixed  Array or object that implements \ArrayAccess
	 * @since  1.0
	 */
	protected $options = array();

	/**
	 * Create a new Archive object.
	 *
	 * @param   mixed  $options  An array of options or an object that implements \ArrayAccess
	 *
	 * @since   1.0
	 */
	public function __construct($options = array())
	{
		$this->options = $options;
	}

	/**
	 * Extract a Bzip2 compressed file to a given path
	 *
	 * @param   string  $archive      Path to Bzip2 archive to extract
	 * @param   string  $destination  Path to extract archive to
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 * @todo    Add support for PHP Streams
	 */
	public function extract($archive, $destination)
	{
		$this->data = file_get_contents($archive);

		if (!$this->data)
		{
			throw new \RuntimeException('Unable to read archive');
		}

		$buffer = bzdecompress($this->data);
		unset($this->data);

		if (empty($buffer))
		{
			throw new \RuntimeException('Unable to decompress data');
		}

		// If the destination directory doesn't exist we need to create it
		if (file_exists(dirname($destination)) === false && mkdir(dirname($destination), 0755, true) === false)
		{
			throw new \RuntimeException('Destination directory does not exist and could not be created.');
		}

		// Write out the file
		if (file_put_contents($destination, $buffer) === false)
		{
			throw new \RuntimeException('Unable to write archive to destination.');
		}

		return true;
	}

	/**
	 * Tests whether this adapter can unpack files on this computer.
	 *
	 * @return  boolean  True if supported
	 *
	 * @since   1.0
	 */
	public static function isSupported()
	{
		return extension_loaded('bz2');
	}
}
