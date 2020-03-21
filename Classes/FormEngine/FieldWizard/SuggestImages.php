<?php
declare(strict_types=1);

namespace A7digital\A7picsuggest\FormEngine\FieldWizard;

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

use TYPO3\CMS\Backend\Form\AbstractNode;

/**
 * Adds the necessary HTML/CSS/JS to suitable image fields in the backend so suggestions will be loaded.
 */
class SuggestImages extends AbstractNode
{
    /**
     * @inheritDoc
     */
    public function render()
    {
        $result = $this->initializeResultArray();

        $html = [];
        $html[] = '<div class="a7picsuggest-suggestions">';
        $html[] =   'TEST';
        $html[] = '</div>';

        $result['html'] = implode(LF, $html);
        return $result;
    }
}
