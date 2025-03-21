<?php
/*
 *     Copyright (c) 2024. Olayemi Faruq <olayemi@tonics.app>
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

namespace Devsrealm\TonicsTemplateSystemExtensionBundle\Services;

use Devsrealm\TonicsTemplateSystem\AbstractClasses\TonicsTemplateViewAbstract;
use Devsrealm\TonicsTemplateSystem\Exceptions\TonicsTemplateRangeException;
use Devsrealm\TonicsTemplateSystem\Interfaces\TonicsModeRenderWithTagInterface;
use Devsrealm\TonicsTemplateSystem\Interfaces\TonicsTemplateCustomRendererInterface;
use Devsrealm\TonicsTemplateSystem\Loader\TonicsTemplateArrayLoader;
use Devsrealm\TonicsTemplateSystem\Node\Tag;
use Devsrealm\TonicsTemplateSystem\TonicsView;
use Devsrealm\TonicsTemplateSystemExtensionBundle\State\SimpleShortCode\TonicsSimpleShortCode;

class SimpleShortCodeService
{
    /**
     * @return TonicsTemplateCustomRendererInterface
     */
    public static function GlobalVariableShortCodeCustomRendererForAttributes(): TonicsTemplateCustomRendererInterface
    {
        return new class implements TonicsTemplateCustomRendererInterface {

            private array $args = [];

            public function render(TonicsView $tonicsView): string
            {
                /**@var Tag $tag */
                foreach ($tonicsView->getStackOfOpenTagEl() as $tag) {
                    try {
                        $mode = $tonicsView->getModeRendererHandler($tag->getTagName());
                        if ($mode instanceof TonicsModeRenderWithTagInterface) {
                            $tagName = strtolower($tag->getTagName());
                            if ($tagName === 'attributes') {
                                $this->args = [...$this->args, ...$tag->getArgs()];
                            }
                        }
                    } catch (TonicsTemplateRangeException|\Exception) {
                    }
                }
                return true;
            }

            public function getArgs(): array
            {
                return $this->args;
            }

        };
    }

    /**
     * @return TonicsModeRenderWithTagInterface|TonicsTemplateViewAbstract
     */
    public static function AttributesShortCode(): TonicsModeRenderWithTagInterface|TonicsTemplateViewAbstract
    {
        return new class extends TonicsTemplateViewAbstract implements TonicsModeRenderWithTagInterface {

            private array $args = [];

            public function render(string $content, array $args, Tag $tag): string
            {
                $this->args = $args;
                return '';
            }


            public function defaultArgs(): array
            {
                return [];
            }

            public function getArgs(): array
            {
                return $this->args;
            }

            public function setArgs(array $args): void
            {
                $this->args = $args;
            }
        };
    }

    /**
     * Extracts attributes from the given shortcode content.
     *
     * The content should be a string containing shortcode attributes in the format:
     * [attributes key1="value1", key2="value2", ...]
     *
     * @param string $content The shortcode content to extract attributes from.
     *
     * @return array An associative array of attributes.
     */
    public static function ElementsAttribute(string $content): array
    {
        $content = trim($content);
        $attributes = [];

        if (!empty($content)) {
            $render = SimpleShortCodeService::GlobalVariableShortCodeCustomRendererForAttributes();
            $shortCode = new TonicsSimpleShortCode([
                'render' => $render,
            ]);

            $shortCode->getView()->addModeHandler('attributes', SimpleShortCodeService::AttributesShortCode()::class);
            $shortCode->getView()->setTemplateLoader(new TonicsTemplateArrayLoader(['template' => "[attributes $content]"]))
                ->render('template');

            $attributes = $render->getArgs();
        }

        // Trim the trailing space and return the result
        return array_map(function ($value) {
            return array_map('trim', explode(',', $value));
        }, $attributes);
    }
}