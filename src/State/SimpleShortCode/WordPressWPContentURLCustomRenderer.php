<?php
/*
 *     Copyright (c) 2022-2024. Olayemi Faruq <olayemi@tonics.app>
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU Affero General Public License as
 *     published by the Free Software Foundation, either version 3 of the
 *     License, or (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU Affero General Public License for more details.
 *
 *     You should have received a copy of the GNU Affero General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Devsrealm\TonicsTemplateSystemExtensionBundle\State\SimpleShortCode;

use Devsrealm\TonicsTemplateSystem\Exceptions\TonicsTemplateRangeException;
use Devsrealm\TonicsTemplateSystem\Interfaces\TonicsModeRendererInterface;
use Devsrealm\TonicsTemplateSystem\Interfaces\TonicsTemplateCustomRendererInterface;
use Devsrealm\TonicsTemplateSystem\Node\Tag;
use Devsrealm\TonicsTemplateSystem\TonicsView;

class WordPressWPContentURLCustomRenderer implements TonicsTemplateCustomRendererInterface
{

    public function render (TonicsView $tonicsView): string
    {
        $modeOutput = '';
        /**@var Tag $tag */
        foreach ($tonicsView->getStackOfOpenTagEl() as $tag) {
            try {
                $mode = $tonicsView->getModeRendererHandler($tag->getTagName());
                if ($mode instanceof TonicsModeRendererInterface) {
                    $modeOutput .= $mode->render($tag->getContent(), $tag->getArgs());
                }
            } catch (TonicsTemplateRangeException) {
            }
        }
        $tonicsView->reset();
        return $modeOutput;
    }
}