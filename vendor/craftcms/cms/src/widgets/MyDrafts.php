<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\widgets;

use Craft;
use craft\base\Widget;
use craft\elements\Entry;
use craft\helpers\Cp;
use craft\helpers\Html;

/**
 * MyDrafts represents a My Drafts dashboard widget.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.6.5
 */
class MyDrafts extends Widget
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('app', 'My Drafts');
    }

    /**
     * @inheritdoc
     */
    protected static function allowMultipleInstances(): bool
    {
        return false;
    }

    /**
     * @var int The total number of drafts that the widget should show
     */
    public $limit = 10;

    /**
     * @inheritdoc
     */
    public static function icon()
    {
        return Craft::getAlias('@appicons/draft.svg');
    }

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['limit'], 'number', 'integerOnly' => true];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Cp::textFieldHtml([
            'label' => Craft::t('app', 'Limit'),
            'id' => 'limit',
            'name' => 'limit',
            'value' => $this->limit,
            'size' => 2,
            'errors' => $this->getErrors('limit'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getBodyHtml()
    {
        $drafts = Entry::find()
            ->drafts()
            ->anyStatus()
            ->draftCreator(Craft::$app->getUser()->getId())
            ->orderBy(['dateUpdated' => SORT_DESC])
            ->limit($this->limit)
            ->all();

        if (empty($drafts)) {
            return Html::tag('div', Craft::t('app', 'You don’t have any active drafts.'), [
                'class' => ['zilch', 'small'],
            ]);
        }

        $html = '';

        foreach ($drafts as $draft) {
            $html .= Html::tag('div', Cp::elementHtml($draft));
        }

        return $html;
    }
}
