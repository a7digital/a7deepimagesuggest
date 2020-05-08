<?php
declare(strict_types=1);

namespace A7digital\A7picsuggest\Domain;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020, a7digital GmbH
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Resource\File;

class Suggestion
{
    /** @var float */
    private $weight;
    /** @var int */
    private $fileUid;
    /** @var File */
    private $file;

    public function __construct(float $weight, ?int $fileUid=null, File $file=null)
    {
        $this->weight = $weight;
        $this->file = $file;
        $this->fileUid = $fileUid;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getFileUid(): int
    {
        if ($this->fileUid === null && $this->file !== null) {
            return $this->file->getUid();
        }
        return $this->fileUid;
    }

    /**
     * @param int $fileUid
     */
    public function setFileUid(int $fileUid): void
    {
        $this->fileUid = $fileUid;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file): void
    {
        $this->file = $file;
    }


}
