<?php
namespace mpblog\rss;
/*
 * Copyright (C) 2016 Michael Bemmerl <mail@mx-server.de>
 *
 * This file is part of the "Universal Feed Writer" project.
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * The exception that is thrown when an invalid operation is performed on
 * the object.
 *
 * @package     UniversalFeedWriter
 */
class InvalidArgumentException extends \Exception implements \Throwable {
	Public function __construct($message) {
	}

  // https://blog.eleven-labs.com/en/php7-throwable-error-exception/
  // public function getMessage(): string;       // Error reason
  // public function getCode(): int;             // Error code
  // public function getFile(): string;          // Error begin file
  // public function getLine(): int;             // Error begin line
  // public function getTrace(): array;          // Return stack trace as array like debug_backtrace()
  // public function getTraceAsString(): string; // Return stack trace as string
  // public function getPrevious(): Throwable;   // Return previous `Trowable`
  // public function __toString(): string;       // Convert into string
}
